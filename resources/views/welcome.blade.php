<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>AMELYS SHOP</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #6f42c1, #d63384);
            min-height: 100vh;
            color: #fff;
        }

        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .card-welcome {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 50px;
            max-width: 550px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
        }

        .card-welcome h1 {
            font-weight: 700;
            letter-spacing: 2px;
        }

        .btn-custom {
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="hero">
    <div class="card-welcome">
        <h1 class="mb-3">AMELYS SHOP</h1>
        <p class="mb-4">
            Selamat datang di <strong>AMELYS SHOP</strong><br>
            Pokokya kerja kerja kerja, gak tau kerjanya apa
        </p>

        <div class="d-flex justify-content-center gap-3">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-light btn-custom">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-light btn-custom">
                    Login
                </a>
            @endauth
        </div>
    </div>
</div>

</body>
</html>
