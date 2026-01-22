import { useStore } from '../store';

export default function IndicatorPanel() {
  const { indicators, currentPrice } = useStore();
  const { rsi, ema9, ema20, ema50, sma20, sma50, bb, macd, macdSignal, macdHistogram, atr, volume, vwap } = indicators;

  const formatValue = (value) => {
    if (value === null || value === undefined) return '---';
    return value.toFixed(4);
  };

  const formatPrice = (value) => {
    if (value === null || value === undefined) return '---';
    return value.toFixed(4);
  };

  const getRSIColor = (rsiValue) => {
    if (rsiValue === null) return 'text-gray-400';
    if (rsiValue < 30) return 'text-bullish';
    if (rsiValue > 70) return 'text-bearish';
    return 'text-yellow-400';
  };

  const getMACDColor = (hist) => {
    if (hist === null) return 'text-gray-400';
    if (hist > 0) return 'text-bullish';
    if (hist < 0) return 'text-bearish';
    return 'text-gray-400';
  };

  const getBBPosition = () => {
    if (!bb || !currentPrice) return null;
    if (currentPrice < bb.lower) return { text: 'Oversold', color: 'text-bullish' };
    if (currentPrice > bb.upper) return { text: 'Overbought', color: 'text-bearish' };
    return { text: 'Middle', color: 'text-yellow-400' };
  };

  const bbPos = getBBPosition();

  return (
    <div className="bg-[#111] rounded-lg border border-[#222] p-4">
      <h3 className="text-lg font-semibold text-white mb-4">Technical Indicators</h3>
      
      <div className="space-y-3">
        <div className="indicator-row p-3 bg-[#0a0a0a] rounded-lg border border-[#222]">
          <div className="flex justify-between items-center mb-2">
            <span className="text-gray-400 text-sm">RSI (14)</span>
            <span className={`text-lg font-mono font-semibold ${getRSIColor(rsi)}`}>
              {formatPrice(rsi)}
            </span>
          </div>
          <div className="h-1.5 bg-[#222] rounded-full overflow-hidden">
            <div 
              className={`h-full ${rsi < 30 ? 'bg-bullish' : rsi > 70 ? 'bg-bearish' : rsi < 50 ? 'bg-yellow-500' : 'bg-green-500'}`}
              style={{ width: `${Math.min(Math.max(rsi || 50, 0), 100)}%` }}
            />
          </div>
          <div className="flex justify-between text-xs text-gray-600 mt-1">
            <span>0</span>
            <span>30</span>
            <span>50</span>
            <span>70</span>
            <span>100</span>
          </div>
        </div>

        <div className="p-3 bg-[#0a0a0a] rounded-lg border border-[#222]">
          <div className="text-gray-400 text-sm mb-2">Moving Averages</div>
          <div className="space-y-2 text-sm">
            <div className="flex justify-between">
              <span className="text-yellow-400">EMA 9</span>
              <span className="font-mono text-white">{formatValue(ema9)}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-[#42a5f5]">EMA 20</span>
              <span className="font-mono text-white">{formatValue(ema20)}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-[#ef5350]">EMA 50</span>
              <span className="font-mono text-white">{formatValue(ema50)}</span>
            </div>
            <div className="border-t border-[#222] my-2 pt-2">
              <div className="flex justify-between">
                <span className="text-purple-400">SMA 20</span>
                <span className="font-mono text-white">{formatValue(sma20)}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-orange-400">SMA 50</span>
                <span className="font-mono text-white">{formatValue(sma50)}</span>
              </div>
            </div>
          </div>
        </div>

        <div className="p-3 bg-[#0a0a0a] rounded-lg border border-[#222]">
          <div className="text-gray-400 text-sm mb-2">Bollinger Bands</div>
          {bb ? (
            <div className="space-y-2 text-sm">
              <div className="flex justify-between">
                <span className="text-purple-400">Upper</span>
                <span className="font-mono text-white">{formatValue(bb.upper)}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-gray-400">Middle</span>
                <span className="font-mono text-white">{formatValue(bb.middle)}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-purple-400">Lower</span>
                <span className="font-mono text-white">{formatValue(bb.lower)}</span>
              </div>
              <div className="flex justify-between pt-2 border-t border-[#222]">
                <span className="text-gray-400">Position</span>
                <span className={`font-mono ${bbPos?.color || 'text-gray-400'}`}>
                  {bbPos?.text || '---'}
                </span>
              </div>
            </div>
          ) : (
            <span className="text-gray-500">Loading...</span>
          )}
        </div>

        <div className="p-3 bg-[#0a0a0a] rounded-lg border border-[#222]">
          <div className="text-gray-400 text-sm mb-2">MACD (12, 26, 9)</div>
          <div className="grid grid-cols-3 gap-2 text-sm">
            <div>
              <span className="text-gray-500 text-xs">MACD</span>
              <p className="font-mono text-[#26a69a]">{formatValue(macd)}</p>
            </div>
            <div>
              <span className="text-gray-500 text-xs">Signal</span>
              <p className="font-mono text-[#ef5350]">{formatValue(macdSignal)}</p>
            </div>
            <div>
              <span className="text-gray-500 text-xs">Hist</span>
              <p className={`font-mono font-semibold ${getMACDColor(macdHistogram)}`}>
                {formatValue(macdHistogram)}
              </p>
            </div>
          </div>
          <div className="mt-2 h-1.5 bg-[#222] rounded-full overflow-hidden">
            <div 
              className={`h-full ${macdHistogram > 0 ? 'bg-bullish' : 'bg-bearish'}`}
              style={{ width: `${Math.min(Math.abs(macdHistogram || 0) * 100, 100)}%` }}
            />
          </div>
        </div>

        <div className="p-3 bg-[#0a0a0a] rounded-lg border border-[#222]">
          <div className="text-gray-400 text-sm mb-2">Volatility & Volume</div>
          <div className="space-y-2 text-sm">
            <div className="flex justify-between">
              <span className="text-cyan-400">ATR (14)</span>
              <span className="font-mono text-white">{formatValue(atr)}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-gray-400">Volume</span>
              <span className="font-mono text-white">{volume ? volume.toFixed(0) : '---'}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-gray-400">Vol SMA (20)</span>
              <span className="font-mono text-white">{vwap ? vwap.toFixed(0) : '---'}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
