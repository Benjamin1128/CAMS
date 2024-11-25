<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classroom Attendance Management System</title>
    <link rel="stylesheet" href="<?php echo base_url('assets/dist/css/adminlte.min.css');?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/plugins/fontawesome-free/css/all.min.css');?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css');?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .login-page {
            background: #f4f6f9;
        }
        .login-box {
            width: 360px;
            margin: 7% auto;
        }
        .login-logo {
            margin-bottom: 20px;
            color: #333;
        }
    </style>
    <body class="login-page">
        <div class="login-box">
            <div class="login-logo">Classroom Attendance Management System</div>
            <div class="card">
                <div class="card-body login-card-body">
                    <p class="login-box-msg">Sign in to start your session</p>
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message?></div>
                    <?php endif; ?>
                    <form action="<?php echo site_url('userLogin'); ?>" method="POST">
                        <div class="input-group mb-3">
                            <input type="text" name="username" class="form-control" placeholder="Username" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                            </div>
                        </div>
                    </form>

                    <!-- <div class="text-center">
                        <a href="">Forget Password</a>
                    </div> -->
                </div>
            </div>
        </div>
    </body>
</head>
</html>