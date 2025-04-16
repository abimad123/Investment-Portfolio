<?php 
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: user/dashboard.php");
    exit;
}

include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Check if email exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    if (!$stmt) {
        die("❌ Database query preparation failed: " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result) {
        die("❌ Database query execution failed: " . $conn->error);
    }
    $user = $result->fetch_assoc();

    if ($user) {
        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Password is correct
            $_SESSION['user_id'] = $user['user_id'];
            header("Location: user/dashboard.php");
            exit;
        } else {
            // Password is incorrect
            $error_message = "❌ Invalid password.";
        }
    } else {
        // Email not found
        $error_message = "❌ No account found with that email.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="assets/images/logo.png" rel="icon" type="image/png">
    <title>InvestSmart - Sign In</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #93f9ca;
            --secondary-color: #ff6219;
            --text-color: #393f81;
        }
        
        body {
            background-color: var(--primary-color);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .login-card {
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .login-image {
            height: 100%;
            object-fit: cover;
            border-radius: 1rem 0 0 1rem;
        }
        
        .form-control-lg {
            padding: 1rem 1.5rem;
            font-size: 1rem;
        }
        
        .btn-login {
            background-color: #2c3e50;
            border: none;
            padding: 0.8rem;
            font-size: 1.1rem;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            background-color: #1a252f;
            transform: translateY(-2px);
        }
        
        .brand-logo {
            color: var(--secondary-color);
            font-size: 2rem;
        }
        
        @media (max-width: 767.98px) {
            .login-image-container {
                display: none;
            }
            
            .login-card {
                border-radius: 1rem;
            }
            
            .login-form-container {
                padding: 2rem 1.5rem;
            }
        }
        
        /* Floating labels */
        .form-outline {
            position: relative;
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
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="row justify-content-center align-items-center">
            <div class="col-12 col-lg-10">
                <div class="card login-card">
                    <div class="row g-0">
                        <!-- Image Column - Hidden on mobile -->
                        <div class="col-md-6 login-image-container">
                            <img src="assets/images/index.png" alt="InvestSmart" class="img-fluid login-image w-100 h-100">
                        </div>
                        
                        <!-- Form Column -->
                        <div class="col-md-6">
                            <div class="card-body p-4 p-lg-5 login-form-container">
                                <form method="POST" action="index.php">
                                    <!-- Brand Logo -->
                                    <div class="d-flex align-items-center mb-4">
                                       
                                        <span class="h2 fw-bold mb-0">InvestSmart</span>
                                    </div>
                                    
                                    <h5 class="fw-normal mb-4" style="letter-spacing: 1px;">Sign in to your account</h5>
                                    
                                    <!-- Error Message -->
                                    <?php if (isset($error_message)): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <?php echo $error_message; ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Email Input -->
                                    <div class="form-outline mb-4">
                                        <input type="email" name="email" id="email" class="form-control form-control-lg" 
                                               required autocomplete="email" placeholder=" " />
                                        <label class="form-label" for="email">Email address</label>
                                    </div>
                                    
                                    <!-- Password Input -->
                                    <div class="form-outline mb-4">
                                        <input type="password" name="password" id="password" class="form-control form-control-lg" 
                                               required autocomplete="current-password" placeholder=" " />
                                        <label class="form-label" for="password">Password</label>
                                    </div>
                                    
                                    <!-- Remember Me & Forgot Password -->
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="remember">
                                            <label class="form-check-label" for="remember">Remember me</label>
                                        </div>
                                        <a href="auth/forget.php" class="text-primary">Forgot password?</a>
                                    </div>
                                    
                                    <!-- Login Button -->
                                    <div class="pt-1 mb-4">
                                        <button class="btn btn-dark btn-lg btn-block w-100 btn-login" type="submit">
                                            <i class="fas fa-sign-in-alt me-2"></i> Login
                                        </button>
                                    </div>
                                    
                                    <!-- Register Link -->
                                    <p class="mb-4 text-center" style="color: var(--text-color);">
                                        Don't have an account? 
                                        <a href="auth/register.php" class="fw-bold">Register here</a>
                                    </p>
                                    
                                    <!-- Divider
                                    <div class="divider d-flex align-items-center my-4">
                                        <p class="text-center fw-bold mx-3 mb-0 text-muted">OR</p>
                                    </div>
                                    
                                    <!-- Social Login Buttons -->
                                    <!-- <div class="d-flex justify-content-center mb-4">
                                        <button type="button" class="btn btn-outline-primary btn-floating mx-1">
                                            <i class="fab fa-google"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-primary btn-floating mx-1">
                                            <i class="fab fa-facebook-f"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-primary btn-floating mx-1">
                                            <i class="fab fa-twitter"></i>
                                        </button>
                                    </div> -->
                                    
                                    <!-- Footer Links -->
                                    <div class="d-flex justify-content-center small">
                                        <a href="#" class="text-muted me-3">Terms of use</a>
                                        <a href="#" class="text-muted">Privacy policy</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Activate floating labels
        document.querySelectorAll('.form-outline input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentNode.querySelector('label').classList.add('active');
            });
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentNode.querySelector('label').classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>
