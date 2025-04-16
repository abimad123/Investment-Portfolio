<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

include '../includes/db.php';

$user_id = $_SESSION['user_id'];

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token!");
    }

    $asset_name = mysqli_real_escape_string($conn, $_POST['asset_name']);
    $asset_type = mysqli_real_escape_string($conn, $_POST['asset_type']);
    $amount = filter_var($_POST['amount'], FILTER_VALIDATE_FLOAT);
    $purchase_price = filter_var($_POST['purchase_price'], FILTER_VALIDATE_FLOAT);
    $purchase_date = $_POST['purchase_date'];

    if ($amount === false || $purchase_price === false) {
        die("Invalid numeric input!");
    }

    $sql = "INSERT INTO assets (user_id, asset_type, asset_name, amount, purchase_price, purchase_date) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issdds", $user_id, $asset_type, $asset_name, $amount, $purchase_price, $purchase_date);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit;
    } else {
        error_log("DB Error: " . $conn->error);
        // Store error in session to display after redirect
        $_SESSION['error'] = "An error occurred. Please try again.";
        header("Location: add_investment.php");
        exit;
    }
}

// Only include the header after all potential redirects
include '../includes/header.php';

// Rest of your code for fetching user data...
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Investment | Portfolio Tracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #EAE7DC;;
            --secondary: #3f37c9;
            --dark: #1e1e1e;
            --light: #f8f9fa;
            --success: #4cc9f0;
            --warning: #f8961e;
            --danger: #f72585;
            --gray: #6c757d;
            --gray-light: #e9ecef;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color:  #EAE7DC;
            color: var(--dark);
            line-height: 1.6;
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
        }
        
        .form-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2.5rem;
            border-radius: 16px;
            background: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
        }
        
        .form-header h2 {
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .form-header p {
            color: var(--gray);
            font-size: 0.95rem;
        }
        
        .form-header::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--success));
            margin: 1rem auto 0;
            border-radius: 2px;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .form-control, .form-select {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid var(--gray-light);
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
        }
        
        .input-group-text {
            background-color: var(--primary-light);
            border-color: var(--gray-light);
            color: var(--primary);
        }
        
        .btn-primary {
            background-color: var(--primary);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 1rem;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.2);
        }
        
        .asset-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-right: 1rem;
            color: white;
        }
        
        .stock-icon { background-color: #4cc9f0; }
        .crypto-icon { background-color: #f8961e; }
        .gold-icon { background-color: #ffd700; }
        .estate-icon { background-color: #7209b7; }
        
        .floating-label {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .floating-label input, .floating-label select {
            height: 56px;
            padding-top: 1.5rem;
        }
        
        .floating-label label {
            position: absolute;
            top: 0.75rem;
            left: 1rem;
            font-size: 0.8rem;
            color: var(--gray);
            transition: all 0.2s ease;
            pointer-events: none;
        }
        
        .floating-label input:focus + label,
        .floating-label input:not(:placeholder-shown) + label,
        .floating-label select:focus + label,
        .floating-label select:not([value=""]) + label {
            top: 0.4rem;
            font-size: 0.7rem;
            color: var(--primary);
        }
        
        .asset-type-selector {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        
        .asset-type-option {
            flex: 1;
            text-align: center;
            padding: 1rem 0.5rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid var(--gray-light);
            background-color: white;
        }
        
        .asset-type-option:hover {
            border-color: var(--primary);
        }
        
        .asset-type-option.active {
            border-color: var(--primary);
            background-color: var(--primary-light);
        }
        
        .asset-type-option i {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: var(--primary);
        }
        
        .asset-type-option.active i {
            color: var(--secondary);
        }
        
        .asset-type-option span {
            display: block;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        @media (max-width: 768px) {
            .form-container {
                padding: 1.5rem;
                margin: 1rem;
            }
            
            .asset-type-selector {
                flex-wrap: wrap;
            }
            
            .asset-type-option {
                flex: 0 0 calc(50% - 0.75rem);
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container py-5">
        <div class="form-container glass-card">
            <div class="form-header">
                <h2>Add New Investment</h2>
                <p>Track your assets and grow your portfolio</p>
            </div>
            
            <form method="POST" id="investmentForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <!-- Asset Type Selector -->
                <div class="mb-4">
                    <label class="form-label">Asset Type</label>
                    <div class="asset-type-selector">
                        <div class="asset-type-option active" data-value="Stock">
                            <i class="fas fa-chart-line"></i>
                            <span>Stock</span>
                        </div>
                        <div class="asset-type-option" data-value="Crypto">
                            <i class="fab fa-bitcoin"></i>
                            <span>Crypto</span>
                        </div>
                        <div class="asset-type-option" data-value="Gold">
                            <i class="fas fa-coins"></i>
                            <span>Gold</span>
                        </div>
                        <div class="asset-type-option" data-value="Real Estate">
                            <i class="fas fa-home"></i>
                            <span>Real Estate</span>
                        </div>
                    </div>
                    <input type="hidden" name="asset_type" id="asset_type" value="Stock" required>
                </div>
                
                <!-- Asset Name -->
                <div class="floating-label">
                    <input type="text" class="form-control" name="asset_name" id="asset_name" placeholder=" " required>
                    <label for="asset_name">Asset Name (e.g., Apple Inc., Bitcoin, etc.)</label>
                </div>
                
                <div class="row g-3">
                    <!-- Amount Invested -->
                    <div class="col-md-6">
                        <div class="floating-label">
                            <input type="number" class="form-control" name="amount" id="amount" placeholder=" " step="0.01" min="0" required>
                            <label for="amount">Amount Invested (₹)</label>
                        </div>
                    </div>
                    
                    <!-- Purchase Price -->
                    <div class="col-md-6">
                        <div class="floating-label">
                            <input type="number" class="form-control" name="purchase_price" id="purchase_price" placeholder=" " step="0.01" min="0" required>
                            <label for="purchase_price">Purchase Price (₹)</label>
                        </div>
                    </div>
                </div>
                
                <!-- Purchase Date -->
                <div class="floating-label mt-3">
                    <input type="date" class="form-control" name="purchase_date" id="purchase_date" placeholder=" " required>
                    <label for="purchase_date">Purchase Date</label>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-2"></i> Add Investment
                </button>
            </form>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Asset type selection
        document.querySelectorAll('.asset-type-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.asset-type-option').forEach(opt => {
                    opt.classList.remove('active');
                });
                this.classList.add('active');
                document.getElementById('asset_type').value = this.getAttribute('data-value');
            });
        });
        
        // Form validation
        document.getElementById('investmentForm').addEventListener('submit', function(e) {
            const amount = parseFloat(document.getElementById('amount').value);
            const price = parseFloat(document.getElementById('purchase_price').value);
            const date = document.getElementById('purchase_date').value;
            
            if (amount <= 0 || price <= 0) {
                e.preventDefault();
                alert('Amount and purchase price must be greater than zero.');
                return false;
            }
            
            if (!date) {
                e.preventDefault();
                alert('Please select a purchase date.');
                return false;
            }
            
            return true;
        });
        
        // Today's date as default for purchase date
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('purchase_date').value = today;
        });
    </script>
</body>
</html>
