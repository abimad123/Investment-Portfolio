<?php
// Ensure session is started only once
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure correct path to db.php
include __DIR__ . '/db.php';

// Fetch user details
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Use correct column name: user_id instead of id
    $stmt = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
    
    // Check if prepare succeeded
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $username = $user['username'] ?? 'User';
} else {
    $username = 'Guest';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/images/logo.png" rel="icon" type="image/png">
    <title>InvestSmart</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #eef2ff;
            --secondary: #3f37c9;
            --dark: #1e1e1e;
            --light: #f8f9fa;
            --success: #4cc9f0;
            --warning: #f8961e;
            --danger: #f72585;
            --gray: #6c757d;
            --gray-light: #e9ecef;
        }
        
        .bherder {
            font-family: 'Inter', sans-serif;
            padding-top: 0px;
        }
        
        /* Modern Navbar */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 15px 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
        }
        
        .navbar-brand {
            display: flex;
            align-items: center;
            font-weight: 700;
            color: var(--dark);
            transition: transform 0.3s ease;
        }
        
        .navbar-brand:hover {
            transform: translateY(-2px);
        }
        
        .navbar-brand img {
            height: 32px;
            margin-right: 12px;
            transition: transform 0.3s ease;
        }
        
        .navbar-brand:hover img {
            transform: rotate(-5deg) scale(1.05);
        }
        
        .brand-text {
            font-family: 'Inter', sans-serif;
            font-size: 24px;
            font-weight: 800;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: -0.5px;
        }
        
        .nav-link {
            font-weight: 500;
            color: var(--dark);
            margin: 0 12px;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .nav-link:hover {
            color: var(--primary);
            background: var(--primary-light);
        }
        
        .nav-link.active {
            color: var(--primary);
            font-weight: 600;
        }
        
        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 12px;
            right: 12px;
            height: 3px;
            background: var(--primary);
            border-radius: 3px;
        }
        
        /* Search Bar */
        .search-container {
            position: relative;
            margin: 0 20px;
            flex-grow: 1;
            max-width: 500px;
        }
        
        .search-bar {
            width: 100%;
            padding: 10px 20px 10px 45px;
            border-radius: 50px;
            border: 1px solid var(--gray-light);
            background: var(--light);
            font-size: 14px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.03);
        }
        
        .search-bar:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
        }
        
        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }
        
        /* Action Buttons */
        .action-btn {
            position: relative;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: var(--light);
            color: var(--dark);
            margin: 0 8px;
            transition: all 0.3s ease;
            border: none;
        }
        
        .action-btn:hover {
            background: var(--primary-light);
            color: var(--primary);
            transform: translateY(-2px);
        }
        
        .action-btn .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            font-size: 10px;
            padding: 4px 6px;
            border-radius: 50%;
            background: var(--danger);
            color: white;
        }
        
        /* Profile Dropdown */
        .profile-dropdown {
            position: relative;
            margin-left: 15px;
        }
        
        .profile-btn {
            display: flex;
            align-items: center;
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        
        .profile-btn:hover {
            background: var(--primary-light);
        }
        
        .profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-light);
            transition: all 0.3s ease;
        }
        
        .profile-btn:hover .profile-img {
            border-color: var(--primary);
            transform: scale(1.05);
        }
        
        .username {
            margin: 0 10px;
            font-weight: 600;
            color: var(--dark);
            display: none;
        }
        
        .dropdown-arrow {
            color: var(--gray);
            transition: transform 0.3s ease;
        }
        
        .profile-btn:hover .dropdown-arrow {
            color: var(--primary);
        }
        
        .dropdown-menu {
            position: absolute;
            right: 0;
            top: 55px;
            min-width: 240px;
            padding: 10px 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(0, 0, 0, 0.05);
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s ease;
            z-index: 1001;
        }
        
        .dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .dropdown-header {
            padding: 10px 20px;
            border-bottom: 1px solid var(--gray-light);
        }
        
        .dropdown-item {
            padding: 10px 20px;
            color: var(--dark);
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
        }
        
        .dropdown-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
            color: var(--gray);
        }
        
        .dropdown-item:hover {
            background: var(--primary-light);
            color: var(--primary);
        }
        
        .dropdown-item:hover i {
            color: var(--primary);
        }
        
        .dropdown-divider {
            height: 1px;
            background: var(--gray-light);
            margin: 5px 0;
        }
        
        /* Mobile Responsiveness */
        @media (max-width: 992px) {
            .navbar {
                padding: 15px 20px;
            }
            
            .search-container {
                order: 3;
                width: 100%;
                margin: 15px 0 0 0;
                max-width: none;
            }
            
            .navbar-collapse {
                flex-direction: column;
            }
            
            .nav-links {
                order: 2;
                margin-top: 15px;
            }
            
            .action-buttons {
                order: 1;
                margin-left: auto;
            }
            
            .username {
                display: inline;
            }
        }
        
        @media (max-width: 768px) {
            .brand-text {
                font-size: 20px;
            }
            
            .navbar-brand img {
                height: 28px;
            }
        }
    </style>
</head>
<body class="bherder">

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="../user/dashboard.php">
            <img src="../assets/images/logo.png" alt="InvestSmart Logo" style="transform: scale(3.2);">
            <span class="brand-text" style="font-family: 'Times New Roman', serif; font-size: 24px;">InvestSmart</span>
        </a>
        
       
            
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-bar" id="searchBar" placeholder="Search assets, reports...">
            </div>
            
          
                
                <div class="profile-dropdown">
                    <button class="profile-btn" id="profileBtn">
                        <img src="../assets/images/male.png" class="profile-img" alt="Profile">
                        <span class="username d-none d-lg-inline"><?= htmlspecialchars($username) ?></span>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </button>
                    
                    <div class="dropdown-menu" id="dropdownMenu">
                        <div class="dropdown-header">
                            <strong><?= htmlspecialchars($username) ?></strong>
                            <div class="text-muted small">Premium Member</div>
                        </div>
                        <a href="../user/profile.php" class="dropdown-item">
                            <i class="fas fa-user"></i> Profile
                        </a>
                        <a href="../user/add_investment.php" class="dropdown-item">
                            <i class="fas fa-plus-circle"></i> Add Investment
                        </a>
                        <a href="../user/investdash.php" class="dropdown-item">
                            <i class="fas fa-chart-pie"></i> Portfolio
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="../user/buy.php" class="dropdown-item">
                            <i class="fas fa-crown"></i> Upgrade Plan
                        </a>
                        <a href="../includes/report.php" class="dropdown-item">
                            <i class="fas fa-file-alt"></i> Reports
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="../auth/logout.php" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Profile dropdown toggle
    document.addEventListener("DOMContentLoaded", () => {
        const profileBtn = document.getElementById("profileBtn");
        const dropdownMenu = document.getElementById("dropdownMenu");
        
        profileBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            dropdownMenu.classList.toggle("show");
        });
        
        // Close dropdown when clicking outside
        document.addEventListener("click", () => {
            dropdownMenu.classList.remove("show");
        });
        
        // Search functionality
        const searchInput = document.getElementById("searchBar");
        const keywordMap = {
            "pricing": "pricing-cards-1-uHKCE3WUl8",
            "watchlist": "watchlist",
            "cheers": "cheers",
            "market": "market-data",
            "market data": "market-data",
            "contact": "contact",
            "ultimate": "pricing-cards-1-uHKCE3WUl8",
        };
        
        if (searchInput) {
            searchInput.addEventListener("keypress", function(e) {
                if (e.key === "Enter") {
                    const query = searchInput.value.toLowerCase().trim();
                    if (!query) return;
                    
                    let matchedSectionId = null;
                    for (const [keyword, sectionId] of Object.entries(keywordMap)) {
                        if (query.includes(keyword)) {
                            matchedSectionId = sectionId;
                            break;
                        }
                    }
                    
                    if (matchedSectionId) {
                        const target = document.getElementById(matchedSectionId);
                        if (target) {
                            target.scrollIntoView({ behavior: "smooth" });
                        } else {
                            alert("Section found but not available on this page.");
                        }
                    } else {
                        alert("No matching section found. Try 'market', 'pricing', or 'watchlist'");
                    }
                }
            });
        }
    });
</script>

</body>
</html>