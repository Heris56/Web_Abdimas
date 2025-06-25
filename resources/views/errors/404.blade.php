<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found</title>
    @vite(['resources/css/app.css'])
</head>

<body>
    <style>
        html,
        body {
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
            font-family: var(--font-body);
            font-size: var(--font-size-base);
            background-color: var(--background);
            color: var(--background);
        }
    </style>

    <div class="error-container">
        <h1 class="error-code">404</h1>
        <h2 class="error-message">Oops! Page Not Found</h2>
        <p class="error-description mb-2">The page you're looking for doesn't exist or has been moved.</p>
        <a href="{{ url('/') }}" class="button btn-destructive">Back to Home</a>
        <div class="error-graphic"></div>
    </div>
</body>

</html>
