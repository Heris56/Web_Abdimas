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
    <link rel="stylesheet" href="{{ asset('css/dashboard-guru-mapel.css') }}">

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
        <div class="Tabs d-flex align-items-center">
            <input type="text" class="form-control me-auto" id="cariSiswa" name="cariSiswa" placeholder="Cari Siswa">
            <div class="btns input-nilai me-3">
                <a href="{{ route('login-siswa') }}">
                    Input Nilai
                </a>
            </div>
            <div class="btns cetak-nilai">
                <a href="{{ route('login-siswa') }}">
                    Cetak Nilai
                </a>
            </div>
        </div>

        <div class="Contents">
            <!-- Table 1 -->
            <div class="header mb-2">
                <span class="head">XII A</span>
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">NISN/NIS</th>
                        <th scope="col">Nama Siswa</th>
                        <th scope="col">Quiz 1</th>
                        <th scope="col">Quiz 2</th>
                        <th scope="col">UTS</th>
                        <th scope="col">UAS</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>0056789012</td>
                        <td>Amanda Putri</td>
                        <td>85</td>
                        <td>88</td>
                        <td>82</td>
                        <td>90</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>0056789013</td>
                        <td>Bagas Pratama</td>
                        <td>78</td>
                        <td>80</td>
                        <td>75</td>
                        <td>84</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>0056789014</td>
                        <td>Citra Lestari</td>
                        <td>92</td>
                        <td>95</td>
                        <td>89</td>
                        <td>93</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>0056789015</td>
                        <td>Dimas Aditya</td>
                        <td>70</td>
                        <td>72</td>
                        <td>68</td>
                        <td>74</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>0056789016</td>
                        <td>Elvira Sari</td>
                        <td>88</td>
                        <td>90</td>
                        <td>85</td>
                        <td>91</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="Profile">
            <div class="head text-center">Daftar Kelas</div>

            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td>XII A</td>
                    </tr>
                    <tr>
                        <td>XII B</td>
                    </tr>
                    <tr>
                        <td>XI A</td>
                    </tr>
                    <tr>
                        <td>XI B</td>
                    </tr>
                    <tr>
                        <td>X A</td>
                    </tr>
                    <tr>
                        <td>X B</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Connect Bootsrap bundle-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>

        <!-- Connect Custom JS -->
        <script src="{{ asset('js/darryl.js') }}"></script>
</body>

</html>