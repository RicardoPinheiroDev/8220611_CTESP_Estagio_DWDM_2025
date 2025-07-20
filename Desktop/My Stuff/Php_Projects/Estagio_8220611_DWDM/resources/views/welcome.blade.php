<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to the Platform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
       <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            text-align: center;
        }

        h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        p {
            font-size: 1.2rem;
            max-width: 600px;
        }

        .routes {
            margin-top: 2rem;
        }

        .routes a {
            display: block;
            color: #00d4ff;
            text-decoration: none;
            margin: 0.5rem 0;
            font-weight: bold;
        }

        .routes a:hover {
            text-decoration: underline;
        }
    </style>

    <h1> Welcome to the digiUp Área Cliente</h1>
    <p>This page redirects you to diffrent routes of the plataform.Use your own credentials</p>

    <div class="routes">
        <a href="/client/register">Register as a Client</a>
        <a href="/admin/login">Admin Login</a>
        <a href="/team/login">Team Member Login</a>
    </div>
</body>
</html>
