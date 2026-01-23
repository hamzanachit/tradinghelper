<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HBAR Trading Platform</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    
    :root {
      --primary: #26a69a;
      --primary-dark: #1e8e83;
      --secondary: #2962ff;
      --dark: #131722;
      --dark-secondary: #1e222d;
      --text: #d1d4dc;
      --text-muted: #787b86;
    }
    
    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: var(--dark);
      color: var(--text);
      line-height: 1.6;
    }
    
    .page { display: none; }
    .page.active { display: block; }
    
    .hero {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      padding: 120px 20px 60px;
      background: linear-gradient(135deg, var(--dark) 0%, #0d1117 100%);
    }
    
    .hero h1 {
      font-size: 64px;
      margin-bottom: 20px;
      background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .hero p {
      font-size: 24px;
      color: var(--text-muted);
      max-width: 600px;
      margin-bottom: 40px;
    }
    
    .hero-buttons { display: flex; gap: 20px; }
    
    .btn {
      padding: 16px 40px;
      border-radius: 12px;
      font-size: 18px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s;
      cursor: pointer;
      border: none;
    }
    
    .btn-primary {
      background: var(--primary);
      color: #fff;
    }
    
    .btn-primary:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
    }
    
    .btn-secondary {
      background: transparent;
      color: var(--text);
      border: 2px solid var(--text-muted);
    }
    
    .btn-secondary:hover {
      border-color: var(--primary);
      color: var(--primary);
    }
    
    .btn-outline {
      background: transparent;
      color: var(--primary);
      border: 2px solid var(--primary);
    }
    
    .btn-outline:hover {
      background: var(--primary);
      color: #fff;
    }
    
    .features {
      padding: 100px 20px;
      background: var(--dark-secondary);
    }
    
    .section-title {
      text-align: center;
      font-size: 48px;
      margin-bottom: 60px;
    }
    
    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 40px;
      max-width: 1200px;
      margin: 0 auto;
    }
    
    .feature-card {
      background: var(--dark);
      padding: 40px;
      border-radius: 16px;
      text-align: center;
      transition: transform 0.3s;
    }
    
    .feature-card:hover { transform: translateY(-10px); }
    
    .feature-icon { font-size: 48px; margin-bottom: 20px; }
    
    .feature-card h3 {
      font-size: 24px;
      margin-bottom: 16px;
      color: var(--primary);
    }
    
    .feature-card p { color: var(--text-muted); }
    
    .stats-section {
      padding: 100px 20px;
      background: var(--dark);
    }
    
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 40px;
      max-width: 1000px;
      margin: 0 auto;
      text-align: center;
    }
    
    .stat-number { font-size: 56px; font-weight: 700; color: var(--primary); }
    .stat-label { font-size: 18px; color: var(--text-muted); }
    
    footer {
      padding: 40px 20px;
      text-align: center;
      color: var(--text-muted);
      border-top: 1px solid var(--dark-secondary);
    }
    
    .nav {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      padding: 20px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: rgba(19, 23, 34, 0.95);
      backdrop-filter: blur(10px);
      z-index: 1000;
    }
    
    .nav-logo {
      font-size: 24px;
      font-weight: 700;
      color: var(--primary);
      text-decoration: none;
    }
    
    .nav-links { display: flex; gap: 40px; }
    
    .nav-links a {
      color: var(--text);
      text-decoration: none;
      font-weight: 500;
      transition: color 0.3s;
    }
    
    .nav-links a:hover,
    .nav-links a.active { color: var(--primary); }
    
    .nav-buttons { display: flex; gap: 12px; }
    
    .nav-btn {
      padding: 8px 20px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s;
    }
    
    .nav-btn-login {
      background: transparent;
      color: var(--text);
      border: 1px solid var(--text-muted);
    }
    
    .nav-btn-login:hover {
      border-color: var(--primary);
      color: var(--primary);
    }
    
    .nav-btn-register {
      background: var(--primary);
      color: #fff;
    }
    
    .nav-btn-register:hover {
      background: var(--primary-dark);
    }
    
    /* Pricing Section */
    .pricing-section {
      padding: 100px 20px;
      background: var(--dark-secondary);
    }
    
    .pricing-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 30px;
      max-width: 1200px;
      margin: 0 auto;
    }
    
    .pricing-card {
      background: var(--dark);
      border-radius: 20px;
      padding: 40px;
      text-align: center;
      position: relative;
      transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .pricing-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }
    
    .pricing-card.featured {
      border: 2px solid var(--primary);
    }
    
    .pricing-badge {
      position: absolute;
      top: -15px;
      left: 50%;
      transform: translateX(-50%);
      background: var(--primary);
      color: #fff;
      padding: 8px 24px;
      border-radius: 20px;
      font-size: 14px;
      font-weight: 600;
    }
    
    .pricing-name {
      font-size: 28px;
      font-weight: 700;
      margin-bottom: 12px;
    }
    
    .pricing-price {
      margin-bottom: 24px;
    }
    
    .pricing-amount {
      font-size: 56px;
      font-weight: 700;
      color: var(--primary);
    }
    
    .pricing-period {
      color: var(--text-muted);
      font-size: 18px;
    }
    
    .pricing-desc {
      color: var(--text-muted);
      margin-bottom: 30px;
      font-size: 14px;
    }
    
    .pricing-features {
      list-style: none;
      text-align: left;
      margin-bottom: 30px;
    }
    
    .pricing-features li {
      padding: 12px 0;
      border-bottom: 1px solid var(--dark-secondary);
      display: flex;
      align-items: center;
      gap: 12px;
    }
    
    .pricing-features li::before {
      content: "✓";
      color: var(--primary);
      font-weight: 700;
    }
    
    .pricing-btn {
      width: 100%;
      padding: 16px;
      border-radius: 12px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
    }
    
    .content-section {
      padding: 80px 20px;
      max-width: 800px;
      margin: 0 auto;
    }
    
    .timeline { margin-top: 60px; }
    
    .timeline-item {
      display: flex;
      gap: 20px;
      margin-bottom: 40px;
    }
    
    .timeline-year {
      width: 80px;
      height: 80px;
      background: var(--primary);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 18px;
      flex-shrink: 0;
    }
    
    .timeline-year.secondary { background: var(--secondary); }
    
    .timeline-content h3 { font-size: 24px; margin-bottom: 12px; }
    .timeline-content p { color: var(--text-muted); }
    
    .about-section { padding: 100px 20px; }
    
    .about-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 40px;
      max-width: 1000px;
      margin: 0 auto;
    }
    
    .about-card {
      background: var(--dark-secondary);
      padding: 40px;
      border-radius: 16px;
    }
    
    .about-card h3 {
      font-size: 24px;
      margin-bottom: 16px;
      color: var(--primary);
    }
    
    .about-card ul { list-style: none; }
    
    .about-card li {
      padding: 12px 0;
      border-bottom: 1px solid var(--dark);
      color: var(--text-muted);
    }
    
    .about-card li:last-child { border-bottom: none; }
  </style>
</head>
<body>
  <nav class="nav">
    <a href="#" class="nav-logo" onclick="showPage('home')">HBAR Trading</a>
    <div class="nav-links">
      <a href="#" onclick="showPage('home')" class="active">Home</a>
      <a href="#" onclick="showPage('pricing')">Pricing</a>
      <a href="#" onclick="showPage('about')">About</a>
      <a href="#" onclick="showPage('history')">History</a>
    </div>
    <div class="nav-buttons">
      <a href="/login" class="nav-btn nav-btn-login">Login</a>
      <a href="/register" class="nav-btn nav-btn-register">Register</a>
    </div>
  </nav>

  <!-- Home Page -->
  <div id="page-home" class="page active">
    <section class="hero">
      <h1>HBAR/USDT Trading Platform</h1>
      <p>Professional trading tools, real-time charts, and paper trading for Hedera ecosystem</p>
      <div class="hero-buttons">
        <a href="/app" class="btn btn-primary">Start Trading</a>
        <a href="#" onclick="showPage('pricing')" class="btn btn-secondary">View Plans</a>
      </div>
    </section>

    <section class="features">
      <h2 class="section-title">Platform Features</h2>
      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon">📊</div>
          <h3>Real-Time Charts</h3>
          <p>Advanced candlestick charts with multiple timeframes and drawing tools</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">📈</div>
          <h3>Technical Indicators</h3>
          <p>RSI, MACD, EMA, Bollinger Bands, and more</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">🎯</div>
          <h3>Price Alerts</h3>
          <p>Set custom alerts and never miss trading opportunities</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">📝</div>
          <h3>Trade Journal</h3>
          <p>Track your trades and analyze performance over time</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">🔄</div>
          <h3>Replay Mode</h3>
          <p>Backtest your strategies with historical data</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">💰</div>
          <h3>Paper Trading</h3>
          <p>Practice with $10,000 demo account risk-free</p>
        </div>
      </div>
    </section>

    <section class="stats-section">
      <div class="stats-grid">
        <div>
          <div class="stat-number">$0</div>
          <div class="stat-label">Total Revenue</div>
        </div>
        <div>
          <div class="stat-number">0</div>
          <div class="stat-label">Active Traders</div>
        </div>
        <div>
          <div class="stat-number">0</div>
          <div class="stat-label">Trades Placed</div>
        </div>
        <div>
          <div class="stat-number">24/7</div>
          <div class="stat-label">Market Coverage</div>
        </div>
      </div>
    </section>
  </div>

  <!-- Pricing Page -->
  <div id="page-pricing" class="page">
    <section class="hero" style="min-height: 50vh;">
      <h1>Simple Pricing</h1>
      <p>Choose the plan that fits your trading needs</p>
    </section>

    <section class="pricing-section">
      <div class="pricing-grid">
        <div class="pricing-card">
          <h3 class="pricing-name">Free</h3>
          <div class="pricing-price">
            <span class="pricing-amount">$0</span>
            <span class="pricing-period">/forever</span>
          </div>
          <p class="pricing-desc">Perfect for getting started</p>
          <ul class="pricing-features">
            <li>Basic charts</li>
            <li>5 Price alerts</li>
            <li>10 Drawings</li>
            <li>Paper trading ($10k)</li>
            <li>RSI indicator</li>
          </ul>
            <button class="pricing-btn btn-secondary" onclick="window.location.href='/register'">Get Started</button>
        </div>
        
        <div class="pricing-card featured">
          <div class="pricing-badge">Most Popular</div>
          <h3 class="pricing-name">Pro</h3>
          <div class="pricing-price">
            <span class="pricing-amount">$29.99</span>
            <span class="pricing-period">/month</span>
          </div>
          <p class="pricing-desc">For serious traders</p>
          <ul class="pricing-features">
            <li>Everything in Free</li>
            <li>Unlimited alerts</li>
            <li>Unlimited drawings</li>
            <li>All indicators</li>
            <li>Backtesting</li>
            <li>Replay mode</li>
            <li>Data export</li>
          </ul>
           <button class="pricing-btn btn-primary" onclick="window.location.href='/register'">Start Pro Trial</button>
        </div>
        
        <div class="pricing-card">
          <h3 class="pricing-name">Premium</h3>
          <div class="pricing-price">
            <span class="pricing-amount">$99.99</span>
            <span class="pricing-period">/month</span>
          </div>
          <p class="pricing-desc">For trading teams</p>
          <ul class="pricing-features">
            <li>Everything in Pro</li>
            <li>Team collaboration</li>
            <li>White-label options</li>
            <li>Priority support</li>
            <li>Custom integrations</li>
            <li>API access</li>
          </ul>
           <button class="pricing-btn btn-outline" onclick="window.location.href='/register'">Contact Sales</button>
        </div>
      </div>
    </section>
  </div>

  <!-- About Page -->
  <div id="page-about" class="page">
    <section class="hero" style="min-height: 50vh;">
      <h1>About HBAR Trading</h1>
      <p>The most advanced HBAR/USDT trading platform</p>
    </section>

    <section class="about-section">
      <div class="about-grid">
        <div class="about-card">
          <h3>Our Mission</h3>
          <p style="color: var(--text-muted);">
            We built HBAR Trading to provide traders with professional-grade tools for analyzing and trading HBAR/USDT. Our platform combines real-time market data with advanced charting capabilities.
          </p>
        </div>
        <div class="about-card">
          <h3>Key Features</h3>
          <ul>
            <li>✓ Real-time candlestick charts from Binance</li>
            <li>✓ Multiple timeframes (1m, 5m, 15m, 1h, 4h, 1d)</li>
            <li>✓ Technical indicators (RSI, MACD, EMA, BB)</li>
            <li>✓ Drawing tools for chart analysis</li>
            <li>✓ Paper trading with $10,000 demo</li>
            <li>✓ Trade journal to track performance</li>
            <li>✓ Price alerts and replay mode</li>
          </ul>
        </div>
      </div>
    </section>
  </div>

  <!-- History Page -->
  <div id="page-history" class="page">
    <section class="hero" style="min-height: 50vh;">
      <h1>Platform History</h1>
      <p>Our journey to bring professional trading tools to HBAR</p>
    </section>

    <section class="content-section">
      <div class="timeline">
        <div class="timeline-item">
          <div class="timeline-year">2024</div>
          <div class="timeline-content">
            <h3>Platform Launch</h3>
            <p>HBAR Trading platform launched with basic charting and paper trading features.</p>
          </div>
        </div>
        <div class="timeline-item">
          <div class="timeline-year secondary">2025</div>
          <div class="timeline-content">
            <h3>Major Updates</h3>
            <p>Added technical indicators (RSI, MACD, EMA), drawing tools, price alerts, and trade journal.</p>
          </div>
        </div>
        <div class="timeline-item">
          <div class="timeline-year" style="background: linear-gradient(135deg, var(--primary), var(--secondary));">2026</div>
          <div class="timeline-content">
            <h3>Full Platform</h3>
            <p>Rebuilt with Laravel backend, MySQL database, and Filament admin panel. Added subscription plans and SaaS features.</p>
          </div>
        </div>
      </div>
    </section>
  </div>

  <footer>
    <p>&copy; 2026 HBAR Trading Platform. All rights reserved.</p>
  </footer>

  <script>
    function showPage(page) {
      event.preventDefault();
      document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
      document.querySelectorAll('.nav-links a').forEach(a => a.classList.remove('active'));
      document.getElementById('page-' + page).classList.add('active');
      event.target.classList.add('active');
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  </script>
</body>
</html>
