const express = require("express");
const { Server } = require("socket.io");
const http = require("http");
const WebSocket = require("ws");
const https = require("https");

const app = express();
const server = http.createServer(app);

const io = new Server(server, {
  cors: { origin: "*" }
});

app.get("/", (req, res) => {
  res.sendFile("C:/wamp64/www/hbar/frontend/index.html");
});

app.get("/api/health", (req, res) => {
  res.json({ status: "ok", time: new Date().toISOString() });
});

app.get("/api/candles", (req, res) => {
  const timeframe = req.query.timeframe || "1h";
  const limit = parseInt(req.query.limit) || 1000;
  
  const url = `https://api.binance.com/api/v3/klines?symbol=HBARUSDT&interval=${timeframe}&limit=${limit}`;
  
  https.get(url, (response) => {
    let data = "";
    response.on("data", chunk => data += chunk);
    response.on("end", () => {
      try {
        const candles = JSON.parse(data);
        const formatted = candles.map(c => ({
          time: Math.floor(c[0] / 1000),
          o: parseFloat(c[1]),
          h: parseFloat(c[2]),
          l: parseFloat(c[3]),
          c: parseFloat(c[4]),
          v: parseFloat(c[5])
        }));
        res.json(formatted);
      } catch (e) {
        res.json([]);
      }
    });
  }).on("error", () => res.json([]));
});

let clients = 0;

io.on("connection", (socket) => {
  clients++;
  console.log(`Client ${clients} connected`);
  
  socket.on("disconnect", () => {
    console.log("Client disconnected");
  });
});

const BINANCE_WS = "wss://stream.binance.com:9443/ws/hbarusdt@kline_1m";

function startBinance() {
  console.log("Connecting to Binance...");
  const binance = new WebSocket(BINANCE_WS);
  
  binance.onopen = function() {
    console.log("Binance WS connected");
  };
  
  binance.onmessage = function(event) {
    try {
      const json = JSON.parse(event.data);
      const k = json.k;
      
      const candle = {
        time: Math.floor(k.t / 1000),
        o: parseFloat(k.o),
        h: parseFloat(k.h),
        l: parseFloat(k.l),
        c: parseFloat(k.c),
      };
      
      io.emit("candle", candle);
    } catch (e) {
      console.error("Error:", e.message);
    }
  };

  binance.onclose = function() {
    console.log("Binance disconnected, reconnecting...");
    setTimeout(startBinance, 3000);
  };

  binance.onerror = function(err) {
    console.error("Binance error:", err.message || err);
  };
}

startBinance();

const PORT = 3001;
server.listen(PORT, "0.0.0.0", () => {
  console.log(`Server: http://localhost:${PORT}`);
  console.log(`API: http://localhost:${PORT}/api/candles?timeframe=1h`);
});
