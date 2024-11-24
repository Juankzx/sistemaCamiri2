<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    
    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
        }
        .split-left {
            position: relative;
            background-image: url('{{ asset('storage/imagenes/logosoin.png') }}'); /* Replace with your image path */
            background-size: cover;
            background-position: center;
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .split-right {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
            background: rgba(255, 255, 255, 0.9);
        }
        .login-container {
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .login-header img {
            width: 80px;
        }
        .login-header h2 {
            margin: 0;
            font-size: 24px;
        }
        .login-footer {
            text-align: center;
            margin-top: 20px;
        }
        .login-footer a {
            color: #333;
        }
        .social-buttons {
            position: absolute;
            bottom: 40px;
            display: flex;
            gap: 20px;
        }
        .social-buttons a {
            color: white;
            font-size: 30px;
        }
    </style>
</head>
<body>
    <div class="split-left">
        <div class="social-buttons">
            <a href="https://www.instagram.com/soin_tech" target="_blank"><i class="fab fa-instagram"></i></a>
            <a href="https://wa.me/+56945296314" target="_blank"><i class="fab fa-whatsapp"></i></a>
        </div>
    </div>
    <div class="split-right">
        <div class="login-container">
            @yield('content')
        </div>
    </div>
</body>
</html>
