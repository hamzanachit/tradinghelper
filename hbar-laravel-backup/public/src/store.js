import { create } from 'zustand';

export const useStore = create((set) => ({
  currentPrice: null,
  priceChange: 0,
  priceChangePercent: 0,
  signal: 'WAIT',
  signalClass: 'neutral',
  signalScore: 0,
  signalReasons: [],
  indicators: {
    rsi: null,
    ema9: null,
    ema20: null,
    ema50: null,
    sma20: null,
    sma50: null,
    bb: null,
    macd: null,
    macdSignal: null,
    macdHistogram: null,
    atr: null,
    volume: null,
    vwap: null,
  },
  timeframe: '1m',
  lastUpdate: null,
  connectionStatus: 'disconnected',
  
  setPriceData: (data) => set({
    currentPrice: data.candle?.close,
    priceChange: data.candle?.close - data.candle?.open || 0,
    priceChangePercent: data.candle?.open 
      ? ((data.candle.close - data.candle.open) / data.candle.open) * 100 
      : 0,
    signal: data.signal?.signal || 'WAIT',
    signalClass: data.signal?.signalClass || 'neutral',
    signalScore: data.signal?.score || 0,
    signalReasons: data.signal?.reasons || [],
    indicators: {
      rsi: data.indicators?.rsi,
      ema9: data.indicators?.ema9,
      ema20: data.indicators?.ema20,
      ema50: data.indicators?.ema50,
      sma20: data.indicators?.sma20,
      sma50: data.indicators?.sma50,
      bb: data.indicators?.bb,
      macd: data.indicators?.macd,
      macdSignal: data.indicators?.macdSignal,
      macdHistogram: data.indicators?.macdHistogram,
      atr: data.indicators?.atr,
      volume: data.indicators?.volume,
      vwap: data.indicators?.vwap,
    },
    timeframe: data.timeframe || '1m',
    lastUpdate: data.timestamp
  }),
  
  setConnectionStatus: (status) => set({ connectionStatus: status }),
}));
