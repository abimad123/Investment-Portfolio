<?php
session_start();
include '../includes/db.php';

$message = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
    if (mysqli_query($conn, $sql)) {
        // Store user data in session for EmailJS
        $_SESSION['new_user_email'] = $email;
        $_SESSION['new_user_name'] = $username;
        
        $message = "<div class='alert alert-success'>✅ Registration successful! <a href='../index.php'>Login here</a></div>";
    } else {
        $message = "<div class='alert alert-danger'>❌ Error: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/images/logo.png" rel="icon" type="image/png" style="zoom: 15.0;">
    <title>InvestSmart-Sign Up</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- EmailJS SDK -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
    <script>
       
        emailjs.init('BzR5399e4ttCvjZHi');
        function sendWelcomeEmail(email, name) {
            emailjs.send('service_zpptr3a', 'template_18sdpm7', {
                to_name: '<?php echo $username; ?>',
                to_email: '<?php echo $email; ?>',
                 login_link: window.location.origin + '/index.php',
                 facebook_url: 'https://facebook.com',
                 twitter_url: 'https://twitter.com',
                instagram_url: 'https://instagram.com'
            }).then(
                function(response) {
                    console.log('Welcome email sent!', response.status, response.text);
                },
                function(error) {
                    console.error('Failed to send welcome email:', error);
                }
            );
        }
        
       
        window.onload = function() {
            <?php if (isset($_SESSION['new_user_email'])): ?>
                sendWelcomeEmail(
                    '<?php echo $_SESSION['new_user_email']; ?>',
                    '<?php echo $_SESSION['new_user_name']; ?>'
                );
                <?php 
                unset($_SESSION['new_user_email']); 
                unset($_SESSION['new_user_name']);
                ?>
            <?php endif; ?>
        };
    </script>
    <style>
        .form-outline input:focus + label,
        .form-outline input:not(:placeholder-shown) + label {
            transform: translateY(-1.1rem) scale(0.8);
            background-color: white;
            padding: 0 5px;
            margin-left: 10px;
        }
        .form-outline label {
            position: absolute;
            top: 10px;
            left: 15px;
            transition: all 0.3s;
            pointer-events: none;
        }
        .form-outline {
            position: relative;
        }
    </style>
</head>
<body>
    <section class="vh-100" style="background-color:#93f9ca">
        <div class="container h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-lg-12 col-xl-11">
                    <div class="card text-black" style="border-radius: 25px;">
                        <div class="card-body p-md-5">
                            <div class="row justify-content-center">
                                
                                <!-- Form Section (Left Side) -->
                                <div class="col-md-10 col-lg-6 col-xl-5 order-2 order-lg-1">
                                    <p class="text-center h1 fw-bold mb-5 mx-1 mx-md-4 mt-4">Sign up</p>

                                    <!-- Display Success/Error Message -->
                                    <?php if (!empty($message)) echo $message; ?>

                                    <form class="mx-1 mx-md-4" method="POST" action="register.php">
                                        <div class="d-flex flex-row align-items-center mb-4">
                                            <i class="fas fa-user fa-lg me-3 fa-fw"></i>
                                            <div class="form-outline flex-fill mb-0">
                                                <input type="text" name="username" class="form-control" required placeholder=" " />
                                                <label class="form-label">Your Name</label>
                                            </div>
                                        </div>

                                        <div class="d-flex flex-row align-items-center mb-4">
                                            <i class="fas fa-envelope fa-lg me-3 fa-fw"></i>
                                            <div class="form-outline flex-fill mb-0">
                                                <input type="email" name="email" class="form-control" required placeholder=" " />
                                                <label class="form-label">Your Email</label>
                                            </div>
                                        </div>

                                        <div class="d-flex flex-row align-items-center mb-4">
                                            <i class="fas fa-lock fa-lg me-3 fa-fw"></i>
                                            <div class="form-outline flex-fill mb-0">
                                                <input type="password" name="password" class="form-control" required placeholder=" " />
                                                <label class="form-label">Password</label>
                                            </div>
                                        </div>

                                        <div class="form-check d-flex justify-content-center mb-5">
                                            <input class="form-check-input me-2" type="checkbox" required />
                                            <label class="form-check-label">
                                                I agree to the <a href="#">Terms of service</a>
                                            </label>
                                        </div>

                                        <div class="d-flex justify-content-center mx-4 mb-3 mb-lg-4">
                                            <button type="submit" class="btn btn-primary btn-lg">Register</button>
                                        </div>

                                        <p class="mb-5 pb-lg-2 text-center" style="color: #393f81;">
                                            Already have an account? <a href="../index.php" style="color: #393f81;">Login here</a>
                                        </p>
                                    </form>
                                </div>

                                <!-- Image Section (Right Side) -->
                                <div class="col-md-10 col-lg-6 col-xl-7 d-flex align-items-center order-1 order-lg-2">
                                    <img src="../assets/images/index.png" class="img-fluid" alt="Registration Image">
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>