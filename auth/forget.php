<?php
session_start();
require_once '../includes/db.php';
$email = $new_password = $confirm_password = "";
$email_err = $new_password_err = $confirm_password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
    }

    if (empty(trim($_POST["new_password"]))) {
        $new_password_err = "Please enter the new password.";
    } elseif (strlen(trim($_POST["new_password"])) < 5) {
        $new_password_err = "Password must have at least 5 characters.";
    } else {
        $new_password = trim($_POST["new_password"]);
    }

    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm the password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($new_password_err) && ($new_password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    if (empty($email_err) && empty($new_password_err) && empty($confirm_password_err)) {
        $sql = "UPDATE users SET password = ? WHERE email = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ss", $param_password, $param_email);
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_email = $email;
            if (mysqli_stmt_execute($stmt)) {
                header("location: ../index.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/images/logo.png" rel="icon" type="image/png" style="zoom: 15.0;">
    <title>InvestSmart-Change Password</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-bg: #93f9ca;
            --accent-color: #ff6219;
            --card-radius: 1rem;
        }
        
        body {
            background-color: var(--primary-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .password-card {
            border-radius: var(--card-radius);
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .login-image {
            height: 100%;
            object-fit: cover;
            border-radius: var(--card-radius) 0 0 var(--card-radius);
        }
        
        .form-control-lg {
            padding: 1rem 1.5rem;
        }
        
        .btn-password {
            background-color: #2c3e50;
            border: none;
            padding: 0.8rem;
            font-size: 1.1rem;
            transition: all 0.3s;
            width: 100%;
        }
        
        .btn-password:hover {
            background-color: #1a252f;
            transform: translateY(-2px);
        }
        
        .brand-icon {
            color: var(--accent-color);
            font-size: 2rem;
        }
        
        /* Floating labels */
        .form-outline {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .form-outline input:focus + label,
        .form-outline input:not(:placeholder-shown) + label {
            transform: translateY(-1.1rem) scale(0.8);
            background-color: white;
            padding: 0 5px;
            margin-left: 10px;
        }
        
        .form-outline label {
            position: absolute;
            top: 1rem;
            left: 1.5rem;
            transition: all 0.3s;
            pointer-events: none;
            color: #6c757d;
        }
        
        /* Password strength indicator */
        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .login-image-col {
                display: none;
            }
            
            .password-card {
                border-radius: var(--card-radius);
            }
            
            .form-container {
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-10 col-lg-10 col-xl-10 mx-auto">
                    <div class="card password-card">
                        <div class="row g-0">
                            <!-- Image Column - Hidden on mobile -->
                            <div class="col-md-5 login-image-col">
                                <img src="../assets/images/login.webp" alt="InvestSmart Login" class="img-fluid login-image h-100">
                            </div>
                            
                            <!-- Form Column -->
                            <div class="col-md-7">
                                <div class="card-body p-4 p-lg-5 form-container">
                                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                        <!-- Brand Header -->
                                        <div class="d-flex align-items-center mb-4">
                                           
                                            <span class="h1 fw-bold mb-0">InvestSmart</span>
                                        </div>
                                        
                                        <h5 class="fw-normal mb-4" style="letter-spacing: 1px;">Change your password</h5>
                                        
                                        <!-- Error Message -->
                                        <?php if (isset($error_message)): ?>
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <?php echo $error_message; ?>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Success Message -->
                                        <?php if (isset($success_message)): ?>
                                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                <?php echo $success_message; ?>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Email Input -->
                                        <div class="form-outline">
                                            <input type="email" name="email" id="email" class="form-control form-control-lg" 
                                                   required autocomplete="email" placeholder=" " />
                                            <label class="form-label" for="email">Email address</label>
                                        </div>
                                        
                                        <!-- New Password Input -->
                                        <div class="form-outline">
                                            <input type="password" name="new_password" id="new_password" 
                                                   class="form-control form-control-lg" required 
                                                   placeholder=" " pattern=".{8,}" 
                                                   title="Minimum 8 characters" />
                                            <label class="form-label" for="new_password">New Password</label>
                                            <div class="password-strength" id="passwordStrength"></div>
                                            <small class="form-text text-muted">Minimum 8 characters</small>
                                        </div>
                                        
                                        <!-- Confirm Password Input -->
                                        <div class="form-outline mb-4">
                                            <input type="password" name="confirm_password" id="confirm_password" 
                                                   class="form-control form-control-lg" required 
                                                   placeholder=" " />
                                            <label class="form-label" for="confirm_password">Confirm Password</label>
                                            <div id="passwordMatch" class="small mt-1"></div>
                                        </div>
                                        
                                        <!-- Submit Button -->
                                        <div class="pt-1 mb-4">
                                            <button class="btn btn-dark btn-lg btn-password" type="submit" id="submitBtn">
                                                <i class="fas fa-key me-2"></i> Change Password
                                            </button>
                                        </div>
                                        
                                        <!-- Back to Login -->
                                        <p class="text-center mb-0">
                                            <a href="../index.php" class="text-primary">
                                                <i class="fas fa-arrow-left me-1"></i> Back to login
                                            </a>
                                        </p>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password strength indicator
        document.getElementById('new_password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrength');
            let strength = 0;
            
            // Check password length
            if (password.length >= 8) strength += 1;
            if (password.length >= 12) strength += 1;
            
            // Check for character variety
            if (/[A-Z]/.test(password)) strength += 1;
            if (/[0-9]/.test(password)) strength += 1;
            if (/[^A-Za-z0-9]/.test(password)) strength += 1;
            
            // Update strength bar
            switch(strength) {
                case 0:
                case 1:
                    strengthBar.style.width = '25%';
                    strengthBar.style.backgroundColor = '#dc3545';
                    break;
                case 2:
                case 3:
                    strengthBar.style.width = '50%';
                    strengthBar.style.backgroundColor = '#fd7e14';
                    break;
                case 4:
                    strengthBar.style.width = '75%';
                    strengthBar.style.backgroundColor = '#ffc107';
                    break;
                case 5:
                    strengthBar.style.width = '100%';
                    strengthBar.style.backgroundColor = '#28a745';
                    break;
            }
        });
        
        // Password match verification
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            const matchText = document.getElementById('passwordMatch');
            
            if (confirmPassword.length === 0) {
                matchText.textContent = '';
                matchText.style.color = '';
            } else if (password === confirmPassword) {
                matchText.textContent = 'Passwords match!';
                matchText.style.color = '#28a745';
            } else {
                matchText.textContent = 'Passwords do not match!';
                matchText.style.color = '#dc3545';
            }
        });
        
        // Initialize floating labels
        document.querySelectorAll('.form-outline input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentNode.querySelector('label').classList.add('active');
            });
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentNode.querySelector('label').classList.remove('active');
                }
            });
            // Initialize labels if values exist (for form repopulation)
            if (input.value) {
                input.parentNode.querySelector('label').classList.add('active');
            }
        });
    </script>
</body>
</html>
