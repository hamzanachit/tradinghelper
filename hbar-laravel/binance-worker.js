import WebSocket from 'ws';
import axios from 'axios';

const SYMBOL = process.argv[2] || 'hbarusdt';
const INTERVAL = process.argv[3] || '1m';
const LARAVEL_API = 'http://127.0.0.1:8000/api/internal/broadcast-candle';

const wsUrl = `wss://stream.binance.com:9443/ws/${SYMBOL.toLowerCase()}@kline_${INTERVAL}`;

console.log(`[NodeWorker] Connecting to ${wsUrl}`);

function connect() {
    const ws = new WebSocket(wsUrl);

    ws.on('open', () => {
        console.log('[NodeWorker] Connected to Binance');
    });

    ws.on('message', async (data) => {
        try {
            const json = JSON.parse(data);
            if (!json.k) return;

            const k = json.k;
            const candle = {
                time: Math.floor(k.t / 1000),
                o: parseFloat(k.o),
                h: parseFloat(k.h),
                l: parseFloat(k.l),
                c: parseFloat(k.c),
                v: parseFloat(k.v)
            };

            // Send to Laravel to broadcast
            await axios.post(LARAVEL_API, {
                candle: candle,
                symbol: SYMBOL.toUpperCase()
            });

            // console.log(`[NodeWorker] Sent candle: ${candle.c}`);
        } catch (e) {
            // console.error('[NodeWorker] Error processing message:', e.message);
        }
    });

    ws.on('close', () => {
        console.log('[NodeWorker] Connection closed. Reconnecting in 3s...');
        setTimeout(connect, 3000);
    });

    ws.on('error', (err) => {
        console.error('[NodeWorker] Error:', err.message);
    });
}

connect();
