<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Registrasi - Sistem Arsip Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #2c3e50;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .register-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            width: 350px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .register-header {
            text-align: center;
            margin-bottom: 20px;
            color: #fff;
            background-color: #34495e;
            padding: 15px;
            border-radius: 6px 6px 0 0;
            font-weight: bold;
            font-size: 1.25rem;
        }
        .form-label {
            font-weight: 600;
        }
        .btn-register {
            background-color: #3498db;
            border: none;
            width: 100%;
            padding: 10px;
            color: white;
            font-weight: 600;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .btn-register:hover {
            background-color: #2980b9;
        }
        .login-link {
            font-size: 0.875rem;
            text-align: center;
            margin-top: 10px;
        }
        .login-link a {
            color: #3498db;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            Sistem Arsip Digital - Registrasi
        </div>
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus />
                @error('name')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required />
                @error('email')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input id="password" type="password" class="form-control" name="password" required autocomplete="new-password" />
                @error('password')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" />
            </div>
            <button type="submit" class="btn btn-register">Daftar</button>
            <div class="login-link">
                Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
            </div>
        </form>
    </div>
</body>
</html>
