import { useEffect, useRef, useState } from 'react';
import { createChart, ColorType } from 'lightweight-charts';
import { io } from 'socket.io-client';

export default function Chart() {
  const chartContainerRef = useRef(null);
  const chartRef = useRef(null);
  const candleSeriesRef = useRef(null);
  const socketRef = useRef(null);
  const initializedRef = useRef(false);
  const [isConnected, setIsConnected] = useState(false);
  const [currentTimeframe, setCurrentTimeframe] = useState("1m");
  const [isLoading, setIsLoading] = useState(true);
  const [candleCount, setCandleCount] = useState(0);
  const [error, setError] = useState(null);

  useEffect(() => {
    if (initializedRef.current) return;
    initializedRef.current = true;

    console.log('Chart component initializing...');

    if (!chartContainerRef.current) {
      console.log('Chart container not found');
      return;
    }

    console.log('Creating chart...');

    const chart = createChart(chartContainerRef.current, {
      layout: {
        background: { type: ColorType.Solid, color: '#0a0a0a' },
        textColor: '#888',
      },
      grid: {
        vertLines: { color: '#1a1a1a' },
        horzLines: { color: '#1a1a1a' },
      },
      crosshair: {
        mode: 1,
        vertLine: { color: '#444', width: 1, style: 2 },
        horzLine: { color: '#444', width: 1, style: 2 },
      },
      rightPriceScale: { 
        borderColor: '#222',
        scaleMargins: { top: 0.1, bottom: 0.1 },
      },
      timeScale: {
        borderColor: '#222',
        timeVisible: true,
        secondsVisible: false,
      },
      height: 600,
    });

    chartRef.current = chart;

    const candleSeries = chart.addCandlestickSeries({
      upColor: '#26a69a',
      downColor: '#ef5350',
      borderUpColor: '#26a69a',
      borderDownColor: '#ef5350',
      wickUpColor: '#26a69a',
      wickDownColor: '#ef5350',
    });
    candleSeriesRef.current = candleSeries;

    const handleResize = () => {
      if (chartContainerRef.current) {
        chart.applyOptions({ width: chartContainerRef.current.clientWidth });
      }
    };

    handleResize();
    window.addEventListener('resize', handleResize);

    console.log('Connecting to WebSocket...');

    try {
      socketRef.current = io('http://localhost:3001', {
        transports: ['polling', 'websocket'],
        reconnection: true,
        reconnectionAttempts: 5,
        reconnectionDelay: 1000,
        timeout: 10000,
      });

      const socket = socketRef.current;

      socket.on('connect', () => {
        console.log('WebSocket connected successfully');
        setIsConnected(true);
        setError(null);
      });

      socket.on('disconnect', (reason) => {
        console.log('WebSocket disconnected:', reason);
        setIsConnected(false);
      });

      socket.on('connect_error', (err) => {
        console.error('WebSocket connection error:', err.message);
        setError(err.message);
        setIsConnected(false);
      });

      socket.on('error', (err) => {
        console.error('Socket error:', err);
      });

      socket.on('historical-data', (data) => {
        console.log('Received historical data:', data.length, 'candles');
        
        if (Array.isArray(data) && data.length > 0) {
          const candles = data.map(c => ({
            time: c.time,
            open: c.open,
            high: c.high,
            low: c.low,
            close: c.close,
          }));
          candleSeriesRef.current.setData(candles);
          setCandleCount(data.length);
          setIsLoading(false);
          
          if (data[0] && data[data.length - 1]) {
            const oldest = new Date(data[0].time * 1000);
            const newest = new Date(data[data.length - 1].time * 1000);
            console.log(`Date range: ${oldest.toLocaleDateString()} - ${newest.toLocaleDateString()}`);
          }
        } else {
          console.log('Empty data received');
          setIsLoading(false);
        }
      });

      socket.on('realtime-update', (data) => {
        if (candleSeriesRef.current && data.candle) {
          const candle = {
            time: data.candle.time,
            open: data.candle.open,
            high: data.candle.high,
            low: data.candle.low,
            close: data.candle.close,
          };
          candleSeriesRef.current.update(candle);
        }
      });

      socket.on('timeframe-changed', (data) => {
        console.log('Timeframe changed to:', data.timeframe);
        setCurrentTimeframe(data.timeframe);
        setIsLoading(true);
        setCandleCount(0);
      });

    } catch (err) {
      console.error('Error creating socket:', err);
      setError(err.message);
    }

    return () => {
      console.log('Chart cleanup...');
      window.removeEventListener('resize', handleResize);
      if (socketRef.current) {
        socketRef.current.disconnect();
        socketRef.current = null;
      }
      if (chartRef.current) {
        chartRef.current.remove();
        chartRef.current = null;
      }
    };
  }, []);

  const handleTimeframeChange = (tf) => {
    if (socketRef.current && tf !== currentTimeframe) {
      socketRef.current.emit("change-timeframe", tf);
      setCurrentTimeframe(tf);
    }
  };

  return (
    <div className="relative">
      <div className="absolute top-4 left-4 z-10 flex flex-col gap-2">
        <div className="flex items-center gap-2">
          <h2 className="text-2xl font-bold text-white">HBAR/USDT</h2>
          <span className={`w-3 h-3 rounded-full ${isConnected ? 'bg-green-500 animate-pulse' : 'bg-red-500'}`}></span>
        </div>
        
        <div className="flex gap-1 bg-[#111] p-1 rounded-lg">
          {['1m', '5m', '15m', '1h', '4h', '1d'].map((tf) => (
            <button
              key={tf}
              onClick={() => handleTimeframeChange(tf)}
              className={`px-2 py-1 text-xs rounded transition-colors ${
                currentTimeframe === tf
                  ? 'bg-[#26a69a] text-white'
                  : 'text-gray-400 hover:text-white hover:bg-[#222]'
              }`}
            >
              {tf}
            </button>
          ))}
        </div>
        
        {candleCount > 0 && (
          <div className="text-xs text-gray-500">
            <span className="text-white font-mono">{candleCount.toLocaleString()}</span> candles
          </div>
        )}
      </div>
      
      {isLoading && (
        <div className="absolute inset-0 z-10 flex items-center justify-center bg-black bg-opacity-70">
          <div className="text-center text-gray-400">
            <div className="animate-spin w-10 h-10 border-3 border-[#26a69a] border-t-transparent rounded-full mx-auto mb-4"></div>
            <p className="text-lg">Loading historical data...</p>
            <p className="text-sm mt-2 text-gray-500">
              {currentTimeframe} timeframe
            </p>
            {error && (
              <p className="text-xs mt-2 text-red-500">{error}</p>
            )}
          </div>
        </div>
      )}
      
      <div ref={chartContainerRef} className="w-full" style={{ height: '600px' }} />
    </div>
  );
}
