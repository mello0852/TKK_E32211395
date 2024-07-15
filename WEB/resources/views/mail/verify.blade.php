<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #333;
        }
        .content {
            text-align: center;
        }
        .content p {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Verifikasi Email</h1>
        </div>
        <div class="content">
            <p>Halo, {{ $user->name }}</p>
            <p>Terima kasih telah mendaftar di VoltTech. Untuk menyelesaikan proses pendaftaran, silakan klik tombol di bawah ini untuk memverifikasi alamat email Anda.</p>
            <a href="{{ $verificationUrl }}" class="button">Verifikasi Email</a>
        </div>
        <div class="footer">
            <p>Jika Anda tidak melakukan pendaftaran ini, silakan abaikan email ini.</p>
            <p>&copy; {{ date('Y') }} VoltTech. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
