<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - HBAR Trading</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    
    :root {
      --primary: #26a69a;
      --primary-dark: #1e8e83;
      --dark: #131722;
      --dark-secondary: #1e222d;
      --text: #d1d4dc;
      --text-muted: #787b86;
    }
    
    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: var(--dark);
      color: var(--text);
      min-height: 100vh;
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
    
    .nav-btn-register {
      background: var(--primary);
      color: #fff;
    }
    
    .login-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 100px 20px;
    }
    
    .login-card {
      background: var(--dark-secondary);
      border-radius: 20px;
      padding: 50px;
      width: 100%;
      max-width: 440px;
      animation: slideUp 0.4s ease;
    }
    
    @keyframes slideUp {
      from { transform: translateY(20px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }
    
    .login-header {
      text-align: center;
      margin-bottom: 40px;
    }
    
    .login-header h1 {
      font-size: 32px;
      margin-bottom: 12px;
      color: var(--primary);
    }
    
    .login-header p {
      color: var(--text-muted);
      font-size: 16px;
    }
    
    .form-group {
      margin-bottom: 24px;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 10px;
      font-weight: 500;
      color: var(--text);
    }
    
    .form-input {
      width: 100%;
      padding: 16px;
      background: var(--dark);
      border: 1px solid var(--dark-secondary);
      border-radius: 12px;
      color: var(--text);
      font-size: 16px;
      transition: border-color 0.3s;
    }
    
    .form-input:focus {
      outline: none;
      border-color: var(--primary);
    }
    
    .form-input::placeholder { color: var(--text-muted); }
    
    .form-options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
    }
    
    .remember-me {
      display: flex;
      align-items: center;
      gap: 10px;
      cursor: pointer;
    }
    
    .remember-me input {
      width: 18px;
      height: 18px;
      accent-color: var(--primary);
    }
    
    .forgot-link {
      color: var(--primary);
      text-decoration: none;
      font-size: 14px;
      transition: color 0.3s;
    }
    
    .forgot-link:hover { color: var(--primary-dark); }
    
    .submit-btn {
      width: 100%;
      padding: 18px;
      background: var(--primary);
      border: none;
      border-radius: 12px;
      color: #fff;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
    }
    
    .submit-btn:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
    }
    
    .submit-btn:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none;
    }
    
    .error-message {
      background: rgba(239, 83, 80, 0.1);
      border: 1px solid #ef5350;
      color: #ef5350;
      padding: 14px;
      border-radius: 10px;
      margin-bottom: 20px;
      font-size: 14px;
      display: none;
    }
    
    .error-message.show { display: block; }
    
    .divider {
      display: flex;
      align-items: center;
      gap: 16px;
      margin: 30px 0;
      color: var(--text-muted);
      font-size: 14px;
    }
    
    .divider::before,
    .divider::after {
      content: "";
      flex: 1;
      height: 1px;
      background: var(--dark-secondary);
    }
    
    .register-prompt {
      text-align: center;
      color: var(--text-muted);
      font-size: 15px;
    }
    
    .register-prompt a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s;
    }
    
    .register-prompt a:hover { color: var(--primary-dark); }
    
    footer {
      padding: 30px;
      text-align: center;
      color: var(--text-muted);
      border-top: 1px solid var(--dark-secondary);
    }
  </style>
</head>
<body>
  <nav class="nav">
    <a href="/" class="nav-logo">HBAR Trading</a>
    <div class="nav-links">
      <a href="/">Home</a>
      <a href="/pricing">Pricing</a>
      <a href="/about">About</a>
      <a href="/history">History</a>
    </div>
    <div class="nav-buttons">
      <a href="/login" class="nav-btn nav-btn-login">Login</a>
      <a href="/register" class="nav-btn nav-btn-register">Register</a>
    </div>
  </nav>

  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <h1>Welcome Back</h1>
        <p>Sign in to your HBAR Trading account</p>
      </div>

      <div class="error-message" id="error-message"></div>

      <form id="login-form" onsubmit="handleLogin(event)">
        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" class="form-input" placeholder="Enter your email" required>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" class="form-input" placeholder="Enter your password" required>
        </div>

        <div class="form-options">
          <label class="remember-me">
            <input type="checkbox" id="remember">
            <span>Remember me</span>
          </label>
          <a href="#" class="forgot-link">Forgot password?</a>
        </div>

        <button type="submit" class="submit-btn" id="submit-btn">Sign In</button>
      </form>

      <div class="divider">or</div>

      <p class="register-prompt">
        Don't have an account? <a href="/register">Create one now</a>
      </p>
    </div>
  </div>

  <footer>
    <p>&copy; 2026 HBAR Trading Platform. All rights reserved.</p>
  </footer>

  <script>
    async function handleLogin(e) {
      e.preventDefault();
      
      const email = document.getElementById('email').value;
      const password = document.getElementById('password').value;
      const errorEl = document.getElementById('error-message');
      const submitBtn = document.getElementById('submit-btn');
      
      errorEl.classList.remove('show');
      errorEl.textContent = '';
      submitBtn.disabled = true;
      submitBtn.textContent = 'Signing in...';
      
      try {
        const res = await fetch('/api/auth/login', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email, password })
        });
        
        const data = await res.json();
        
        if (data.error) {
          errorEl.textContent = data.error;
          errorEl.classList.add('show');
        } else {
          localStorage.setItem('hbar_token', data.token);
          window.location.href = '/app';
        }
      } catch (err) {
        errorEl.textContent = 'Unable to connect. Please check your connection.';
        errorEl.classList.add('show');
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Sign In';
      }
    }
  </script>
</body>
</html>
