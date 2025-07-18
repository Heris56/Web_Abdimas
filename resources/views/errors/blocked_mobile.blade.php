<!DOCTYPE html>
<html>

<head>
    <title>Akses Ditolak</title>
    <style>
        body {
            font-family: sans-serif;
            text-align: center;
            margin-top: 20%;
            background-color: #f8f9fa;
            color: #333;
        }

        h1 {
            font-size: 2em;
            margin-bottom: 0.5em;
        }

        p {
            font-size: 1.2em;
        }
    </style>
</head>

<body>
    <h1>Akses Ditolak</h1>
    <p>Role <strong>{{ ucfirst($role) }}</strong> tidak diperbolehkan membuka halaman ini dari perangkat mobile.</p>
    <p>Silakan gunakan desktop atau laptop untuk mengakses halaman ini.</p>
</body>

</html>