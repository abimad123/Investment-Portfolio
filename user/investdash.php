<?php
// Start session and include header
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

include '../includes/header.php';

// Database connection
$servername = "localhost"; // Change as needed
$username = "root"; // Change as needed
$password = ""; // Change as needed
$dbname = "portfolio_db"; // Change to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Fetch asset data for the user
$sql = "SELECT * FROM assets WHERE user_id = $user_id";
$result = $conn->query($sql);

// Calculate summary data
$sql_summary = "SELECT 
    SUM(amount * purchase_price) as total_investment,
    COUNT(*) as total_assets,
    MIN(purchase_date) as earliest_investment 
    FROM assets 
    WHERE user_id = $user_id";
$summary_result = $conn->query($sql_summary);
$summary = $summary_result->fetch_assoc();

// Get asset types for allocation chart
$sql_types = "SELECT 
    asset_type, 
    SUM(amount * purchase_price) as total_value 
    FROM assets 
    WHERE user_id = $user_id 
    GROUP BY asset_type";
$types_result = $conn->query($sql_types);

// Prepare data for charts
$asset_allocation_data = [];
$colors = ['#4361ee', '#3f37c9', '#4cc9f0', '#f8961e', '#7209b7', '#f72585', '#4895ef', '#3a0ca3', '#3a86ff', '#560bad'];
$color_index = 0;

while ($row = $types_result->fetch_assoc()) {
    $asset_allocation_data[] = [
        'name' => $row['asset_type'],
        'value' => (float)$row['total_value'],
        'color' => $colors[$color_index % count($colors)]
    ];
    $color_index++;
}

// Calculate portfolio growth over last 6 months
$portfolio_history = [];
$current_month = date('n');
$current_year = date('Y');

for ($i = 6; $i >= 0; $i--) {
    $month = $current_month - $i;
    $year = $current_year;
    
    if ($month <= 0) {
        $month += 12;
        $year--;
    }
    
    $month_name = date('M', mktime(0, 0, 0, $month, 1, $year));
    
    // Calculate monthly value from database (simplified for example)
    $sql_monthly = "SELECT SUM(amount * purchase_price) as monthly_value 
                   FROM assets 
                   WHERE user_id = $user_id 
                   AND MONTH(purchase_date) <= $month 
                   AND YEAR(purchase_date) <= $year";
    $monthly_result = $conn->query($sql_monthly);
    $monthly_data = $monthly_result->fetch_assoc();
    
    $monthly_value = $monthly_data['monthly_value'] ?? 0;
    
    $portfolio_history[] = [
        'month' => $month_name,
        'value' => round($monthly_value, 2)
    ];
}

$portfolio_history_json = json_encode($portfolio_history);
$allocation_json = json_encode($asset_allocation_data);

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InvestSmart Portfolio | Premium Investment Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --primary-bg: #EAE7DC;
            --secondary-bg: #ffffff;
            --accent-color: #4C3F91;
            --text-color: #333;
            --text-light: #8D8D8D;
            --shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            --highlight: #9145B6;
            --success: #03C4A1;
            --danger: #FF5677;
            --warning: #FFC93C;
            --info: #3A86FF;
            --card-radius: 20px;
            --transition-speed: 0.3s;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--primary-bg);
            color: var(--text-color);
            line-height: 1.6;
            overflow-x: hidden;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px;
        }

        .greeting-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .greeting {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(90deg, var(--accent-color), var(--highlight));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            color: transparent;
        }

        .subtitle {
            font-size: 1.1rem;
            color: var(--text-light);
            margin-top: -5px;
        }

        .date-display {
            font-size: 1.1rem;
            font-weight: 500;
            color: var(--text-light);
            padding: 10px 20px;
            background: var(--secondary-bg);
            border-radius: 50px;
            box-shadow: var(--shadow);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 25px;
            margin-bottom: 25px;
        }

        .card {
            background: var(--secondary-bg);
            border-radius: var(--card-radius);
            padding: 25px;
            box-shadow: var(--shadow);
            transition: transform var(--transition-speed), box-shadow var(--transition-speed);
            overflow: hidden;
            position: relative;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .card-sm {
            grid-column: span 3;
        }

        .card-md {
            grid-column: span 6;
        }

        .card-lg {
            grid-column: span 12;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .card-icon {
            font-size: 1.4rem;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 15px;
            color: white;
        }

        .bg-accent {
            background: var(--accent-color);
        }

        .bg-highlight {
            background: var(--highlight);
        }

        .bg-success {
            background: var(--success);
        }

        .bg-danger {
            background: var(--danger);
        }

        .bg-warning {
            background: var(--warning);
        }

        .bg-info {
            background: var(--info);
        }

        .card-value {
            font-size: 2rem;
            font-weight: 700;
            margin: 10px 0;
        }

        .card-subtitle {
            font-size: 0.9rem;
            color: var(--text-light);
        }

        .chart-container {
            height: 350px;
            width: 100%;
            position: relative;
            margin-top: 20px;
        }
        
        .donut-chart-container {
            height: 300px;
            position: relative;
        }

        .table-container {
            overflow-x: auto;
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            padding: 15px;
            font-weight: 500;
            color: var(--text-light);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .asset-name {
            font-weight: 500;
            display: flex;
            align-items: center;
        }

        .asset-icon {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            margin-right: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.9rem;
        }

        .positive {
            color: var(--success);
        }

        .negative {
            color: var(--danger);
        }

        .badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .badge-stocks {
            background-color: rgba(76, 63, 145, 0.1);
            color: var(--accent-color);
        }

        .badge-crypto {
            background-color: rgba(255, 86, 119, 0.1);
            color: var(--danger);
        }

        .badge-bonds {
            background-color: rgba(3, 196, 161, 0.1);
            color: var(--success);
        }

        .badge-realestate {
            background-color: rgba(255, 201, 60, 0.1);
            color: var(--warning);
        }

        .badge-commodities {
            background-color: rgba(58, 134, 255, 0.1);
            color: var(--info);
        }

        .card-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 25px;
            border-radius: 12px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all var(--transition-speed);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            font-family: 'Poppins', sans-serif;
        }

        .btn-primary {
            background-color: var(--accent-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--highlight);
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--accent-color);
            color: var(--accent-color);
        }

        .btn-outline:hover {
            background-color: var(--accent-color);
            color: white;
        }

        .portfolio-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .stat-item {
            text-align: center;
            padding: 15px;
            background-color: rgba(255, 255, 255, 0.5);
            border-radius: 15px;
            flex: 1;
            margin: 0 10px;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--text-light);
        }

        .pulse {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: var(--success);
            box-shadow: 0 0 0 0 rgba(3, 196, 161, 0.7);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(3, 196, 161, 0.7);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 10px rgba(3, 196, 161, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(3, 196, 161, 0);
            }
        }

        /* Legend for pie chart */
        .pie-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 20px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            font-size: 0.9rem;
        }
        
        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 2px;
            margin-right: 8px;
        }

        .asset-percentage {
            margin-left: 5px;
            color: var(--text-light);
            font-size: 0.8rem;
        }
        
        /* Responsive styles */
        @media (max-width: 1200px) {
            .card-sm {
                grid-column: span 6;
            }
        }
        
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .card-sm, .card-md, .card-lg {
                grid-column: span 1;
            }
            
            .greeting {
                font-size: 2rem;
            }
            
            .portfolio-stats {
                flex-direction: column;
                gap: 15px;
            }
            
            .stat-item {
                margin: 0;
            }
            
            .greeting-section {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
        }
        
        @media (max-width: 480px) {
            .dashboard-container {
                padding: 20px 15px;
            }
            
            .card {
                padding: 20px;
            }
            
            .card-value {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <br>
    <br>
    <br>
    <br><br>
    <br>
    <div class="dashboard-container">
        <div class="greeting-section">
            <div>
                <h1 class="greeting">Your Investment Portfolio</h1>
                <p class="subtitle">Track, analyze, and optimize your investments in one place</p>
            </div>
            <div class="date-display">
                <i class="far fa-calendar-alt"></i> <?php echo date('d M, Y'); ?>
            </div>
        </div>
        
        <!-- Summary Cards -->
        <div class="dashboard-grid">
            <div class="card card-sm">
                <div class="pulse"></div>
                <div class="card-header">
                    <div class="card-title">Total Investment</div>
                    <div class="card-icon bg-accent">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
                <div class="card-value">$<?php echo number_format($summary['total_investment'] ?? 0, 2); ?></div>
                <div class="card-subtitle">
                    <i class="fas fa-clock"></i> Since <?php echo date('M Y', strtotime($summary['earliest_investment'] ?? 'now')); ?>
                </div>
            </div>
            
            <div class="card card-sm">
                <div class="card-header">
                    <div class="card-title">Total Assets</div>
                    <div class="card-icon bg-highlight">
                        <i class="fas fa-cubes"></i>
                    </div>
                </div>
                <div class="card-value"><?php echo $summary['total_assets'] ?? 0; ?></div>
                <div class="card-subtitle">Across multiple categories</div>
            </div>
            
            <div class="card card-sm">
                <div class="card-header">
                    <div class="card-title">Portfolio Value</div>
                    <div class="card-icon bg-success">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
                <?php
                // In a real scenario, you would calculate current market value
                // For this example, we'll use a 5% increase from total investment
                $portfolio_value = ($summary['total_investment'] ?? 0) * 1.05;
                $profit = $portfolio_value - ($summary['total_investment'] ?? 0);
                $profit_percentage = $summary['total_investment'] ? ($profit / $summary['total_investment']) * 100 : 0;
                ?>
                <div class="card-value">$<?php echo number_format($portfolio_value, 2); ?></div>
                <div class="card-subtitle <?php echo $profit >= 0 ? 'positive' : 'negative'; ?>">
                    <i class="fas <?php echo $profit >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'; ?>"></i>
                    <?php echo $profit >= 0 ? '+' : ''; ?><?php echo number_format($profit_percentage, 2); ?>% overall
                </div>
            </div>
            
            <div class="card card-sm">
                <div class="card-header">
                    <div class="card-title">ROI</div>
                    <div class="card-icon bg-warning">
                        <i class="fas fa-percentage"></i>
                    </div>
                </div>
                <?php
                // Calculate average ROI (Return on Investment)
                // In a real implementation, you would calculate this based on actual data
                $roi = $summary['total_investment'] ? ($profit / $summary['total_investment']) * 100 : 0;
                ?>
                <div class="card-value <?php echo $roi >= 0 ? 'positive' : 'negative'; ?>">
                    <?php echo $roi >= 0 ? '+' : ''; ?><?php echo number_format($roi, 2); ?>%
                </div>
                <div class="card-subtitle">Annual return on investment</div>
            </div>
            
            <!-- Portfolio Growth Chart -->
            <div class="card card-md">
                <div class="card-header">
                    <div class="card-title">Portfolio Growth</div>
                </div>
                <div class="chart-container">
                    <canvas id="portfolioGrowthChart"></canvas>
                </div>
            </div>
            
            <!-- Asset Allocation Chart -->
            <div class="card card-md">
                <div class="card-header">
                    <div class="card-title">Asset Allocation</div>
                </div>
                <div class="donut-chart-container">
                    <canvas id="assetAllocationChart"></canvas>
                </div>
                <div class="pie-legend" id="pieLegend">
                    <!-- Legend will be populated by JavaScript -->
                </div>
            </div>
            
            <!-- Assets Table -->
            <div class="card card-lg">
                <div class="card-header">
                    <div class="card-title">Your Assets</div>
                    <div class="card-actions">
                        <button class="btn btn-outline">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <button class="btn btn-primary" style="margin-left: 10px;" onclick="window.location.href='add_investment.php';">
                            <i class="fas fa-plus"></i> Add Asset
                        </button>
                    </div>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Asset Name</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Purchase Price</th>
                                <th>Total Value</th>
                                <th>Purchase Date</th>
                                <th>Current Value</th>
                                <th>Profit/Loss</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Reset the result pointer
                            if (isset($result) && $result->num_rows > 0) {
                                $result->data_seek(0);
                                while($row = $result->fetch_assoc()) {
                                    $total_value = $row['amount'] * $row['purchase_price'];
                                    
                                    // In a real scenario, you would get current market prices for each asset
                                    // For this example, we'll simulate some random price changes
                                    $current_price = $row['purchase_price'] * (1 + (mt_rand(-15, 25) / 100));
                                    $current_value = $row['amount'] * $current_price;
                                    $profit_loss = $current_value - $total_value;
                                    $profit_loss_percentage = ($profit_loss / $total_value) * 100;
                                    
                                    // Determine icon and badge class based on asset type
                                    $asset_type = strtolower($row['asset_type']);
                                    $icon_class = 'fa-chart-line'; // default
                                    $badge_class = 'badge-stocks'; // default
                                    $icon_bg = 'bg-accent'; // default
                                    
                                    switch($asset_type) {
                                        case 'crypto':
                                            $icon_class = 'fa-bitcoin';
                                            $badge_class = 'badge-crypto';
                                            $icon_bg = 'bg-danger';
                                            break;
                                        case 'bonds':
                                            $icon_class = 'fa-file-contract';
                                            $badge_class = 'badge-bonds';
                                            $icon_bg = 'bg-success';
                                            break;
                                        case 'real estate':
                                            $icon_class = 'fa-building';
                                            $badge_class = 'badge-realestate';
                                            $icon_bg = 'bg-warning';
                                            break;
                                        case 'commodities':
                                            $icon_class = 'fa-coins';
                                            $badge_class = 'badge-commodities';
                                            $icon_bg = 'bg-info';
                                            break;
                                    }
                                    
                                    echo "<tr>";
                                    echo "<td><div class='asset-name'><div class='asset-icon {$icon_bg}'><i class='fas {$icon_class}'></i></div>" . htmlspecialchars($row['asset_name']) . "</div></td>";
                                    echo "<td><span class='badge {$badge_class}'>" . htmlspecialchars($row['asset_type']) . "</span></td>";
                                    echo "<td>" . htmlspecialchars($row['amount']) . "</td>";
                                    echo "<td>$" . number_format($row['purchase_price'], 2) . "</td>";
                                    echo "<td>$" . number_format($total_value, 2) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['purchase_date']) . "</td>";
                                    echo "<td>$" . number_format($current_value, 2) . "</td>";
                                    echo "<td class='" . ($profit_loss >= 0 ? 'positive' : 'negative') . "'>" 
                                        . ($profit_loss >= 0 ? '+' : '') 
                                        . "$" . number_format($profit_loss, 2) 
                                        . " <span class='asset-percentage'>(" 
                                        . ($profit_loss_percentage >= 0 ? '+' : '')
                                        . number_format($profit_loss_percentage, 2) . "%)</span></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' style='text-align: center;'>No assets found. Add your first investment to get started!</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize charts when the DOM is fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Asset Allocation Pie Chart
            if (document.getElementById('assetAllocationChart')) {
                const ctx = document.getElementById('assetAllocationChart').getContext('2d');
                
                // Extract data for the chart
                const allocationData = <?php echo $allocation_json ?? '[]'; ?>;
                
                if (allocationData.length > 0) {
                    const labels = allocationData.map(item => item.name);
                    const values = allocationData.map(item => item.value);
                    const colors = allocationData.map(item => item.color);
                    
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: values,
                                backgroundColor: colors,
                                borderWidth: 0,
                                borderRadius: 5,
                                hoverOffset: 15
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '70%',
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                    titleColor: '#333',
                                    bodyColor: '#666',
                                    bodyFont: {
                                        family: 'Poppins',
                                        size: 14
                                    },
                                    titleFont: {
                                        family: 'Poppins',
                                        size: 16,
                                        weight: 'bold'
                                    },
                                    padding: 15,
                                    borderColor: 'rgba(0, 0, 0, 0.1)',
                                    borderWidth: 1,
                                    displayColors: true,
                                    boxWidth: 15,
                                    boxHeight: 15,
                                    boxPadding: 5,
                                    usePointStyle: true,
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.raw || 0;
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = Math.round((value / total) * 100);
                                            return `${label}: $${value.toLocaleString()} (${percentage}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                    
                    // Create custom legend
                    const legendContainer = document.getElementById('pieLegend');
                    legendContainer.innerHTML = '';
                    
                    const totalValue = values.reduce((a, b) => a + b, 0);
                    
                    allocationData.forEach(item => {
                        const percentage = Math.round((item.value / totalValue) * 100);
                        const legendItem = document.createElement('div');
                        legendItem.className = 'legend-item';
                        
                        const colorBox = document.createElement('div');
                        colorBox.className = 'legend-color';
                        colorBox.style.backgroundColor = item.color;
                        
                        const labelText = document.createElement('span');
                        labelText.textContent = `${item.name}`;
                        
                        const percentText = document.createElement('span');
                        percentText.className = 'asset-percentage';
                        percentText.textContent = `(${percentage}%)`;
                        
                        legendItem.appendChild(colorBox);
                        legendItem.appendChild(labelText);
                        legendItem.appendChild(percentText);
                        
                        legendContainer.appendChild(legendItem);
                    });
                }
            }
            
            // Portfolio Growth Line Chart
            if (document.getElementById('portfolioGrowthChart')) {
                const ctx = document.getElementById('portfolioGrowthChart').getContext('2d');
                
                const portfolioHistory = <?php echo $portfolio_history_json ?? '[]'; ?>;
                
                if (portfolioHistory.length > 0) {
                    const months = portfolioHistory.map(item => item.month);
                    const values = portfolioHistory.map(item => item.value);
                    
                    const gradientFill = ctx.createLinearGradient(0, 0, 0, 300);
                    gradientFill.addColorStop(0, 'rgba(76, 63, 145, 0.3)');
                    gradientFill.addColorStop(1, 'rgba(76, 63, 145, 0)');
                    
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: months,
                            datasets: [{
                                label: 'Portfolio Value',
                                data: values,
                                borderColor: '#4C3F91',
                                backgroundColor: gradientFill,
                                borderWidth: 3,
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: '#ffffff',
                                pointBorderColor: '#4C3F91',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                pointHoverRadius: 8
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: false,
                                    grid: {
                                        display: true,
                                        drawBorder: false,
                                        color: 'rgba(200, 200, 200, 0.15)'
                                    },
                                    ticks: {
                                        font: {
                                            family: 'Poppins',
                                            size: 12
                                        },
                                        color: '#8D8D8D',
                                        callback: function(value) {
                                            return '$' + value.toLocaleString();
                                        }
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        font: {
                                            family: 'Poppins',
                                            size: 12
                                        },
                                        color: '#8D8D8D'
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                    titleColor: '#333',
                                    bodyColor: '#666',
                                    bodyFont: {
                                        family: 'Poppins',
                                        size: 14
                                    },
                                    titleFont: {
                                        family: 'Poppins',
                                        size: 16,
                                        weight: 'bold'
                                    },
                                    padding: 15,
                                    borderColor: 'rgba(0, 0, 0, 0.1)',
                                    borderWidth: 1,
                                    displayColors: false,
                                    callbacks: {
                                        label: function(context) {
                                            return `$${context.raw.toLocaleString()}`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }
        });
    </script>
</body>
</html>
