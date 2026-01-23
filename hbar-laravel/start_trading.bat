@echo off
echo Starting HBAR Trading Platform...

REM Start Laravel Reverb (WebSocket Server)
start "Reverb WebSocket Server" php artisan reverb:start

REM Start Binance Worker (Fetches live data)
start "Binance Data Stream" node binance-worker.js HBARUSDT 1m

REM Start Laravel Development Server
start "Laravel Web Server" php artisan serve

echo.
echo All services started!
echo ----------------------------------------
echo 1. Web Server: http://127.0.0.1:8000/app
echo 2. WebSocket:  ws://localhost:8080
echo 3. Data Stream: Connection to Binance (Node.js)
echo ----------------------------------------
echo.
echo You can now open the app in your browser.
echo Press any key to exit this launcher (services will keep running)...
pause >nul
