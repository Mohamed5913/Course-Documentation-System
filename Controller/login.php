<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Registration</title>
    <link rel="stylesheet" href="../View/loginstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <!-- Login Form -->
            <section id="login-section" class="form-section">
                <h2>Login</h2>
                <form action="process_login.php" method="POST">
                    <div class="form-group">
                        <label for="login-username"><i class="fas fa-user"></i> Username:</label>
                        <input type="text" id="login-username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="login-password"><i class="fas fa-lock"></i> Password:</label>
                        <input type="password" id="login-password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="login-role"><i class="fas fa-users"></i> Login as:</label>
                        <select id="login-role" name="role" required>
                            <option value="student">Student</option>
                            <option value="instructor">Instructor</option>
                        </select>
                    </div>
                    <button type="submit" class="btn">Login</button>
                </form>
            </section>

            <div class="divider">
                <span>OR</span>
            </div>

            <!-- Registration Form -->
            <section id="register-section" class="form-section">
                <h2>Register</h2>
                <form action="process_register.php" method="POST">
                    <div class="form-group">
                        <label for="register-username"><i class="fas fa-user"></i> Username:</label>
                        <input type="text" id="register-username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="register-password"><i class="fas fa-lock"></i> Password:</label>
                        <input type="password" id="register-password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="register-role"><i class="fas fa-users"></i> Register as:</label>
                        <select id="register-role" name="role" required>
                            <option value="student">Student</option>
                            <option value="instructor">Instructor</option>
                        </select>
                    </div>
                    <button type="submit" class="btn">Register</button>
                </form>
            </section>

            <!-- Home Redirection Button -->
            <section id="home-redirect">
                <button onclick="window.location.href='../View/Home.html'" class="btn home-btn">
                    <i class="fas fa-home"></i> Go to Homepage
                </button>
            </section>
        </div>
    </div>
</body>
</html>