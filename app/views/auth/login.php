<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Restaurant Management</title>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Shadcn UI CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/shadcn.css">
    
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1.5rem;
        }
        :root {
            --background: 0 0% 100%;
            --foreground: 222.2 84% 4.9%;
            --card: 0 0% 100%;
            --card-foreground: 222.2 84% 4.9%;
            --border: 214.3 31.8% 91.4%;
            --input: 214.3 31.8% 91.4%;
            --primary: 222.2 47.4% 11.2%;
            --primary-foreground: 210 40% 98%;
            --ring: 222.2 84% 4.9%;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: hsl(var(--background));
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: hsl(var(--foreground));
            padding: 20px;
        }
        
        .login-container {
            max-width: 420px;
            width: 100%;
        }
        
        .login-card {
            background: hsl(var(--card));
            border: 1px solid hsl(var(--border));
            border-radius: 8px;
            padding: 0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        
        .login-header {
            padding: 32px 24px 24px;
            text-align: center;
            border-bottom: 1px solid hsl(var(--border));
        }
        
        .login-header i {
            font-size: 48px;
            margin-bottom: 12px;
            color: hsl(var(--primary));
        }
        
        .login-header h2 {
            font-size: 24px;
            font-weight: 600;
            color: hsl(var(--foreground));
            margin: 0 0 4px 0;
            letter-spacing: -0.025em;
        }
        
        .login-header p {
            font-size: 14px;
            color: hsl(var(--foreground) / 0.6);
            margin: 0;
        }
        
        .login-body {
            padding: 24px;
        }
        
        .form-group {
            margin-bottom: 16px;
        }
        
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
            color: hsl(var(--foreground));
        }
        
        .form-control {
            width: 100%;
            padding: 10px 12px;
            font-size: 14px;
            border: 1px solid hsl(var(--input));
            border-radius: 6px;
            background: hsl(var(--background));
            color: hsl(var(--foreground));
            transition: all 0.15s ease;
            outline: none;
        }
        
        .form-control:hover {
            border-color: hsl(var(--foreground) / 0.3);
        }
        
        .form-control:focus {
            border-color: hsl(var(--ring));
            box-shadow: 0 0 0 3px hsl(var(--ring) / 0.1);
        }
        
        .form-control::placeholder {
            color: hsl(var(--foreground) / 0.4);
        }
        
        .password-toggle {
            position: relative;
        }
        
        .password-toggle-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: hsl(var(--foreground) / 0.5);
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.15s;
        }
        
        .password-toggle-btn:hover {
            color: hsl(var(--foreground));
        }
        
        .form-check {
            display: flex;
            align-items: center;
            margin: 16px 0;
        }
        
        .form-check-input {
            width: 16px;
            height: 16px;
            margin-right: 8px;
            cursor: pointer;
            border: 1px solid hsl(var(--input));
        }
        
        .form-check-input:checked {
            background-color: hsl(var(--primary));
            border-color: hsl(var(--primary));
        }
        
        .form-check-label {
            font-size: 14px;
            color: hsl(var(--foreground));
            cursor: pointer;
            user-select: none;
        }
        
        .btn-login {
            width: 100%;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 500;
            color: hsl(var(--primary-foreground));
            background: hsl(var(--primary));
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.15s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-login:hover:not(:disabled) {
            background: hsl(var(--primary) / 0.9);
        }
        
        .btn-login:active:not(:disabled) {
            background: hsl(var(--primary) / 0.95);
        }
        
        .btn-login:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 14px;
            display: flex;
            align-items: start;
            gap: 8px;
            border: 1px solid;
        }
        
        .alert-danger {
            background: hsl(0 84.2% 60.2% / 0.1);
            border-color: hsl(0 84.2% 60.2% / 0.2);
            color: hsl(0 84.2% 35%);
        }
        
        .alert-success {
            background: hsl(142.1 76.2% 36.3% / 0.1);
            border-color: hsl(142.1 76.2% 36.3% / 0.2);
            color: hsl(142.1 76.2% 25%);
        }
        
        .alert i {
            flex-shrink: 0;
            margin-top: 1px;
        }
        
        .spinner-border-sm {
            width: 14px;
            height: 14px;
            border-width: 2px;
        }
        
        .info-box {
            margin-top: 20px;
            padding: 16px;
            background: hsl(var(--foreground) / 0.03);
            border: 1px solid hsl(var(--border));
            border-radius: 6px;
            text-align: center;
        }
        
        .info-box p {
            margin: 0 0 8px 0;
            font-size: 13px;
            color: hsl(var(--foreground) / 0.7);
        }
        
        .info-box .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 12px;
            font-weight: 500;
            border-radius: 4px;
            margin: 0 4px;
        }
        
        .badge-admin {
            background: hsl(var(--foreground) / 0.1);
            color: hsl(var(--foreground));
        }
        
        .badge-password {
            background: hsl(var(--foreground) / 0.08);
            color: hsl(var(--foreground) / 0.8);
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <i class="bi bi-shield-lock"></i>
                <h2>Đăng nhập</h2>
                <p>Hệ thống quản lý nhà hàng</p>
            </div>
            
            <div class="login-body">
                <!-- Alert -->
                <div id="alertBox" class="alert alert-danger d-none" role="alert">
                    <i class="bi bi-exclamation-circle"></i>
                    <span id="alertMessage"></span>
                </div>
                
                <!-- Login Form -->
                <form id="loginForm">
                    <div class="form-group">
                        <label for="username" class="form-label">Tên đăng nhập</label>
                        <input type="text" class="form-control" id="username" placeholder="Nhập tên đăng nhập" required autocomplete="username">
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <div class="password-toggle">
                            <input type="password" class="form-control" id="password" placeholder="Nhập mật khẩu" required autocomplete="current-password">
                            <button type="button" class="password-toggle-btn" onclick="togglePassword()">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" checked>
                        <label class="form-check-label" for="remember">
                            Ghi nhớ đăng nhập
                        </label>
                    </div>
                    
                    <button type="submit" class="btn-login" id="btnLogin">
                        <span id="btnText">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Đăng nhập
                        </span>
                        <span id="btnLoading" class="d-none">
                            <span class="spinner-border spinner-border-sm"></span>
                            Đang xử lý...
                        </span>
                    </button>
                </form>
                
                <!-- Info -->
                <div class="info-box">
                    <p><strong>Tài khoản mặc định:</strong></p>
                    <p>
                        <span class="badge badge-admin">admin</span>
                        <span class="badge badge-admin">manager</span>
                        <span class="badge badge-admin">user</span>
                    </p>
                    <p>Mật khẩu: <span class="badge badge-password">admin123</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const BASE_URL = '<?php echo BASE_URL; ?>';
        
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        }
        
        // Show alert
        function showAlert(message, type = 'danger') {
            const alertBox = document.getElementById('alertBox');
            const alertMessage = document.getElementById('alertMessage');
            
            alertBox.className = `alert alert-${type}`;
            alertMessage.textContent = message;
            alertBox.classList.remove('d-none');
            
            // Auto hide after 5 seconds
            setTimeout(() => {
                alertBox.classList.add('d-none');
            }, 5000);
        }
        
        // Handle form submit
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            const remember = document.getElementById('remember').checked;
            
            // Validate
            if (!username || !password) {
                showAlert('Vui lòng nhập đầy đủ thông tin đăng nhập');
                return;
            }
            
            // Show loading
            const btnLogin = document.getElementById('btnLogin');
            const btnText = document.getElementById('btnText');
            const btnLoading = document.getElementById('btnLoading');
            
            btnLogin.disabled = true;
            btnText.classList.add('d-none');
            btnLoading.classList.remove('d-none');
            
            try {
                const response = await fetch(`${BASE_URL}/auth/doLogin`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        username: username,
                        password: password,
                        remember: remember
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Store token in localStorage
                    localStorage.setItem('jwt_token', data.token);
                    localStorage.setItem('user', JSON.stringify(data.user));
                    
                    // Show success message
                    showAlert(`Chào mừng ${data.user.fullname}!`, 'success');
                    
                    // Redirect to dashboard
                    setTimeout(() => {
                        window.location.href = `${BASE_URL}/dashboard`;
                    }, 1000);
                } else {
                    showAlert(data.message || 'Đăng nhập thất bại');
                    
                    // Reset button
                    btnLogin.disabled = false;
                    btnText.classList.remove('d-none');
                    btnLoading.classList.add('d-none');
                }
            } catch (error) {
                console.error('Login error:', error);
                showAlert('Có lỗi xảy ra. Vui lòng thử lại sau.');
                
                // Reset button
                btnLogin.disabled = false;
                btnText.classList.remove('d-none');
                btnLoading.classList.add('d-none');
            }
        });
        
        // Auto focus username
        document.getElementById('username').focus();
        
        // Check if already logged in
        const token = localStorage.getItem('jwt_token');
        if (token) {
            // Verify token
            fetch(`${BASE_URL}/auth/verify`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.href = `${BASE_URL}/dashboard`;
                }
            })
            .catch(err => console.log('Token verification failed'));
        }
    </script>
</body>
</html>
