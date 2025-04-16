<?php
session_start();
include '../includes/db.php';

// Check if the user is logged in by verifying the session
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $plan = mysqli_real_escape_string($conn, $_POST['plan']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $card = mysqli_real_escape_string($conn, $_POST['card']);
    $expiry = mysqli_real_escape_string($conn, $_POST['expiry']);
    $cvv = mysqli_real_escape_string($conn, $_POST['cvv']);
    
    // Retrieve the user_id from the session
    $user_id = $_SESSION['user_id'];

    // Prepare and execute the query to insert data including the user_id
    $stmt = $conn->prepare("INSERT INTO subscriptions (plan, name, email, card, expiry, cvv, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $plan, $name, $email, $card, $expiry, $cvv, $user_id);

    $success = $stmt->execute();

    $stmt->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="../assets/images/logo.png" rel="icon" type="image/png" style="zoom: 15.0;">
  <title>InvestSmart-Subscribe Now</title>

  <style>
    * {
      box-sizing: border-box;
      font-family: 'Comfortaa', sans-serif;
    }

    body {
      margin: 0;
      background-color: #EAE7DC;;
      color: white;
      min-height: 100vh;
      padding: 0;
    }

    .checkout-container {
      background: #1e2a38;
      padding: 30px;
      border-radius: 12px;
      width: 90%;
      max-width: 1300px;
      box-shadow: 0 0 20px rgba(0,0,0,0.5);
      margin: 40px auto;
    }

    .checkout-container h2 {
      margin-bottom: 20px;
      text-align: center;
    }

    .form-group {
      margin-bottom: 30px;
    }

    .form-group label {
      display: block;
      margin-bottom: 6px;
      font-weight: bold;
    }

    .form-group input,
    .form-group select {
      width: 100%;
      padding: 10px;
      border: none;
      border-radius: 6px;
      background-color: #fff;
      color: #000;
    }

    .form-group input:focus {
      outline: 2px solid #f5a623;
    }

    .checkout-btn {
      background: #f5a623;
      color: white;
      padding: 12px;
      width: 100%;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .checkout-btn:hover {
      background: #e59410;
    }

    .secure {
      margin-top: 15px;
      font-size: 12px;
      color: #ccc;
      text-align: center;
    }
    
    .popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #1b2735;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            z-index: 1000;
            display: none;
            box-shadow: 0 0 20px #0ff;
        }
        .popup h2 {
            color: #f5a623;
            margin-bottom: 10px;
        }
        .popup p {
            font-size: 18px;
        }
        .popup.show {
            display: block;
        }
  </style>
</head>
<body style="background-color: #EAE7DC;">

<?php include '../includes/header.php'; ?>
<br>
<br>
<br>
<br>
<div class="checkout-container">
<h2 style="color: white;" >Subscribe to Premium</h2>
  <form method="POST" action="buy.php">
    <div class="form-group">
      <label for="plan" style="color: white;">Choose Plan</label>
      <select id="plan" name="plan" required>
        <option value="">Select a plan</option>
        <option value="starter">Starter - $1 / mo</option>
        <option value="pro">Pro Investor - $29 / mo</option>
        <option value="expert">Expert Analyst - $49 / mo</option>
        <option value="ultimate">Ultimate Package - $99 / mo</option>
      </select>
    </div>

    <div class="form-group">
      <label for="name" style="color: white;">Full Name</label>
      <input type="text" id="name" name="name" required placeholder="John Doe"/>
    </div>

    <div class="form-group">
      <label for="email" style="color: white;">Email Address</label>
      <input type="email" id="email" name="email" required placeholder="you@example.com"/>
    </div>

    <div class="form-group">
      <label for="card" style="color: white;">Credit Card Number</label>
      <input type="text" id="card" name="card" required placeholder="1234 5678 9012 3456"/>
    </div>

    <div class="form-group">
      <label for="expiry" style="color: white;">Expiration Date</label>
      <input type="text" id="expiry" name="expiry" required placeholder="MM/YY"/>
    </div>

    <div class="form-group">
      <label for="cvv" style="color: white;">CVV</label>
      <input type="text" id="cvv" name="cvv" required placeholder="123"/>
    </div>

    <button type="submit" class="checkout-btn">Confirm and Subscribe</button>
    <p class="secure">🔒 Your payment is secure and encrypted.</p>

    <?php if (isset($success) && $success): ?>
    <div class="popup show" id="confirmationPopup">
        <h2>🎉 Subscription Confirmed!</h2>
        <p style="color: #ccc;">Your plan details have been securely submitted.<br>Confirmation has been sent to the admin for review.</p>
    </div>
<?php endif; ?>
<script>
    setTimeout(() => {
        const popup = document.getElementById("confirmationPopup");
        if (popup) popup.classList.remove("show");
    }, 5000); // Hide after 5 seconds
</script>
  </form>
</div>

</body>
</html>
<?php include '../includes/footer.php'; ?>
