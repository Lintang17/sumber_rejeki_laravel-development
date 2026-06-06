<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN</title>
    <link rel="stylesheet" href="{{ asset('assets/login/style.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.css">
    <link rel="shortcut icon" href="{{ url('/') }}/assets/logo.png" />
</head>

<body>
    <div class="wrapper">
        <form action="{{ url('loginproses') }}" method="post">
            @csrf
            <h2>Login</h2>
            <div class="input-field">
                <input type="text" name="email" required>
                <label>Email</label>
            </div>
            <div class="input-field"> 
                <input type="password" name="password" required>
                <label>Password</label>
            </div>
            <br><br>
            <button type="submit" name="simpan" value="Masuk">Log In</button>
            {{-- <div class="register">
                <p>Tidak Punya Akun ? <a href="{{ url('daftar') }}">Daftar</a></p>
            </div> --}}
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.all.min.js"></script>

    @if (session('success'))
    <script>
        Swal.fire({
            title: "Sukses!",
            text: "{{ session('success') }}",
            icon: "success",
            timer: 2000,
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false
        });
    </script>
    @endif

    @if (session('error'))
    <script>
        Swal.fire({
            title: "Oops!",
            text: "{{ session('error') }}",
            icon: "error",
            timer: 2000,
            showConfirmButton: false
        });
    </script>
    @endif

</body>

</html>