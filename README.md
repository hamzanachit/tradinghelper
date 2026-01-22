# HBAR/USDT Real-Time Trading Application

A full-stack real-time trading application that streams live HBAR/USDT prices, displays TradingView-style charts, calculates technical indicators (RSI, EMA, MACD), and generates trading signals.

## 🚀 Features

- **Real-time Price Streaming**: Live HBAR/USDT data from Binance WebSocket
- **TradingView Charts**: Interactive candlestick charts using Lightweight Charts
- **Technical Indicators**: RSI, EMA (20 & 50), MACD with signal line and histogram
- **Trading Signals**: Buy / Sell / Neutral signals based on indicator confluence
- **Responsive Design**: Dark mode optimized UI with Tailwind CSS

## 🛠 Tech Stack

### Frontend
- React 18 + Vite
- TradingView Lightweight Charts
- Tailwind CSS
- Zustand (state management)
- Socket.IO Client

### Backend
- Node.js + Express
- Socket.IO (WebSocket server)
- Binance WebSocket stream
- Technical Indicators library

## 📦 Installation

### Prerequisites
- Node.js 18+ 
- npm or yarn

### Setup

1. **Clone and install root dependencies**:
```bash
npm install
```

2. **Install backend dependencies**:
```bash
cd backend
npm install
cd ..
```

3. **Install frontend dependencies**:
```bash
cd frontend
npm install
cd ..
```

## 🏃 Running the Application

### Development Mode (runs both frontend and backend)

```bash
npm run dev
```

This will start:
- Backend server on `http://localhost:3001`
- Frontend dev server on `http://localhost:5173`

### Manual Start

**Terminal 1 - Backend:**
```bash
cd backend
npm run dev
```

**Terminal 2 - Frontend:**
```bash
cd frontend
npm run dev
```

### Production Build

```bash
npm run build
```

## 📡 API Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/health` | GET | Health check |
| `/api/historical` | GET | Get historical candle data |

## 🔌 WebSocket Events

### Client → Server
- `connection` - Automatic on connect

### Server → Client
- `historical-data` - Initial historical candles
- `realtime-update` - Real-time candle with indicators and signal

## 📊 Signal Logic

**BUY Signal** (score ≥ 3):
- RSI < 30 (+2 points) or RSI < 40 (+1 point)
- MACD histogram positive and increasing (+2 points)
- EMA20 > EMA50 (+1 point)

**SELL Signal** (score ≥ 3):
- RSI > 70 (+2 points) or RSI > 60 (+1 point)
- MACD histogram negative and decreasing (+2 points)
- EMA20 < EMA50 (+1 point)

**NEUTRAL**: Score < 3
**WAIT**: Insufficient data for calculation

## 🎨 Customization

### Adding New Indicators

Edit `backend/indicators.js`:

```javascript
// Add new indicator calculation
export function calculateIndicators(close) {
  // ... existing code ...
  
  // Add your indicator
  if (closes.length >= PERIOD) {
    const values = YOUR_INDICATOR.calculate({
      values: closes,
      period: PERIOD
    });
    result.yourIndicator = values[values.length - 1];
  }
  
  return result;
}
```

### Changing Trading Pair

Edit `backend/indicators.js`:
```javascript
const ws = new WebSocket(
  "wss://stream.binance.com:9443/ws/YOURPAIR@kline_1m"
);
```

Edit `frontend/src/App.jsx`:
```jsx
<h1 className="text-xl font-bold text-white">YOURPAIR/USDT</h1>
```

## 🐳 Docker (Optional)

```dockerfile
# Dockerfile.backend
FROM node:18-alpine
WORKDIR /app
COPY package*.json ./
RUN npm ci --only=production
COPY . .
EXPOSE 3001
CMD ["npm", "start"]
```

```dockerfile
# Dockerfile.frontend
FROM node:18-alpine AS builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM nginx:alpine
COPY --from=builder /app/dist /usr/share/nginx/html
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
```

## ⚠️ Disclaimer

This application is for educational and demonstration purposes only. The trading signals generated are based on technical analysis and should not be considered financial advice. Always conduct your own research and implement proper risk management before making trading decisions.

## 📄 License

MIT License
