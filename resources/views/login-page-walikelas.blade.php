<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login wali kelas</title>
    <!-- Conect CSS bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />

    <!-- Connect CSS -->
    <link rel="stylesheet" href="{{ asset('css/login-page.css') }}">

    <style>
        .container1 {
            padding-top: 100px;
            min-height: 100vh;
        }

        #form-box {
            max-width: 500px;
            width: 100%;
        }
    </style>
</head>

<body>
    <x-navbar></x-navbar>
    <div class="container1">
        <div class="container-sm border w-100 w-md-75 w-lg-50" id="form-box">
            @if (session('error'))
            <div class="alert alert-danger">
                {{session('error')}}
            </div>
            @endif
            <div>Halo ini wali kelas</div>
        </div>
    </div>
</body>

</html>