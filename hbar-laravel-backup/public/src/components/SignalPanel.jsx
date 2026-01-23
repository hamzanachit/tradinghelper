import { useStore } from '../store';

export default function SignalPanel() {
  const { signal, signalClass, signalScore, signalReasons, currentPrice, indicators, timeframe } = useStore();
  const { rsi, ema9, ema20, bb } = indicators;

  const getSignalConfig = (sig, cls) => {
    switch (sig) {
      case 'STRONG_BUY':
        return {
          icon: '🚀',
          color: 'bg-green-600',
          borderColor: 'border-green-500',
          textColor: 'text-green-400',
          bgColor: 'bg-green-500',
          description: 'Strong Buy Signal'
        };
      case 'BUY':
        return {
          icon: '↑',
          color: 'bg-signal-buy',
          borderColor: 'border-signal-buy',
          textColor: 'text-signal-buy',
          bgColor: 'bg-signal-buy',
          description: 'Buy Signal'
        };
      case 'STRONG_SELL':
        return {
          icon: '🔻',
          color: 'bg-red-700',
          borderColor: 'border-red-600',
          textColor: 'text-red-400',
          bgColor: 'bg-red-600',
          description: 'Strong Sell Signal'
        };
      case 'SELL':
        return {
          icon: '↓',
          color: 'bg-signal-sell',
          borderColor: 'border-signal-sell',
          textColor: 'text-signal-sell',
          bgColor: 'bg-signal-sell',
          description: 'Sell Signal'
        };
      case 'NEUTRAL':
        return {
          icon: '→',
          color: 'bg-signal-neutral',
          borderColor: 'border-signal-neutral',
          textColor: 'text-signal-neutral',
          bgColor: 'bg-signal-neutral',
          description: 'Neutral - Wait'
        };
      default:
        return {
          icon: '⏳',
          color: 'bg-gray-500',
          borderColor: 'border-gray-500',
          textColor: 'text-gray-400',
          bgColor: 'bg-gray-500',
          description: 'Collecting Data...'
        };
    }
  };

  const config = getSignalConfig(signal, signalClass);

  return (
    <div className="bg-[#111] rounded-lg border border-[#222] p-4">
      <div className="flex justify-between items-center mb-4">
        <h3 className="text-lg font-semibold text-white">Trading Signal</h3>
        <span className="text-xs text-gray-500 uppercase">{timeframe}</span>
      </div>
      
      <div className={`p-4 rounded-lg border-2 ${config.borderColor} ${config.color} bg-opacity-10 text-center mb-4`}>
        <div className={`text-4xl mb-2 ${config.textColor}`}>
          {config.icon}
        </div>
        <div className={`text-2xl font-bold ${config.textColor}`}>
          {signal.replace('_', ' ')}
        </div>
        <div className="text-gray-400 text-sm mt-1">
          {config.description}
        </div>
        {signalScore !== 0 && (
          <div className="text-xs mt-2">
            Score: <span className={signalScore > 0 ? 'text-bullish' : 'text-bearish'}>
              {signalScore > 0 ? '+' : ''}{signalScore}
            </span>
          </div>
        )}
      </div>

      {signalReasons.length > 0 && (
        <div className="mb-4 p-3 bg-[#0a0a0a] rounded-lg">
          <div className="text-xs text-gray-500 mb-2 uppercase">Signal Reasons</div>
          <ul className="space-y-1">
            {signalReasons.map((reason, idx) => (
              <li key={idx} className="text-xs text-gray-300 flex items-center gap-2">
                <span className={`w-1.5 h-1.5 rounded-full ${
                  reason.includes('Bull') || reason.includes('Oversold') || reason.includes('Buy') || reason.includes('Uptrend') || reason.includes('>')
                    ? 'bg-bullish'
                    : reason.includes('Bear') || reason.includes('Overbought') || reason.includes('Sell') || reason.includes('Downtrend') || reason.includes('<')
                    ? 'bg-bearish'
                    : 'bg-gray-500'
                }`}></span>
                {reason}
              </li>
            ))}
          </ul>
        </div>
      )}

      <div className="space-y-2 text-sm">
        <div className="flex justify-between items-center p-2 bg-[#0a0a0a] rounded">
          <span className="text-gray-400">Price</span>
          <span className="font-mono text-white text-lg">
            ${currentPrice ? currentPrice.toFixed(4) : '---'}
          </span>
        </div>
        
        <div className="flex justify-between items-center p-2 bg-[#0a0a0a] rounded">
          <span className="text-gray-400">RSI (14)</span>
          <span className={
            rsi < 30 ? 'text-bullish font-semibold' : rsi > 70 ? 'text-bearish font-semibold' : 'text-gray-400'
          }>
            {rsi ? rsi.toFixed(2) : '---'}
            {rsi && rsi < 30 && ' 📗'}
            {rsi && rsi > 70 && ' 📕'}
          </span>
        </div>
        
        <div className="flex justify-between items-center p-2 bg-[#0a0a0a] rounded">
          <span className="text-gray-400">EMA Trend</span>
          <span className={
            ema9 > ema20 ? 'text-bullish' : ema9 < ema20 ? 'text-bearish' : 'text-gray-400'
          }>
            {ema9 > ema20 ? '↑ Bullish' : ema9 < ema20 ? '↓ Bearish' : '---'}
          </span>
        </div>

        <div className="flex justify-between items-center p-2 bg-[#0a0a0a] rounded">
          <span className="text-gray-400">BB Position</span>
          <span className={
            bb && currentPrice < bb.lower ? 'text-bullish' : 
            bb && currentPrice > bb.upper ? 'text-bearish' : 'text-gray-400'
          }>
            {bb && currentPrice < bb.lower ? '📗 Oversold' : 
             bb && currentPrice > bb.upper ? '📕 Overbought' : 
             bb ? 'Middle' : '---'}
          </span>
        </div>
      </div>

      <div className="mt-4 p-3 bg-[#0a0a0a] rounded-lg text-xs">
        <div className="flex items-center gap-2 mb-2">
          <div className="flex-1 h-2 bg-[#222] rounded-full overflow-hidden">
            <div 
              className={`h-full ${signalScore >= 4 ? 'bg-bullish' : signalScore <= -4 ? 'bg-bearish' : 'bg-yellow-500'}`}
              style={{ width: `${Math.min(Math.abs(signalScore) * 12.5, 100)}%` }}
            />
          </div>
        </div>
        <div className="flex justify-between text-gray-600">
          <span>Strong Sell</span>
          <span>Neutral</span>
          <span>Strong Buy</span>
        </div>
      </div>

      <div className="mt-4 p-3 bg-[#0a0a0a] rounded-lg text-xs text-gray-500">
        <p className="font-semibold text-gray-400 mb-1">⚠️ Disclaimer</p>
        <p>Signals are generated using technical indicators. This is NOT financial advice. Always do your own research and implement proper risk management.</p>
      </div>
    </div>
  );
}
