<?php
session_start();
require 'function.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM login WHERE email='$email' AND password='$password'");
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        $_SESSION['login'] = true;
        $_SESSION['email'] = $email;
        $_SESSION['level'] = $data['level'];
        $_SESSION['user_id'] = $data['iduser'];
        $_SESSION['cabang_id'] = $data['cabang_id'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Email atau password salah!";
    }
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PLN ULP Inventaris Material</title>

    <!-- Font Awesome untuk icon -->
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>

<body>
    <div id="layoutAuthentication">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-6 col-sm-8">
                    <div class="card shadow-lg border-0 rounded-lg">
                        <div class="card-header">
                            <img src="assets/img/logo.jpg"
                                alt="Logo PLN"
                                onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\' viewBox=\'0 0 100 100\'%3E%3Crect width=\'100\' height=\'100\' fill=\'%234361ee\'/%3E%3Ctext x=\'50\' y=\'65\' font-size=\'40\' text-anchor=\'middle\' fill=\'white\' font-family=\'Arial\'%3EPLN%3C/text%3E%3C/svg%3E'">
                            <h4>PLN ULP Inventaris Material</h4>
                        </div>

                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <?php echo $error; ?>
                                </div>
                            <?php endif; ?>

                            <form method="post" id="loginForm">
                                <div class="form-group">
                                    <label for="email">
                                        <i class="fas fa-envelope"></i>
                                        Email
                                    </label>
                                    <div class="input-group">
                                        <i class="fas fa-envelope"></i>
                                        <input class="form-control"
                                            id="email"
                                            name="email"
                                            type="email"
                                            placeholder="Masukkan email"
                                            required
                                            autocomplete="off">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="password">
                                        <i class="fas fa-lock"></i>
                                        Password
                                    </label>
                                    <div class="input-group">
                                        <i class="fas fa-lock"></i>
                                        <input class="form-control"
                                            id="password"
                                            name="password"
                                            type="password"
                                            placeholder="Masukkan kata sandi"
                                            required
                                            autocomplete="off">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary" name="login">
                                    <i class="fas fa-sign-in-alt"></i>
                                    Masuk
                                </button>
                            </form>

                            <div class="text-muted">
                                <small>
                                    <i class="far fa-copyright"></i>
                                    <?php echo date('Y'); ?> PLN ULP - All rights reserved
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Animasi focus pada input
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.closest('.form-group').classList.add('focused');
            });

            input.addEventListener('blur', function() {
                if (this.value === '') {
                    this.closest('.form-group').classList.remove('focused');
                }
            });
        });

        // Prevent form resubmission on refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>

</html>