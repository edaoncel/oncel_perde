<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifremi Unuttum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
    <div class="card shadow p-4" style="width: 400px;">
        <h3 class="text-center mb-3">Şifremi Unuttum</h3>
        <p class="text-muted small text-center">E-posta adresinizi girin, size 6 haneli doğrulama kodu gönderelim.</p>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">E-posta Adresi</label>
                <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
            </div>
            <button type="submit" class="btn btn-primary w-100">Kod Gönder</button>
        </form>
        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="text-decoration-none small">Giriş Yap'a Dön</a>
        </div>
    </div>
</body>
</html>