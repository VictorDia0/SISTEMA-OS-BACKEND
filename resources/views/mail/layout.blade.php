<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        .adapt-img {
            width: 100% !important;
            height: auto !important
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
        }

        .header {
            background-color: #2a9c5d;
            color: #ffffff;
            text-align: center;
            padding: 0px;
        }

        .header img {
            width: 150px;
        }

        .content {
            padding: 20px;
            color: #333333;
            text-align: left;
        }

        .content h2 {
            font-size: 22px;
            color: #333333;
            margin: 0 0 20px;
        }

        .content p {
            font-size: 16px;
            margin: 10px 0;
        }

        .content img {
            max-width: 100% !important;
            height: auto !important;
            display: block;
            margin: 0 auto;
            box-sizing: border-box;
        }
        .content table {
            max-width: 100% !important;
            width: 100% !important;
            box-sizing: border-box;
            overflow-x: auto;
        }
        .content {
            word-break: break-word;
        }



        .footer {
            background-color: #2a9c5d;
            color: #ffffff;
            text-align: center;
            padding: 15px;
            font-size: 10px;
        }

        .button-link {
            display: inline-block;
            padding: 8px 16px;
            font-size: 14px;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
            background-color: #00a65a;
        }
        .center-text {
            text-align: center;
        }

        .footer p {
            margin: 5px 0;
        }

        .footer .logo-container {
            display: flex;
            gap: 50px;
            justify-content: center;
            margin-bottom: 10px;
        }

        .footer img {
            max-width: 100px;
            margin: 10px;
        }

        .aviso {
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header with Image -->
        <div class="header">
            @yield('banner')
        </div>

        <!-- Content aligned to left -->
        <div class="content">
            @yield('mensagem')
            <!-- Signature -->

        </div>

        <!-- Footer with images -->
        <div class="footer" style="text-align: center; max-height: 60px; font-size: 7px; padding: 7px;">
            <div>
            <div class="logo-container" style="margin-bottom: 0px;">
            <a href="https://ifnmg.edu.br/almenara">
            <img src="{{ asset('/imagens/logoIFNMG.png') }}" alt="IFNMG Logo" style="margin: 0px;">
            </a>
            <a href="https://eventos.ifnmg.edu.br">
            <img src="{{ asset('/imagens/logo-pharus.png') }}" alt="Pharus Logo" style="margin: 0px;">
            </a>
            </div>
            </div>
            <p>Copyright © 2024 IFNMG. Todos direitos reservados.</p>
        </div>

    </div>
    <div class="aviso">
        <p>Este é um email automático! Por favor, não responda.</p>
    </div>
</body>

</html>
