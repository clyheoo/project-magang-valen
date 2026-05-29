<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Sistem Arsip Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-image: url("{{ asset('images/background-login.jpg') }}");
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("{{ asset('images/background-login.jpg') }}");
            background-size: 100% auto;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 8px;
            width: 350px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .login-header {
            margin: -30px -30px 20px -30px;
            text-align: center;
            color: #fff;
            background-color: rgba(52, 73, 94, 0.9);
            padding: 15px;
            border-radius: 6px 6px 0 0;
            font-weight: bold;
            font-size: 1.25rem;
        }
        .form-label {
            font-weight: 600;
        }
        .btn-login {
            background-color: #3498db;
            border: none;
            width: 100%;
            padding: 10px;
            color: white;
            font-weight: 600;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .btn-login:hover {
            background-color: #2980b9;
        }
        .forgot-password {
            font-size: 0.875rem;
            text-align: right;
            margin-top: 10px;
        }
        .forgot-password a {
            color: #3498db;
            text-decoration: none;
        }
        .forgot-password a:hover {
            text-decoration: underline;
        }
        .register-link {
            font-size: 0.875rem;
            text-align: left;
            margin-top: 10px;
        }
        .register-link a {
            color: #3498db;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
        .links-container {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fas fa-lock"></i> Sistem Arsip Digital
            <div style="font-weight: normal; font-size: 0.9rem;">Masukkan kredensial Anda</div>
        </div>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus />
                @error('email')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input id="password" type="password" class="form-control" name="password" required autocomplete="current-password" />
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
                @error('password')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember" />
                <label class="form-check-label" for="remember">Ingat saya</label>
            </div>
            <button type="submit" class="btn btn-login">Masuk</button>
            <div class="links-container">
                <div class="register-link">
                    <a href="{{ route('register') }}">Belum punya akun? Daftar</a>
                </div>
                <div class="forgot-password">
                    <a href="{{ route('password.request') }}">Lupa Password?</a>
                </div>
            </div>
        </form>
    </div>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });
    </script>
</body>
</html>
