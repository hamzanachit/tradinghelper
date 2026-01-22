import { useStore } from '../store';

export default function Header() {
  const { currentPrice, priceChange, priceChangePercent, signal, signalClass, lastUpdate, timeframe } = useStore();

  const formatPrice = (price) => {
    if (!price) return '---';
    return `$${price.toFixed(4)}`;
  };

  const formatChange = (change, percent) => {
    if (change === undefined || percent === undefined) return '---';
    const isPositive = change >= 0;
    const colorClass = isPositive ? 'text-bullish' : 'text-bearish';
    return (
      <span className={colorClass}>
        {isPositive ? '+' : ''}{change.toFixed(4)} ({isPositive ? '+' : ''}{percent.toFixed(2)}%)
      </span>
    );
  };

  const getSignalBadgeStyle = (cls) => {
    switch (cls) {
      case 'strong-buy': return 'bg-green-600';
      case 'buy': return 'bg-signal-buy';
      case 'strong-sell': return 'bg-red-700';
      case 'sell': return 'bg-signal-sell';
      case 'neutral': return 'bg-signal-neutral';
      default: return 'bg-gray-500';
    }
  };

  return (
    <header className="bg-[#111] border-b border-[#222]">
      <div className="container mx-auto px-4 py-3">
        <div className="flex flex-wrap items-center justify-between gap-4">
          <div className="flex items-center gap-4">
            <div>
              <h1 className="text-xl font-bold text-white flex items-center gap-2">
                HBAR/USDT
                <span className="text-xs bg-[#222] text-gray-400 px-2 py-0.5 rounded uppercase">
                  {timeframe}
                </span>
              </h1>
              <div className="flex items-center gap-3 mt-1">
                <span className="text-2xl font-mono font-semibold text-white">
                  {formatPrice(currentPrice)}
                </span>
                <span className="text-sm">
                  {formatChange(priceChange, priceChangePercent)}
                </span>
              </div>
            </div>
          </div>

          <div className="flex items-center gap-4">
            <div className={`px-4 py-2 rounded-lg font-bold text-white signal-badge ${getSignalBadgeStyle(signalClass)}`}>
              {signal.replace('_', ' ')}
            </div>
            <div className="text-right text-xs text-gray-500">
              {lastUpdate && (
                <>
                  <div>Updated: {new Date(lastUpdate).toLocaleTimeString()}</div>
                  <div>{new Date(lastUpdate).toLocaleDateString()}</div>
                </>
              )}
            </div>
          </div>
        </div>
      </div>
    </header>
  );
}
