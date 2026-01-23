<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - HBAR Trading</title>
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
    
    .register-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 100px 20px;
    }
    
    .register-card {
      background: var(--dark-secondary);
      border-radius: 20px;
      padding: 50px;
      width: 100%;
      max-width: 480px;
      animation: slideUp 0.4s ease;
    }
    
    @keyframes slideUp {
      from { transform: translateY(20px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }
    
    .register-header {
      text-align: center;
      margin-bottom: 40px;
    }
    
    .register-header h1 {
      font-size: 32px;
      margin-bottom: 12px;
      color: var(--primary);
    }
    
    .register-header p {
      color: var(--text-muted);
      font-size: 16px;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
      color: var(--text);
    }
    
    .form-input {
      width: 100%;
      padding: 14px 16px;
      background: var(--dark);
      border: 1px solid var(--dark-secondary);
      border-radius: 10px;
      color: var(--text);
      font-size: 15px;
      transition: border-color 0.3s;
    }
    
    .form-input:focus {
      outline: none;
      border-color: var(--primary);
    }
    
    .form-input::placeholder { color: var(--text-muted); }
    
    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }
    
    .password-strength {
      margin-top: 8px;
    }
    
    .strength-bar {
      height: 4px;
      background: var(--dark);
      border-radius: 2px;
      overflow: hidden;
      margin-bottom: 6px;
    }
    
    .strength-fill {
      height: 100%;
      width: 0%;
      transition: width 0.3s, background-color 0.3s;
      border-radius: 2px;
    }
    
    .strength-text {
      font-size: 12px;
      color: var(--text-muted);
    }
    
    .terms {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      margin: 24px 0;
    }
    
    .terms input {
      width: 18px;
      height: 18px;
      margin-top: 2px;
      accent-color: var(--primary);
      flex-shrink: 0;
    }
    
    .terms label {
      font-size: 14px;
      color: var(--text-muted);
      line-height: 1.5;
    }
    
    .terms a {
      color: var(--primary);
      text-decoration: none;
    }
    
    .terms a:hover { text-decoration: underline; }
    
    .submit-btn {
      width: 100%;
      padding: 16px;
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
      margin: 28px 0;
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
    
    .login-prompt {
      text-align: center;
      color: var(--text-muted);
      font-size: 15px;
    }
    
    .login-prompt a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s;
    }
    
    .login-prompt a:hover { color: var(--primary-dark); }
    
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

  <div class="register-container">
    <div class="register-card">
      <div class="register-header">
        <h1>Create Account</h1>
        <p>Start trading HBAR/USDT with professional tools</p>
      </div>

      <div class="error-message" id="error-message"></div>

      <form id="register-form" onsubmit="handleRegister(event)">
        <div class="form-row">
          <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" class="form-input" placeholder="John Doe" required>
          </div>
          <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" class="form-input" placeholder="john@example.com" required>
          </div>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" class="form-input" placeholder="Min 8 characters" required minlength="8" oninput="checkPasswordStrength()">
          <div class="password-strength">
            <div class="strength-bar">
              <div class="strength-fill" id="strength-fill"></div>
            </div>
            <span class="strength-text" id="strength-text">Enter a password</span>
          </div>
        </div>

        <div class="form-group">
          <label for="confirm_password">Confirm Password</label>
          <input type="password" id="confirm_password" class="form-input" placeholder="Confirm your password" required>
        </div>

        <div class="terms">
          <input type="checkbox" id="terms" required>
          <label for="terms">
            I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
          </label>
        </div>

        <button type="submit" class="submit-btn" id="submit-btn">Create Account</button>
      </form>

      <div class="divider">or</div>

      <p class="login-prompt">
        Already have an account? <a href="/login">Sign in</a>
      </p>
    </div>
  </div>

  <footer>
    <p>&copy; 2026 HBAR Trading Platform. All rights reserved.</p>
  </footer>

  <script>
    function checkPasswordStrength() {
      const password = document.getElementById('password').value;
      const fill = document.getElementById('strength-fill');
      const text = document.getElementById('strength-text');
      
      let strength = 0;
      let label = 'Weak';
      let color = '#ef5350';
      
      if (password.length >= 8) strength += 25;
      if (password.match(/[a-z])) strength += 25;
      if (password.match(/[A-Z])) strength += 25;
      if (password.match(/[0-9]|[^a-zA-Z0-9]/)) strength += 25;
      
      switch (strength) {
        case 0:
          label = 'Enter a password';
          color = '#787b86';
          break;
        case 25:
          label = 'Weak';
          color = '#ef5350';
          break;
        case 50:
          label = 'Fair';
          color = '#ff9800';
          break;
        case 75:
          label = 'Good';
          color = '#ffeb3b';
          break;
        case 100:
          label = 'Strong';
          color = '#26a69a';
          break;
      }
      
      fill.style.width = strength + '%';
      fill.style.backgroundColor = color;
      text.textContent = label;
      text.style.color = color;
    }
    
    async function handleRegister(e) {
      e.preventDefault();
      
      const name = document.getElementById('name').value;
      const email = document.getElementById('email').value;
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirm_password').value;
      const errorEl = document.getElementById('error-message');
      const submitBtn = document.getElementById('submit-btn');
      
      errorEl.classList.remove('show');
      errorEl.textContent = '';
      
      if (password !== confirmPassword) {
        errorEl.textContent = 'Passwords do not match';
        errorEl.classList.add('show');
        return;
      }
      
      submitBtn.disabled = true;
      submitBtn.textContent = 'Creating account...';
      
      try {
        const res = await fetch('/api/auth/register', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email, password, name })
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
        submitBtn.textContent = 'Create Account';
      }
    }
  </script>
</body>
</html>
