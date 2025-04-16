<?php
ob_start(); // Start output buffering
session_start();
include '../includes/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$query = "SELECT username, email, phone, dob, address, job, gender, profile_picture_name FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $query);

if (!$stmt) {
    die("Prepare failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Profile picture logic
$default_avatar = ($user['gender'] === 'female') ? '../assets/images/female.png' : '../assets/images/male.png';
$profile_picture = !empty($user['profile_picture_name']) ? 'uploads/' . $user['profile_picture_name'] : $default_avatar;

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $job = mysqli_real_escape_string($conn, $_POST['job']);
    $gender = $_POST['gender'];
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $dob = $_POST['dob'];

    if (!empty($_FILES["profile_picture"]["name"])) {
        $target_dir = "uploads/";
        $profile_picture_name = basename($_FILES["profile_picture"]["name"]);
        $target_file = $target_dir . $profile_picture_name;

        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            $update_query = "UPDATE users SET address=?, job=?, gender=?, profile_picture_name=?, phone=?, dob=? WHERE user_id=?";
            $stmt = mysqli_prepare($conn, $update_query);
            if (!$stmt) {
                die("Prepare failed: " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt, "ssssssi", $address, $job, $gender, $profile_picture_name, $phone, $dob, $user_id);
        } else {
            echo "Error uploading profile picture.";
            exit;
        }
    } else {
        $update_query = "UPDATE users SET address=?, job=?, gender=?, phone=?, dob=? WHERE user_id=?";
        $stmt = mysqli_prepare($conn, $update_query);
        if (!$stmt) {
            die("Prepare failed: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt, "sssssi", $address, $job, $gender, $phone, $dob, $user_id);
    }

    if (mysqli_stmt_execute($stmt)) {
        ob_end_clean(); // Clear output buffer
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Error updating profile: " . mysqli_error($conn);
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InvestSmart-Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
    font-family: 'Poppins', sans-serif;
    background-color: #EAE7DC;
    margin: 0;
    padding: 0;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background-color:#EAE7DC;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }

        .left-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            margin-bottom: 10px;
        }

        .right-section {
            flex: 1;
        }

        .info-box {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            background-color: #f8f8f8;
            padding: 10px;
            border-radius: 5px;
        }

        .info-box label {
            width: 120px;
            font-weight: bold;
            color: #555;
        }

        .info-box input,
        .info-box select {
            flex: 1;
            border: none;
            background-color: transparent;
            outline: none;
            padding: 5px;
        }

        .btn-submit {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-submit:hover {
            background-color: #0056b3;
        }

        @media (min-width: 768px) 
        
        {
            .navbar {
                padding: 10px 10px;
            }

            .search-bar {
                width: 100%;
                margin-bottom: 10px;
            }
            .container {
                flex-direction: row;
            }

            .left-section {
                width: 200px;
                margin-right: 20px;
                margin-bottom: 0;
            }
        }
    </style>
</head>
<body>
<br><br><br><br><br>
<br>
<div class="container">
    <div class="left-section">
    <br>
    <br>
        <img src="../assets/images/male.png" class="profile-pic" id="profileIcon" alt="Profile">
       
        <br>
        <p class="username"><?php echo htmlspecialchars($user['username']); ?></p>
        <br>
        <br>
        <a href="../auth/forget.php" class="text-dark text-decoration-none"><strong>Change Password</strong></a>
    </div>

    <div class="right-section">
    <br>
    <br>
        <form method="POST" enctype="multipart/form-data">
            <div class="info-box">
                <label>Username</label>
                <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
            </div>
            <div class="info-box">
                <label>Email</label>
                <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
            </div>
            <div class="info-box">
                <label>Phone</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
            </div>
            <div class="info-box">
                <label>Date of Birth</label>
                <input type="date" name="dob" value="<?php echo htmlspecialchars($user['dob']); ?>">
            </div>
            <div class="info-box">
                <label>Address</label>
                <input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>">
            </div>
            <div class="info-box">
                <label>Job</label>
                <input type="text" name="job" value="<?php echo htmlspecialchars($user['job']); ?>">
            </div>
            <div class="info-box">
                <label>Gender</label>
                <select name="gender">
                    <option value="Male" <?php if ($user['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if ($user['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                </select>
            </div>

            <button type="submit" class="btn-submit">Update Profile</button>
        </form>
    </div>
</div>
<br><br>


</body>
</html>
<?php include '../includes/footer.php'; ?>