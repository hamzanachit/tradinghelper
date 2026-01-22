import WebSocket from "ws";
import { RSI, EMA, MACD, SMA } from "technicalindicators";
import https from "https";

let closes = [];
let historicalCandles = {};
let currentTimeframe = "1m";
let wsConnections = {};
const MAX_HISTORY = 10000;

const TIMEFRAMES = {
  "1m": { interval: "1m", limit: 1000, maxBatches: 3 },
  "5m": { interval: "5m", limit: 1000, maxBatches: 10 },
  "15m": { interval: "15m", limit: 1000, maxBatches: 10 },
  "1h": { interval: "1h", limit: 1000, maxBatches: 10 },
  "4h": { interval: "4h", limit: 1000, maxBatches: 10 },
  "1d": { interval: "1d", limit: 1000, maxBatches: 10 }
};

function fetchHistoricalData(timeframe, startTime = null) {
  return new Promise((resolve, reject) => {
    const interval = TIMEFRAMES[timeframe].interval;
    const limit = TIMEFRAMES[timeframe].limit;
    
    let url = `https://api.binance.com/api/v3/klines?symbol=HBARUSDT&interval=${interval}&limit=${limit}`;
    if (startTime) {
      url += `&startTime=${startTime}`;
    }
    
    const timeout = setTimeout(() => {
      reject(new Error('Request timeout'));
    }, 15000);
    
    https.get(url, (res) => {
      clearTimeout(timeout);
      let data = '';
      
      res.on('data', (chunk) => {
        data += chunk;
      });
      
      res.on('end', () => {
        try {
          const candles = JSON.parse(data);
          if (!Array.isArray(candles)) {
            resolve([]);
            return;
          }
          const formattedCandles = candles.map(c => ({
            time: Math.floor(c[0] / 1000),
            open: parseFloat(c[1]),
            high: parseFloat(c[2]),
            low: parseFloat(c[3]),
            close: parseFloat(c[4]),
            volume: parseFloat(c[5]),
          }));
          resolve(formattedCandles);
        } catch (error) {
          reject(error);
        }
      });
    }).on('error', (error) => {
      clearTimeout(timeout);
      reject(error);
    });
  });
}

async function fetchAllHistoricalData(timeframe, socket = null) {
  console.log(`Fetching historical data for ${timeframe}...`);
  const allCandles = [];
  let hasMore = true;
  let startTime = null;
  let iterations = 0;
  const maxBatches = TIMEFRAMES[timeframe].maxBatches;

  while (hasMore && iterations < maxBatches) {
    iterations++;
    try {
      const candles = await fetchHistoricalData(timeframe, startTime);
      
      if (candles.length === 0) {
        hasMore = false;
        continue;
      }
      
      allCandles.push(...candles);
      
      if (candles.length < 1000) {
        hasMore = false;
      } else {
        const lastCandle = candles[candles.length - 1];
        startTime = lastCandle.time * 1000 + 60000;
      }
      
      console.log(`  Batch ${iterations}/${maxBatches}: ${candles.length} candles (total: ${allCandles.length})`);
      
      if (socket) {
        socket.emit("fetch-progress", {
          timeframe,
          iteration: iterations,
          maxBatches,
          total: allCandles.length
        });
      }
      
      await new Promise(resolve => setTimeout(resolve, 200));
    } catch (error) {
      console.error(`Error fetching batch ${iterations}:`, error.message);
      hasMore = false;
    }
  }

  const uniqueCandles = [];
  const seen = new Set();
  for (const candle of allCandles) {
    if (!seen.has(candle.time)) {
      seen.add(candle.time);
      uniqueCandles.push(candle);
    }
  }
  
  uniqueCandles.sort((a, b) => a.time - b.time);
  
  if (uniqueCandles.length > 0) {
    const oldest = new Date(uniqueCandles[0].time * 1000);
    const newest = new Date(uniqueCandles[uniqueCandles.length - 1].time * 1000);
    const days = Math.floor((newest - oldest) / (1000 * 60 * 60 * 24));
    console.log(`Total for ${timeframe}: ${uniqueCandles.length} candles (${days} days of data)`);
  } else {
    console.log(`No data available for ${timeframe}`);
  }
  
  return uniqueCandles;
}

export async function startPriceStream(io) {
  try {
    const historicalData = await fetchAllHistoricalData(currentTimeframe, io);
    historicalCandles[currentTimeframe] = historicalData;
    
    if (historicalData.length > 0) {
      io.emit("historical-data", historicalData);
    }
    
    connectBinanceWS(io, currentTimeframe);
  } catch (error) {
    console.error("Error fetching historical data:", error);
    connectBinanceWS(io, currentTimeframe);
  }
  return wsConnections[currentTimeframe];
}

function connectBinanceWS(io, timeframe) {
  const interval = TIMEFRAMES[timeframe].interval;
  
  if (wsConnections[timeframe]) {
    wsConnections[timeframe].close();
  }

  const ws = new WebSocket(
    `wss://stream.binance.com:9443/ws/hbarusdt@kline_${interval}`
  );

  wsConnections[timeframe] = ws;

  ws.on("open", () => {
    console.log(`Binance WebSocket connected for ${timeframe}`);
  });

  ws.on("message", (data) => {
    try {
      const json = JSON.parse(data);
      const k = json.k;

      const candle = {
        time: Math.floor(k.t / 1000),
        open: parseFloat(k.o),
        high: parseFloat(k.h),
        low: parseFloat(k.l),
        close: parseFloat(k.c),
        volume: parseFloat(k.v),
      };

      const tfData = historicalCandles[timeframe] || [];
      const lastCandle = tfData[tfData.length - 1];
      
      if (lastCandle && lastCandle.time === candle.time) {
        historicalCandles[timeframe] = [...tfData.slice(0, -1), candle];
      } else {
        const newData = [...tfData, candle];
        if (newData.length > MAX_HISTORY) {
          newData.shift();
        }
        historicalCandles[timeframe] = newData;
      }

      const closesArr = historicalCandles[timeframe].map(c => c.close);
      const indicators = calculateIndicators(closesArr, historicalCandles[timeframe]);
      const signal = generateSignal(indicators);

      const payload = {
        candle,
        indicators,
        signal,
        timeframe,
        timestamp: Date.now()
      };

      io.emit("realtime-update", payload);
    } catch (error) {
      console.error("Error processing candle:", error);
    }
  });

  ws.on("error", (error) => {
    console.error("WebSocket error:", error);
  });

  ws.on("close", () => {
    console.log(`Binance WebSocket disconnected for ${timeframe}`);
  });

  return ws;
}

function calculateIndicators(closesArr, candles) {
  const result = {
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
  };

  try {
    if (closesArr.length >= 14) {
      const rsiValues = RSI.calculate({ values: closesArr, period: 14 });
      if (rsiValues.length > 0) {
        result.rsi = parseFloat(rsiValues[rsiValues.length - 1].toFixed(2));
      }
    }

    if (closesArr.length >= 9) {
      const ema9Values = EMA.calculate({ values: closesArr, period: 9 });
      if (ema9Values.length > 0) {
        result.ema9 = parseFloat(ema9Values[ema9Values.length - 1].toFixed(4));
      }
    }

    if (closesArr.length >= 20) {
      const ema20Values = EMA.calculate({ values: closesArr, period: 20 });
      const sma20Values = SMA.calculate({ values: closesArr, period: 20 });
      
      if (ema20Values.length > 0) {
        result.ema20 = parseFloat(ema20Values[ema20Values.length - 1].toFixed(4));
      }
      if (sma20Values.length > 0) {
        result.sma20 = parseFloat(sma20Values[sma20Values.length - 1].toFixed(4));
      }
    }

    if (closesArr.length >= 50) {
      const ema50Values = EMA.calculate({ values: closesArr, period: 50 });
      const sma50Values = SMA.calculate({ values: closesArr, period: 50 });
      
      if (ema50Values.length > 0) {
        result.ema50 = parseFloat(ema50Values[ema50Values.length - 1].toFixed(4));
      }
      if (sma50Values.length > 0) {
        result.sma50 = parseFloat(sma50Values[sma50Values.length - 1].toFixed(4));
      }
    }

    if (closesArr.length >= 26) {
      const macdValues = MACD.calculate({
        values: closesArr,
        fastPeriod: 12,
        slowPeriod: 26,
        signalPeriod: 9,
        SimpleMAOscillator: false,
        SimpleMASignal: false
      });
      
      if (macdValues.length > 0) {
        const macd = macdValues[macdValues.length - 1];
        result.macd = parseFloat(macd.MACD.toFixed(4));
        result.macdSignal = parseFloat(macd.signal.toFixed(4));
        result.macdHistogram = parseFloat(macd.histogram.toFixed(4));
      }
    }

    if (closesArr.length >= 20) {
      const bbInput = closesArr.slice(-20);
      if (bbInput.length === 20) {
        const sma = bbInput.reduce((a, b) => a + b, 0) / 20;
        const stdDev = Math.sqrt(bbInput.reduce((sq, n) => sq + Math.pow(n - sma, 2), 0) / 20);
        result.bb = {
          upper: parseFloat((sma + stdDev * 2).toFixed(4)),
          middle: parseFloat(sma.toFixed(4)),
          lower: parseFloat((sma - stdDev * 2).toFixed(4)),
        };
      }
    }

    if (candles && candles.length >= 14) {
      const atrInput = candles.slice(-14).map((c, i, arr) => {
        if (i === 0) return c.high - c.low;
        return Math.max(
          Math.abs(c.high - arr[i-1].close),
          Math.abs(c.low - arr[i-1].close),
          c.high - c.low
        );
      });
      const atrValue = atrInput.reduce((a, b) => a + b, 0) / atrInput.length;
      result.atr = parseFloat(atrValue.toFixed(4));
    }

    if (candles && candles.length > 0) {
      result.volume = parseFloat(candles[candles.length - 1].volume.toFixed(2));
      
      if (candles.length >= 20) {
        const volSum = candles.slice(-20).reduce((a, b) => a + b.volume, 0);
        result.vwap = parseFloat((volSum / 20).toFixed(2));
      }
    }

  } catch (error) {
    console.error("Indicator calculation error:", error);
  }

  return result;
}

export function generateSignal(indicators) {
  const { rsi, macdHistogram, ema9, ema20, sma20, close } = indicators;

  if (rsi === null || macdHistogram === null) {
    return { signal: "WAIT", score: 0, reasons: ["Collecting data..."] };
  }

  let buyScore = 0;
  let sellScore = 0;
  const reasons = [];

  if (rsi < 30) {
    buyScore += 3;
    reasons.push("RSI Oversold (< 30)");
  } else if (rsi < 40) {
    buyScore += 1;
    reasons.push("RSI Near Oversold");
  } else if (rsi > 70) {
    sellScore += 3;
    reasons.push("RSI Overbought (> 70)");
  } else if (rsi > 60) {
    sellScore += 1;
    reasons.push("RSI Near Overbought");
  } else {
    reasons.push(`RSI Neutral (${rsi})`);
  }

  if (macdHistogram > 0) {
    buyScore += 2;
    reasons.push("MACD Bullish");
  } else if (macdHistogram < 0) {
    sellScore += 2;
    reasons.push("MACD Bearish");
  }

  if (ema9 !== null && ema20 !== null) {
    if (ema9 > ema20) {
      buyScore += 2;
      reasons.push("Golden Cross (EMA 9 > 20)");
    } else {
      sellScore += 2;
      reasons.push("Death Cross (EMA 9 < 20)");
    }
  }

  if (ema20 !== null && sma20 !== null) {
    if (ema20 > sma20) {
      buyScore += 1;
      reasons.push("EMA > SMA (Uptrend)");
    } else {
      sellScore += 1;
      reasons.push("EMA < SMA (Downtrend)");
    }
  }

  let finalSignal = "NEUTRAL";
  let signalClass = "neutral";
  if (buyScore >= 6) {
    finalSignal = "STRONG_BUY";
    signalClass = "strong-buy";
  } else if (buyScore >= 4) {
    finalSignal = "BUY";
    signalClass = "buy";
  } else if (sellScore >= 6) {
    finalSignal = "STRONG_SELL";
    signalClass = "strong-sell";
  } else if (sellScore >= 4) {
    finalSignal = "SELL";
    signalClass = "sell";
  }

  return {
    signal: finalSignal,
    signalClass,
    score: buyScore - sellScore,
    buyScore,
    sellScore,
    reasons
  };
}

export function getHistoricalData(timeframe = "1m") {
  const candles = historicalCandles[timeframe] || [];
  return candles;
}

export function changeTimeframe(tf, io) {
  if (TIMEFRAMES[tf] && tf !== currentTimeframe) {
    currentTimeframe = tf;
    
    if (historicalCandles[tf] && historicalCandles[tf].length > 0) {
      io.emit("historical-data", historicalCandles[tf]);
      connectBinanceWS(io, tf);
      return true;
    }
    
    fetchAllHistoricalData(tf, io).then(data => {
      historicalCandles[tf] = data;
      io.emit("historical-data", data);
      connectBinanceWS(io, tf);
    }).catch(err => {
      console.error("Error fetching historical data:", err);
      connectBinanceWS(io, tf);
    });
    
    return true;
  }
  return false;
}

export function getTimeframe() {
  return currentTimeframe;
}
