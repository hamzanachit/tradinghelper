import Chart from './components/Chart';
import IndicatorPanel from './components/IndicatorPanel';
import SignalPanel from './components/SignalPanel';
import Header from './components/Header';
import { useStore } from './store';

function App() {
  const { connectionStatus } = useStore();

  return (
    <div className="min-h-screen bg-[#0a0a0a] text-white">
      <Header />
      
      <main className="container mx-auto px-4 py-6">
        <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
          <div className="lg:col-span-3">
            <div className="bg-[#111] rounded-lg border border-[#222] overflow-hidden">
              <Chart />
            </div>
          </div>
          
          <div className="space-y-6">
            <SignalPanel />
            <IndicatorPanel />
          </div>
        </div>
      </main>
      
      <footer className="border-t border-[#222] mt-8 py-4">
        <div className="container mx-auto px-4 text-center text-gray-500 text-sm">
          <span className={`inline-block w-2 h-2 rounded-full mr-2 ${
            connectionStatus === 'connected' ? 'bg-green-500' : 'bg-red-500'
          }`}></span>
          {connectionStatus === 'connected' ? 'Live' : 'Disconnected'}
          <span className="ml-4">HBAR/USDT</span>
          <span className="ml-4">1M Timeframe</span>
        </div>
      </footer>
    </div>
  );
}

export default App;
