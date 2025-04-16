<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

include '../includes/db.php';
include '../includes/header.php';

$user_id = $_SESSION['user_id'];

// Fetch User Name
$stmt = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$username = $user['username'] ?? "User";

// Fetch User Investments
$stmt = $conn->prepare("SELECT * FROM assets WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Calculate Total Returns and Current Value
$totalReturns = $currentValue = 0;
while ($row = $result->fetch_assoc()) {
    $currentValue += $row['amount'];
}
$totalReturns = $currentValue - 100; // Example calculation (Replace with actual logic)

// Convert PHP Data to JavaScript
$portfolioData = json_encode($currentValue);

?>
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

<link rel="stylesheet" href="https://r.mobirisesite.com/1378487/assets/web/assets/mobirise-icons2/mobirise2.css?rnd=1744095453804">
  <link rel="stylesheet" href="https://r.mobirisesite.com/1378487/assets/web/assets/mobirise-icons/mobirise-icons.css?rnd=1744095453804">
  <link rel="stylesheet" href="https://r.mobirisesite.com/1378487/assets/bootstrap/css/bootstrap.min.css?rnd=1744095453804">
  <link rel="stylesheet" href="https://r.mobirisesite.com/1378487/assets/bootstrap/css/bootstrap-grid.min.css?rnd=1744095453804">
  <link rel="stylesheet" href="https://r.mobirisesite.com/1378487/assets/bootstrap/css/bootstrap-reboot.min.css?rnd=1744095453804">
  <link rel="stylesheet" href="https://r.mobirisesite.com/1378487/assets/parallax/jarallax.css?rnd=1744095453804">
  <link rel="stylesheet" href="https://r.mobirisesite.com/1378487/assets/dropdown/css/style.css?rnd=1744095453804">
  <link rel="stylesheet" href="https://r.mobirisesite.com/1378487/assets/socicon/css/styles.css?rnd=1744095453804">
  <link rel="stylesheet" href="https://r.mobirisesite.com/1378487/assets/theme/css/style.css?rnd=1744095453804">
  <link rel="stylesheet" href="https://r.mobirisesite.com/1378487/assets/recaptcha.css?rnd=1744095453804">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Source+Serif+4:wght@400;700&amp;display=swap&amp;display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Source+Serif+4:wght@400;700&display=swap&display=swap"></noscript>
  <link rel="stylesheet" href="https://r.mobirisesite.com/1378487/assets/css/mbr-additional.css?rnd=1744095453804" type="text/css">




<!-- Vendor CSS Files -->
<link href="assets/vendor/animate.css/animate.min.css" rel="stylesheet">
<link href="assets/vendor/aos/aos.css" rel="stylesheet">
<link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
<link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
<link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
<link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

<link rel="stylesheet" type="text/css" href="../assets/css/styles.css">
<style>

body {
	background: #EAE7DC;
	font-family: "Comfortaa", sans-serif;
	position: relative;
  background-color: #EAE7DC;
}


/* =================== 📌 Watchlist Section =================== */
.watchlist-wrapper {
  position: relative;
  overflow: hidden;
  margin-top: 10px;
  width: 100%;
}

.watchlist-container {
  display: flex;
  flex-wrap: nowrap;
  gap: 15px;
  overflow-x: auto;
  scroll-behavior: smooth;
  padding: 10px;
  white-space: nowrap;
  max-width: 100%;
}

.watchlist-container::-webkit-scrollbar {
  display: none;
}

.watchlist-item {
  flex: 0 0 auto;
  width: 160px;
  padding: 15px;
  text-align: center;
  font-size: 16px;
  font-weight: bold;
  background: white;
  border-radius: 10px;
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s ease-in-out;
  position: relative;
}

.watchlist-item:hover {
  transform: scale(1.05);
}

.remove-btn {
  position: absolute;
  top: 5px;
  right: 10px;
  background: red;
  color: white;
  border: none;
  padding: 5px;
  cursor: pointer;
  font-size: 14px;
  border-radius: 50%;
}
/* =================== 📌 Stocks Section =================== */
.stocks-container {
  display: flex;
  gap: 15px;
  overflow-x: auto;
  scroll-behavior: smooth;
  padding: 10px;
}

.stocks-container .card {
  flex: 0 0 auto;
  width: 160px;
  padding: 15px;
  text-align: center;
  font-size: 16px;
  font-weight: bold;
  background: white;
  border-radius: 10px;
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s ease-in-out;
  position: relative;
}

.stocks-container .card:hover {
  transform: scale(1.05);
}

.add-btn {
  position: absolute;
  top: 5px;
  right: 10px;
  background: green;
  color: white;
  border: none;
  padding: 5px;
  cursor: pointer;
  border-radius: 50%;
}


/* =================== 📌 Market News Section =================== */
.news-container {
  background: white;
  border-radius: 20px;
  padding: 40px;
  max-width: 1600px;
  align-items: center;
  margin: 10px auto;
}

.news-container h2 {
  font-size: 26px;
  margin-bottom: 15px;
  display: flex;
  align-items: center;
}

.news-container h2::before {

  margin-right: 10px;
}

.news-item {
  display: flex;
  align-items: center;
  padding: 10px;
  border-bottom: 1px solid #ddd;
}

.news-item:last-child {
  border-bottom: none;
}

.news-image {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 10px;
  margin-right: 10px;
}

.news-title {
  font-weight: bold;
  font-size: 16px;
  text-decoration: none;
  color: #000;
  transition: color 0.2s ease-in-out;
}

.news-title:hover {
  color: #0056b3;
}

.news-source {
  font-size: 14px;
  color: #555;
}


.arrow-icon {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  font-size: 1.1rem;
  color: #333;
  transition: transform 0.3s ease;
  pointer-events: none;
}

/* ========================== */
/* 🌟 SCROLL BUTTONS */
/* ========================== */
.scroll-btn {
  display: none; /* Disable scroll buttons */
}


* {
	box-sizing: border-box;
}

.main-content {
	text-align: center;
	text-transform: uppercase;
	scroll-snap-type: y mandatory;
	position: relative;
	height: 100vh;
}

.hover,
.word,
.well {
	cursor: pointer;
}

.well {
	position: relative;
	color: #fff;
	font: 900 0px Montserrat;
	text-shadow: 0 50px 35px rgba(234, 254, 15, 0.87);
}

.concept {
	position: relative;
	padding: 5em;
	height: 100vh;
	overflow: hidden;
	scroll-snap-align: center;
	&:before {
		content: "";
		position: absolute;
		width: 100%;
		height: 100%;
		top: 0;
		left: 0;
		background: radial-gradient(rgba(0, 0, 0, 0.3), transparent);
		opacity: 0;
		transition: all 1s cubic-bezier(0.19, 1, 0.22, 1);
	}
	&:hover:before {
		opacity: 0.5;
	}
}

.concept-one {
  display: grid;
  grid: repeat(3, 1fr) / repeat(3, 1fr);
  background: yellow;
  padding: 8em;
  background: url("../assets/images/dashboard.png") center center / cover no-repeat;
  h1 {
    position: absolute;
    margin: auto;
    left: 0;
    right: 0;
    top: 50%;
    margin-top: -30px;
    transition: 0.5s ease;
    z-index: 0;
    letter-spacing: 25px;
  }
  .hover {
    z-index: 1;
  }
  .hover-1:hover ~ h1 {
    left: 30px;
    margin-top: -10px;
  }
  .hover-2:hover ~ h1 {
    margin-top: -10px;
  }
  .hover-3:hover ~ h1 {
    right: 30px;
    margin-top: -10px;
  }
  .hover-4:hover ~ h1 {
    left: 30px;
  }
  .hover-6:hover ~ h1 {
    right: 30px;
  }
  .hover-7:hover ~ h1 {
    left: 30px;
    margin-top: -50px;
  }
  .hover-8:hover ~ h1 {
    margin-top: -50px;
  }
  .hover-9:hover ~ h1 {
    right: 30px;
    margin-top: -50px;
  }
}

@media (max-width: 768px) {
  .concept-one {
    grid: repeat(2, 1fr) / repeat(2, 1fr);
    padding: 4em;
    h1 {
      font-size: 20px;
      letter-spacing: 10px;
    }
  }
  .hover-1:hover ~ h1,
  .hover-3:hover ~ h1,
  .hover-7:hover ~ h1,
  .hover-9:hover ~ h1 {
    left: 15px;
    right: 15px;
    margin-top: -20px;
  }
}


</style>
<body>


<!-- <br><br> -->
<div class="main-content">
<div class="concept concept-one">
    <?php for ($i = 1; $i < 10; $i++): ?>
      <div class="hover hover-<?= $i ?>"></div>
    <?php endfor; ?>
    
    <h1  class="well" style="color: white; font-size: 40px;">
    <center> Welcome 
      <strong><?= htmlspecialchars($username) ?></strong></center>
    </h1>
  </div>
</div>
<!-- TradingView Widget BEGIN -->
<div class="tradingview-widget-container">
  <div class="tradingview-widget-container__widget"></div>
  <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-ticker-tape.js" async>
  {
  "symbols": [
    {
      "proName": "FOREXCOM:SPXUSD",
      "title": "S&P 500 Index"
    },
    {
      "proName": "FOREXCOM:NSXUSD",
      "title": "US 100 Cash CFD"
    },
    {
      "proName": "FX_IDC:EURUSD",
      "title": "EUR to USD"
    },
    {
      "proName": "BITSTAMP:BTCUSD",
      "title": "Bitcoin"
    },
    {
      "proName": "BITSTAMP:ETHUSD",
      "title": "Ethereum"
    },
    {
      "description": "",
      "proName": "NASDAQ:NVDA"
    },
    {
      "description": "",
      "proName": "NASDAQ:TSLA"
    },
    {
      "description": "",
      "proName": "NASDAQ:META"
    },
    {
      "description": "",
      "proName": "NASDAQ:MSFT"
    },
    {
      "description": "",
      "proName": "NYSE:NKE"
    },
    {
      "description": "",
      "proName": "NYSE:F"
    }
  ],
  "showSymbolLogo": true,
  "isTransparent": false,
  "displayMode": "adaptive",
  "colorTheme": "dark",
  "locale": "en"
}
  </script>
</div>
<!-- TradingView Widget END -->

<div class="container">   
<br>


<section data-bs-version="5.1" class="pricing1 cid-uHKCE3WUl8" id="pricing-cards-1-uHKCE3WUl8" style=background-color: #EAE7DC;>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 content-head">
                <div class="mbr-section-head mb-5">
                    <h4 class="mbr-section-title mbr-fonts-style align-center mb-0 display-2">
                        <strong>Pricing Plans</strong>
                    </h4>
                    
                </div>
            </div>
        </div>
        <div class="row">
            <div class="item features-without-image col-12 col-md-6 col-lg-3 item-mb active">
                <div class="item-wrapper">
                    <div class="item-head">
                        <h5 class="item-title mbr-fonts-style mb-3 display-5">
                            <strong>Starter Pack</strong>
                        </h5>
                        <h6 class="item-subtitle mbr-fonts-style mt-0 mb-0 display-7">
                            <strong>$1</strong>/Free Trial
                        </h6>
                    </div>
                    <div class="item-content">
                        <p class="mbr-text mbr-fonts-style display-7">Perfect for beginners wanting to dip their toes in investment waters.</p>
                    </div>
                    <div class="mbr-section-btn item-footer">
                        <a href="buy.php" class="btn item-btn btn-primary display-7">Try Now</a>
                    </div>
                </div>
            </div>
            <div class="item features-without-image col-12 col-md-6 col-lg-3 item-mb">
                <div class="item-wrapper">
                    <div class="item-head">
                        <h5 class="item-title mbr-fonts-style mb-3 display-5">
                            <strong>Pro Investor</strong>
                        </h5>
                        <h6 class="item-subtitle mbr-fonts-style mt-0 mb-0 display-7">
                            <strong>$29</strong>/Free Trial
                        </h6>
                    </div>
                    <div class="item-content">
                        <p class="mbr-text mbr-fonts-style display-7">For serious investors ready to take control of their financial destiny.</p>
                    </div>
                    <div class="mbr-section-btn item-footer">
                        <a href="buy.php" class="btn item-btn btn-primary display-7">Try Now</a>
                    </div>
                </div>
            </div>

            <div class="item features-without-image col-12 col-md-6 col-lg-3 item-mb">
                <div class="item-wrapper">
                    <div class="item-head">
                        <h5 class="item-title mbr-fonts-style mb-3 display-5">
                            <strong>Expert Analyst</strong>
                        </h5>
                        <h6 class="item-subtitle mbr-fonts-style mt-0 mb-0 display-7">
                            <strong>$49</strong>/Free Trial
                        </h6>
                    </div>
                    <div class="item-content">
                        <p class="mbr-text mbr-fonts-style display-7">Advanced tools for those who mean business in the investment arena.</p>
                    </div>
                    <div class="mbr-section-btn item-footer">
                        <a href="buy.php" class="btn item-btn btn-primary display-7">Try Now</a>
                    </div>
                </div>
            </div>

            <div class="item features-without-image col-12 col-md-6 col-lg-3 item-mb">
                <div class="item-wrapper">
                    <div class="item-head">
                        <h5 class="item-title mbr-fonts-style mb-3 display-5">
                            <strong>Ultimate Package</strong>
                        </h5>
                        <h6 class="item-subtitle mbr-fonts-style mt-0 mb-0 display-7">
                            <strong>$99</strong>/Free Trial
                        </h6>
                    </div>
                    <div class="item-content">
                        <p class="mbr-text mbr-fonts-style display-7">All features unlocked for the investment aficionado who wants it all.</p>
                    </div>
                    <div class="mbr-section-btn item-footer">
                        <a href="buy.php" class="btn item-btn btn-primary display-7">Try Now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

</div>
<br>
<!-- TradingView Widget END -->


    <div class="watchlist-wrapper">
    <h4 class="mbr-section-title mbr-fonts-style align-center mb-0 display-2" id="watchlist">
                        <strong>Your Watchlist</strong>
                    </h4>
                    <br>
<center>   
<!-- TradingView Widget BEGIN -->
<div class="tradingview-widget-container" style="height:100%;width:100%">
  <div class="tradingview-widget-container__widget" style="height:calc(100% - 32px);width:100%"></div>
  <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js" async>
  {
  "width": "1500",
  "height": "610",
  "symbol": "NASDAQ:NVDA",
  "interval": "D",
  "timezone": "Etc/UTC",
  "theme": "dark",
  "style": "1",
  "locale": "en",
  "hide_side_toolbar": false,
  "allow_symbol_change": true,
  "watchlist": [
    "NASDAQ:TSLA",
    "NASDAQ:META",
    "BITSTAMP:BTCUSD"
  ],
  "details": true,
  "support_host": "https://www.tradingview.com"
}
  </script>
</div>
<!-- TradingView Widget END -->
</center>
<br>
<br>
<!-- TradingView Widget END -->
<div class="row mb-5 justify-content-center">
  <div class="col-12 mb-0 content-head">
    <h3 class="mbr-section-title mbr-fonts-style align-center mb-0 display-2" id="cheers">
      <strong>Cheers</strong>
    </h3>
  </div>
</div>
<br>

<div class="container">
  <div class="row gy-4">
    <!-- Testimonial 1 -->
    <div class="col-md-6">
      <div class="d-flex align-items-center">
        <img src="https://r.mobirisesite.com/1378487/assets/images/photo-1676385901160-a86dc9ccdfe1.jpeg" class="rounded-circle me-3" style="width: 100px; height: 100px; object-fit: cover;" alt="">
        <div>
          <h5 class="fw-bold mb-1">Sarah Connors</h5>
          <p class="mb-0">InvestSmart turned my chaos into clarity!</p>
        </div>
      </div>
    </div>

    <!-- Testimonial 2 -->
    <div class="col-md-6">
      <div class="d-flex align-items-center">
        <img src="https://r.mobirisesite.com/1378487/assets/images/photo-1586185018558-ea8f4b4c514f.jpeg" class="rounded-circle me-3" style="width: 100px; height: 100px; object-fit: cover;" alt="">
        <div>
          <h5 class="fw-bold mb-1">Mike Johnson</h5>
          <p class="mb-0">I finally understand my investments, thanks to this genius tool!</p>
        </div>
      </div>
    </div>

    <!-- Testimonial 3 -->
    <div class="col-md-6">
      <div class="d-flex align-items-center">
        <img src="https://r.mobirisesite.com/1378487/assets/images/photo-1677520338280-664ae23816eb.jpeg" class="rounded-circle me-3" style="width: 100px; height: 100px; object-fit: cover;" alt="">
        <div>
          <h5 class="fw-bold mb-1">Emily Davis</h5>
          <p class="mb-0">Tracking my portfolio has never been this fun!</p>
        </div>
      </div>
    </div>

    <!-- Testimonial 4 -->
    <div class="col-md-6">
      <div class="d-flex align-items-center">
        <img src="https://r.mobirisesite.com/1378487/assets/images/photo-1596646285603-e5f9bbfa524a.jpeg" class="rounded-circle me-3" style="width: 100px; height: 100px; object-fit: cover;" alt="">
        <div>
          <h5 class="fw-bold mb-1">Tom Hardy</h5>
          <p class="mb-0">InvestSmart is like having a financial advisor in my pocket!</p>
        </div>
      </div>
    </div>

    <!-- Testimonial 5 -->
    <div class="col-md-6">
      <div class="d-flex align-items-center">
        <img src="https://r.mobirisesite.com/1378487/assets/images/photo-1676385901160-31e1b9e1c0c7.jpeg" class="rounded-circle me-3" style="width: 100px; height: 100px; object-fit: cover;" alt="">
        <div>
          <h5 class="fw-bold mb-1">Jessica Parker</h5>
          <p class="mb-0">I can’t believe I survived without it!</p>
        </div>
      </div>
    </div>

    <!-- Testimonial 6 -->
    <div class="col-md-6">
      <div class="d-flex align-items-center">
        <img src="https://r.mobirisesite.com/1378487/assets/images/photo-1535026406642-530e01750ad7.jpeg" class="rounded-circle me-3" style="width: 100px; height: 100px; object-fit: cover;" alt="">
        <div>
          <h5 class="fw-bold mb-1">Chris Evans</h5>
          <p class="mb-0">This app is a lifesaver for my investments!</p>
        </div>
      </div>
    </div>
  </div>
</div>
<br>
<br>
<h4 class="mbr-section-title mbr-fonts-style align-center mb-0 display-2" id="market-data">
                        <strong>Market Data</strong>
                    </h4>
                    <br>
<center>
    <!-- TradingView Widget BEGIN -->
<div class="tradingview-widget-container">
  <div class="tradingview-widget-container__widget"></div>
   <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-market-quotes.js" async>
  {
  "width": "1500",
  "height": "600",
  "symbolsGroups": [
    {
      "name": "Indices",
      "originalName": "Indices",
      "symbols": [
        {
          "name": "FOREXCOM:SPXUSD",
          "displayName": "S&P 500 Index"
        },
        {
          "name": "FOREXCOM:NSXUSD",
          "displayName": "US 100 Cash CFD"
        },
        {
          "name": "INDEX:NKY",
          "displayName": "Japan 225"
        },
        {
          "name": "NYSE:NKE"
        },
        {
          "name": "NYSE:WMT"
        },
        {
          "name": "NASDAQ:ADBE"
        },
        {
          "name": "NASDAQ:RIOT"
        },
        {
          "name": "NYSE:MCD"
        },
        {
          "name": "NASDAQ:NVDA"
        },
        {
          "name": "NASDAQ:NFLX"
        }
      ]
    },
    {
      "name": "Forex",
      "originalName": "Forex",
      "symbols": [
        {
          "name": "FX_IDC:USDINR"
        },
        {
          "name": "FX_IDC:EURINR"
        },
        {
          "name": "FX_IDC:GBPINR"
        },
        {
          "name": "FX_IDC:INRUSD"
        },
        {
          "name": "FX_IDC:JPYINR"
        },
        {
          "name": "FX:EURUSD"
        }
      ]
    }
  ],
  "showSymbolLogo": true,
  "isTransparent": false,
  "colorTheme": "dark",
  "locale": "en",
  "backgroundColor": "#131722"
}
  </script>
</div>
<!-- TradingView Widget END -->
</div>
</center>
<br> <br> <br>
<!-- TradingView Widget BEGIN -->
<h4 class="mbr-section-title mbr-fonts-style align-center mb-0 display-2">
                        <strong>Most Traded Stocks</strong>
                    </h4>
                    <center>  <br>
<div class="tradingview-widget-container">
  <div class="tradingview-widget-container__widget"></div>
 <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-symbol-overview.js" async>
  {
  "symbols": [
    [
      "Apple",
      "AAPL|1D"
    ],
    [
      "Google",
      "GOOGL|1D"
    ],
    [
      "Microsoft",
      "MSFT|1D"
    ],
    [
      "NASDAQ:NVDA|1D"
    ],
    [
      "NASDAQ:AMD|1D"
    ],
    [
      "NASDAQ:PYPL|1D"
    ]
  ],
  "chartOnly": false,
  "width": "1500",
  "height": "500",
  "locale": "en",
  "colorTheme": "dark",
  "autosize": false,
  "showVolume": false,
  "showMA": false,
  "hideDateRanges": false,
  "hideMarketStatus": false,
  "hideSymbolLogo": false,
  "scalePosition": "right",
  "scaleMode": "Normal",
  "fontFamily": "-apple-system, BlinkMacSystemFont, Trebuchet MS, Roboto, Ubuntu, sans-serif",
  "fontSize": "10",
  "noTimeScale": false,
  "valuesTracking": "1",
  "changeMode": "price-and-percent",
  "chartType": "area",
  "maLineColor": "#2962FF",
  "maLineWidth": 1,
  "maLength": 9,
  "headerFontSize": "medium",
  "lineWidth": 2,
  "lineType": 0,
  "dateRanges": [
    "1d|1",
    "1m|30",
    "3m|60",
    "12m|1D",
    "60m|1W",
    "all|1M"
  ]
}
  </script>
</div>
<!-- TradingView Widget END -->
</center>
<script>
function toggleInvestmentDetails() {
    var details = document.getElementById("investmentDetails");
    var card = document.querySelector(".investment-card");
    details.style.display = (details.style.display === "block") ? "none" : "block";
    card.classList.toggle("expanded");
}
</script>
<section data-bs-version="5.1" class="gallery4 cid-uHKCE3Y7id" id="gallery-12-uHKCE3Y7id" style="background-color: #EAE7DC;">
  <div class="container-fluid gallery-wrapper">
    <div class="row justify-content-center">
      <div class="col-12 content-head">
      </div>
    </div>
    <div class="grid-container">
      <div class="grid-container-1" style="transform: translate3d(-200px, 0px, 0px);">
        <div class="grid-item">
          <img src="https://r.mobirisesite.com/1378487/assets/images/photo-1434626881859-194d67b2b86f.png" alt="">
        </div>
        <div class="grid-item">
          <img src="https://r.mobirisesite.com/1378487/assets/images/photo-1611974789855-9c2a0a7236a3.jpeg" alt="">
        </div>
        <div class="grid-item">
          <img src="https://r.mobirisesite.com/1378487/assets/images/photo-1543286386-713bdd548da4.jpeg" alt="">
        </div>
        <div class="grid-item">
          <img src="https://r.mobirisesite.com/1378487/assets/images/photo-1506818144585-74b29c980d4b.jpeg" alt="">
        </div>
      </div>
      <div class="grid-container-2" style="transform: translate3d(-70px, 0px, 0px);">
        <div class="grid-item">
          <img src="https://r.mobirisesite.com/1378487/assets/images/photo-1532619675605-1ede6c2ed2b0.jpeg" alt="">
        </div>
        <div class="grid-item">
          <img src="https://r.mobirisesite.com/1378487/assets/images/photo-1516321318423-f06f85e504b3.jpeg" alt="">
        </div>
        <div class="grid-item">
          <img src="https://r.mobirisesite.com/1378487/assets/images/photo-1542744173-05336fcc7ad4.jpeg" alt="">
        </div>
        <div class="grid-item">
          <img src="https://r.mobirisesite.com/1378487/assets/images/photo-1504868584819-f8e8b4b6d7e3.jpeg" alt="">
        </div>
      </div>
    </div>
  </div>
</section>
<br>
<h4 class="mbr-section-title mbr-fonts-style align-center mb-0 display-2">
                        <strong>Market News</strong>
                    </h4>
<div id="market-news" class="news-container" style="background-color: #EAE7DC;">
    <p style="background-color: #EAE7DC; color: black; font-weight: bold;">Loading latest news...</p>
</div>

<div class="trending-stocks" style="background-color: #EAE7DC;">
<h4 class="mbr-section-title mbr-fonts-style align-center mb-0 display-2">
                        <strong>Trending Stocks</strong>
                    </h4><br>
    <!-- TradingView Widget BEGIN -->
<div class="tradingview-widget-container">
  <div class="tradingview-widget-container__widget"></div>
  <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-timeline.js" async>
  {
  "feedMode": "all_symbols",
  "isTransparent": false,
  "displayMode": "adaptive",
  "width": "1600",
  "height": "550",
  "colorTheme": "dark",
  "locale": "en"
}
  </script>
</div>
<!-- TradingView Widget END -->
 
</div>
     
<section style="text-align: center; padding: 60px 20px; background-color: #EAE7DC;">
<h4 class="mbr-section-title mbr-fonts-style align-center mb-0 display-2">
                        <strong>What is InvestSmart?</strong>
                    </h4><br> <br><br>
                    <br>
 
  <div style="display: flex; justify-content: center; gap: 80px; flex-wrap: wrap;">
    <div>
      <h3 style="color: #d6863b; font-size: 4rem; font-weight: bold;">99%</h3><br>
      <p style="font-weight: bold; font-size: 2.2rem;">User Satisfaction</p>
    </div>
    <div>
      <h3 style="color: #d6863b; font-size: 4rem; font-weight: bold;">24/7</h3><br>
      <p style="font-weight: bold; font-size: 2.2rem;">Real-Time Updates</p>
    </div>
    <div>
      <h3 style="color: #d6863b; font-size: 4rem; font-weight: bold;">100%</h3><br>
      <p style="font-weight: bold; font-size: 2.2rem;">Secure Tracking</p>
    </div>
  </div>

<br>
<br>
<section data-bs-version="5.1" class="social4 cid-uHKCE3ZB2M" id="follow-us-1-uHKCE3ZB2M" style="background-color: #EAE7DC;">
    <div class="container">
        <div class="media-container-row">
            <div class="col-12">
                <h3 class="mbr-section-title align-center mb-5 mbr-fonts-style display-2">
                    <strong>Join Our Community!</strong>
                </h3>
                <div class="social-list align-center">
                    <a class="iconfont-wrapper bg-facebook m-2 " target="_blank" href="https://www.facebook.com/">
                        <span class="socicon-facebook socicon"></span>
                    </a>
                    <a class="iconfont-wrapper bg-twitter m-2" href="https://x.com/?lang=en" target="_blank">
                        <span class="socicon-twitter socicon"></span>
                    </a>
                    <a class="iconfont-wrapper bg-instagram m-2" href="https://www.instagram.com/" target="_blank">
                        <span class="socicon-instagram socicon"></span>
                    </a>
                           
                </div>
            </div>
        </div>
    </div>
</section>
  <h4 class="mbr-section-title mbr-fonts-style align-center mb-0 display-2" id="contact">
                        <strong>Contact Us</strong>
                    </h4>
</section>

<?php include '../contact.php';?>

<?php include '../includes/footer.php'; ?>
</div>
</div>
</body>

<script>
document.addEventListener("DOMContentLoaded", function () {
    fetch("../api/news.php") // ✅ Fetch news from backend
        .then(response => {
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then(data => {
            if (data.status !== "ok" || !data.articles) {
                throw new Error("Invalid news data");
            }

            let newsContainer = document.getElementById("market-news");
            newsContainer.innerHTML = ""; // Clear existing content

            data.articles.forEach(article => {
                let newsItem = document.createElement("div");
                newsItem.classList.add("news-item");

                newsItem.innerHTML = `
                    <img src="${article.urlToImage || 'default-news.jpg'}" alt="News Image" class="news-image">
                    <div>
                        <a href="${article.url}" target="_blank" class="news-title">${article.title}</a>
                        <p class="news-source">${article.source.name} • ${new Date(article.publishedAt).toLocaleDateString()}</p>
                    </div>
                `;
                newsContainer.appendChild(newsItem);
            });
        })
        .catch(error => {
            document.getElementById("market-news").innerHTML = `<p>⚠️ Failed to load news.</p>`;
            console.error("Error fetching news:", error);
        });
});
</script>


<script>
document.addEventListener("DOMContentLoaded", () => {
    displayWatchlist();
});
</script>

<div id="support-icon" onclick="toggleSupportChat()">
  <img src="chat-icon.png" alt="Support" width="50">
</div>

<script>
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/67b6b76f3e3038190b803130/1ikgrp3hu';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();

function toggleSupportChat() {
  if (typeof Tawk_API !== 'undefined') {
    if (Tawk_API.isChatMinimized()) {
      Tawk_API.maximize();
    } else {
      Tawk_API.minimize();
    }
  }
}
</script>
<script src="https://r.mobirisesite.com/1378487/assets/web/assets/jquery/jquery.min.js?rnd=1744095453804"></script>
  <script src="https://r.mobirisesite.com/1378487/assets/bootstrap/js/bootstrap.bundle.min.js?rnd=1744095453804"></script>
  <script src="https://r.mobirisesite.com/1378487/assets/parallax/jarallax.js?rnd=1744095453804"></script>
  <script src="https://r.mobirisesite.com/1378487/assets/smoothscroll/smooth-scroll.js?rnd=1744095453804"></script>
  <script src="https://r.mobirisesite.com/1378487/assets/ytplayer/index.js?rnd=1744095453804"></script>
  <script src="https://r.mobirisesite.com/1378487/assets/dropdown/js/navbar-dropdown.js?rnd=1744095453804"></script>
  <script src="https://r.mobirisesite.com/1378487/assets/vimeoplayer/player.js?rnd=1744095453805"></script>
  <script src="https://r.mobirisesite.com/1378487/assets/mbr-switch-arrow/mbr-switch-arrow.js?rnd=1744095453805"></script>
  <script src="https://r.mobirisesite.com/1378487/assets/scrollgallery/scroll-gallery.js?rnd=1744095453805"></script>
  <script src="https://r.mobirisesite.com/1378487/assets/theme/js/script.js?rnd=1744095453805"></script>
  <script src="https://r.mobirisesite.com/1378487/assets/formoid.min.js?rnd=1744095453805"></script>

