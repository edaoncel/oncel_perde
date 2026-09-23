<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifreyi Sıfırla</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
    <div class="card shadow p-4" style="width: 400px;">
        <h3 class="text-center mb-3">Yeni Şifre Belirle</h3>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">E-posta Adresi</label>
                <input type="email" name="email" class="form-control" value="{{ session('email') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">6 Haneli Kod</label>
                <input type="text" name="code" class="form-control" maxlength="6" required placeholder="123456">
            </div>
            <div class="mb-3">
                <label class="form-label">Yeni Şifre</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Yeni Şifre (Tekrar)</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Şifreyi Güncelle</button>
        </form>
    </div>
</body>
</html>