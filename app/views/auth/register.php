<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Restaurant Management</title>

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
            /* Đồng bộ tone với dashboard */
            --background: 0 0% 99%;
            --foreground: 234 32% 12%;
            --card: 0 0% 100%;
            --card-foreground: 234 32% 12%;
            --border: 252 26% 88%;
            --input: 252 26% 88%;
            --primary: 249 76% 64%;
            --primary-foreground: 0 0% 100%;
            --ring: 249 76% 64%;
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

        .register-container {
            max-width: 500px;
            width: 100%;
        }

        .register-card {
            background: hsl(var(--card));
            border: 1px solid hsl(var(--border));
            border-radius: 8px;
            padding: 0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .register-header {
            padding: 32px 24px 24px;
            text-align: center;
            border-bottom: 1px solid hsl(var(--border));
        }

        .register-header i {
            font-size: 48px;
            margin-bottom: 12px;
            color: hsl(var(--primary));
        }

        .register-header h2 {
            font-size: 24px;
            font-weight: 600;
            color: hsl(var(--foreground));
            margin: 0 0 4px 0;
            letter-spacing: -0.025em;
        }

        .register-header p {
            font-size: 14px;
            color: hsl(var(--foreground) / 0.6);
            margin: 0;
        }

        .register-body {
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

        .btn-register {
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

        .btn-register:hover:not(:disabled) {
            background: hsl(var(--primary) / 0.9);
        }

        .btn-register:active:not(:disabled) {
            background: hsl(var(--primary) / 0.95);
        }

        .btn-register:disabled {
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

        .login-link {
            text-align: center;
            margin-top: 16px;
            font-size: 14px;
            color: hsl(var(--foreground) / 0.7);
        }

        .login-link a {
            color: hsl(var(--primary));
            text-decoration: none;
            font-weight: 500;
            transition: color 0.15s;
        }

        .login-link a:hover {
            color: hsl(var(--primary) / 0.8);
        }
    </style>
</head>

<body>

    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <i class="bi bi-person-plus"></i>
                <h2>Đăng ký tài khoản</h2>
                <p>Tạo tài khoản mới cho hệ thống</p>
            </div>

            <div class="register-body">
                <!-- Alert -->
                <div id="alertBox" class="alert alert-danger d-none" role="alert">
                    <i class="bi bi-exclamation-circle"></i>
                    <span id="alertMessage"></span>
                </div>

                <!-- Register Form -->
                <form id="registerForm" novalidate>
                    <div class="form-group">
                        <label for="fullname" class="form-label">Tên đầy đủ</label>
                        <input type="text" class="form-control" id="fullname" placeholder="Nhập tên đầy đủ">
                    </div>

                    <div class="form-group">
                        <label for="username" class="form-label">Tên đăng nhập</label>
                        <input type="text" class="form-control" id="username" placeholder="Nhập tên đăng nhập" autocomplete="username">
                    </div>

                    <div class="form-group">
                        <label for="role" class="form-label">Quyền hạn</label>
                        <select class="form-control" id="role">
                            <option value="">-- Chọn quyền hạn --</option>
                            <option value="admin">Quản trị viên (Admin)</option>
                            <option value="manager">Quản lý (Manager)</option>
                            <option value="user">Nhân viên (User)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <div class="password-toggle">
                            <input type="password" class="form-control" id="password" placeholder="Nhập mật khẩu (tối thiểu 6 ký tự)" autocomplete="new-password">
                            <button type="button" class="password-toggle-btn" onclick="togglePassword('password')">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirmPassword" class="form-label">Xác nhận mật khẩu</label>
                        <div class="password-toggle">
                            <input type="password" class="form-control" id="confirmPassword" placeholder="Nhập lại mật khẩu" autocomplete="new-password">
                            <button type="button" class="password-toggle-btn" onclick="togglePassword('confirmPassword')">
                                <i class="bi bi-eye" id="toggleIconConfirm"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-register" id="btnRegister">
                        <span id="btnText">
                            <i class="bi bi-check-circle"></i>
                            Đăng ký
                        </span>
                        <span id="btnLoading" class="d-none">
                            <span class="spinner-border spinner-border-sm"></span>
                            Đang xử lý...
                        </span>
                    </button>
                </form>

                <!-- Login Link -->
                <div class="login-link">
                    Đã có tài khoản? <a href="<?php echo BASE_URL; ?>/auth/login">Đăng nhập ngay</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const BASE_URL = '<?php echo BASE_URL; ?>';

        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = fieldId === 'password' ? document.getElementById('toggleIcon') : document.getElementById('toggleIconConfirm');

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
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
        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const fullname = document.getElementById('fullname').value.trim();
            const username = document.getElementById('username').value.trim();
            const role = document.getElementById('role').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            // Validate
            if (!fullname || !username || !role || !password || !confirmPassword) {
                showAlert('Vui lòng điền đầy đủ tất cả các trường');
                return;
            }

            if (password.length < 6) {
                showAlert('Mật khẩu phải có tối thiểu 6 ký tự');
                return;
            }

            if (password !== confirmPassword) {
                showAlert('Mật khẩu xác nhận không khớp');
                return;
            }

            // Show loading
            const btnRegister = document.getElementById('btnRegister');
            const btnText = document.getElementById('btnText');
            const btnLoading = document.getElementById('btnLoading');

            btnRegister.disabled = true;
            btnText.classList.add('d-none');
            btnLoading.classList.remove('d-none');

            try {
                const response = await fetch(`${BASE_URL}/auth/doRegister`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        fullname: fullname,
                        username: username,
                        password: password,
                        role: role
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showAlert(`Đăng ký thành công! Chuyển hướng...`, 'success');

                    // Redirect to login
                    setTimeout(() => {
                        window.location.href = `${BASE_URL}/auth/login`;
                    }, 2000);
                } else {
                    showAlert(data.message || 'Đăng ký thất bại');

                    // Reset button
                    btnRegister.disabled = false;
                    btnText.classList.remove('d-none');
                    btnLoading.classList.add('d-none');
                }
            } catch (error) {
                console.error('Register error:', error);
                showAlert('Có lỗi xảy ra. Vui lòng thử lại sau.');

                // Reset button
                btnRegister.disabled = false;
                btnText.classList.remove('d-none');
                btnLoading.classList.add('d-none');
            }
        });

        // Auto focus fullname
        document.getElementById('fullname').focus();
    </script>
</body>

</html>