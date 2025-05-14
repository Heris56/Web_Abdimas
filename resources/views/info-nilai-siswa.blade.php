<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Masuk ke SMK Telkom</title>
    <!-- External buat background -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/particlesjs/2.2.2/particles.min.js"></script>

    <!-- Conect CSS bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />

    <!-- Connect CSS -->
    <link rel="stylesheet" href="{{ asset('css/info-siswa.css') }}">

    <!-- Import Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar container-fluid fixed-top">

        <!-- navigate to home/dashboard by clicking logo/name -->
        <a class="logo" href="{{ route('landing') }}">
            <img src="{{ asset('images/logo_pgri.png') }}" alt="Logo" width="64" height="64" class="logo-img d-inline-block" />
            SMK PGRI 35
        </a>

        <!-- Button login/register -->
        <div class="navbar-button ms-auto">
            <a href="{{ route('login-siswa') }}">
                Login
            </a>
        </div>
    </nav>

    <div class="content-wrapper container-fluid">
        <div class="Tabs">
            <ul class="nav nav-pills justify-content-center">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('info.presensi') }}">Presensi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="{{ route('info.nilai') }}">Nilai</a>
                </li>
            </ul>
        </div>
        <div class="Contents">
            <!-- Table 1 -->
            <div class="header mb-2">
                <span class="head">Semester 1</span>
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Mata Pelajaran</th>
                        <th scope="col">Quiz 1</th>
                        <th scope="col">Quiz 2</th>
                        <th scope="col">UTS</th>
                        <th scope="col">UAS</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">1</th>
                        <td>Matematika</td>
                        <td>85</td>
                        <td>90</td>
                        <td>78</td>
                        <td>88</td>
                    </tr>
                    <tr>
                        <th scope="row">2</th>
                        <td>Bahasa Indonesia</td>
                        <td>92</td>
                        <td>87</td>
                        <td>80</td>
                        <td>85</td>
                    </tr>
                    <tr>
                        <th scope="row">3</th>
                        <td>Bahasa Inggris</td>
                        <td>75</td>
                        <td>80</td>
                        <td>82</td>
                        <td>79</td>
                    </tr>
                    <tr>
                        <th scope="row">4</th>
                        <td>Fisika</td>
                        <td>88</td>
                        <td>84</td>
                        <td>76</td>
                        <td>81</td>
                    </tr>
                    <tr>
                        <th scope="row">5</th>
                        <td>Kimia</td>
                        <td>79</td>
                        <td>83</td>
                        <td>77</td>
                        <td>80</td>
                    </tr>
                </tbody>
            </table>

            <!-- Table 2 -->
            <div class="header mb-2">
                <span class="head">Semester 2</span>
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Mata Pelajaran</th>
                        <th scope="col">Quiz 1</th>
                        <th scope="col">Quiz 2</th>
                        <th scope="col">UTS</th>
                        <th scope="col">UAS</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">1</th>
                        <td>Matematika</td>
                        <td>85</td>
                        <td>90</td>
                        <td>78</td>
                        <td>88</td>
                    </tr>
                    <tr>
                        <th scope="row">2</th>
                        <td>Bahasa Indonesia</td>
                        <td>92</td>
                        <td>87</td>
                        <td>80</td>
                        <td>85</td>
                    </tr>
                    <tr>
                        <th scope="row">3</th>
                        <td>Bahasa Inggris</td>
                        <td>75</td>
                        <td>80</td>
                        <td>82</td>
                        <td>79</td>
                    </tr>
                    <tr>
                        <th scope="row">4</th>
                        <td>Fisika</td>
                        <td>88</td>
                        <td>84</td>
                        <td>76</td>
                        <td>81</td>
                    </tr>
                    <tr>
                        <th scope="row">5</th>
                        <td>Kimia</td>
                        <td>79</td>
                        <td>83</td>
                        <td>77</td>
                        <td>80</td>
                    </tr>
                </tbody>
            </table>

            <!-- Table 3 -->
            <div class="header mb-2">
                <span class="head">Semester 3</span>
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Mata Pelajaran</th>
                        <th scope="col">Quiz 1</th>
                        <th scope="col">Quiz 2</th>
                        <th scope="col">UTS</th>
                        <th scope="col">UAS</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">1</th>
                        <td>Matematika</td>
                        <td>85</td>
                        <td>90</td>
                        <td>78</td>
                        <td>88</td>
                    </tr>
                    <tr>
                        <th scope="row">2</th>
                        <td>Bahasa Indonesia</td>
                        <td>92</td>
                        <td>87</td>
                        <td>80</td>
                        <td>85</td>
                    </tr>
                    <tr>
                        <th scope="row">3</th>
                        <td>Bahasa Inggris</td>
                        <td>75</td>
                        <td>80</td>
                        <td>82</td>
                        <td>79</td>
                    </tr>
                    <tr>
                        <th scope="row">4</th>
                        <td>Fisika</td>
                        <td>88</td>
                        <td>84</td>
                        <td>76</td>
                        <td>81</td>
                    </tr>
                    <tr>
                        <th scope="row">5</th>
                        <td>Kimia</td>
                        <td>79</td>
                        <td>83</td>
                        <td>77</td>
                        <td>80</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="Profile">
            <div class="profile-card">
                <div class="card-content">
                    <!-- Avatar Circle -->
                    <div class="avatar-wrapper">
                        <div class="avatar">
                            <div class="avatar-inner">
                                <img src="{{ asset('images/profile.jpg') }}" alt="Profile Picture" class="avatar-img">
                            </div>
                            <div class="avatar-border"></div>
                        </div>
                    </div>

                    <!-- Profile Info -->
                    <div class="profile-info">
                        <h2 class="name">Teddy Aditya</h2>
                        <p class="title">13022222222</p>

                        <div class="stats">
                            <div class="stat">
                                <span class="stat-value">XII A</span>
                                <span class="stat-label">Kelas</span>
                            </div>
                            <div class="stat">
                                <span class="stat-value">2022</span>
                                <span class="stat-label">Angkatan</span>
                            </div>
                            <div class="stat">
                                <span class="stat-value">TKJ</span>
                                <span class="stat-label">Jurusan</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Connect Bootsrap bundle-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>

        <!-- Connect Custom JS -->
        <script src="{{ asset('js/darryl.js') }}"></script>
</body>

</html>