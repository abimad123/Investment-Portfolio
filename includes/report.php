
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/images/logo.png" rel="icon" type="image/png" style="zoom: 15.0;">
    <title>InvestSmart-About</title>
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
</head>
<body>
<section data-bs-version="5.1" class="article01 cid-uHKCE3W4o2" id="about-us-1-uHKCE3W4o2" style="background-color: #EAE7DC;">
	<div class="container">
		<div class="row justify-content-center">
			<div class="card col-md-12 col-lg-12">
				<div class="card-wrapper" style="background-color: #EAE7DC;">
					<div class="row">
						<div class="image-wrapper col-12 col-sm-6 justify-content-center">
							<img src="https://r.mobirisesite.com/1378487/assets/images/photo-1607703703520-bb638e84caf2.jpeg" alt="">
						</div>
						<div class="image-wrapper col-12 col-sm-6 justify-content-center">
							<img src="https://r.mobirisesite.com/1378487/assets/images/photo-1518186285589-2f7649de83e0.jpeg" alt="">
						</div>
					</div>

					<div class="card-box align-left mb-3 card-content-text">
						<h4 class="card-title mbr-fonts-style mbr-white mb-0 display-5">
							<strong>Meet InvestSmart Team</strong>
						</h4>
						<p class="mbr-text mbr-fonts-style mt-3 mb-0 display-7">We are a passionate group of finance enthusiasts, tech wizards, and data nerds. Our mission? To make personal investing as easy as pie, or at least as easy as ordering pizza online.</p>

						<p class="mbr-text mbr-fonts-style mt-3 mb-0 display-7">With years of experience in the financial sector, we know the ins and outs of investment tracking. Our vision is to empower individuals to take charge of their financial futures without the headache.</p>

						<p class="mbr-text mbr-fonts-style mt-3 mb-0 display-7">Join us as we redefine the investment experience, one dashboard at a time!</p>
					</div>

				</div>
			</div>
		</div>
	</div>
</section>

<section data-bs-version="5.1" class="list1 cid-uHKCE3Wjif" id="faq-1-uHKCE3Wjif"  style="background-color: #EAE7DC;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-12 col-lg-10 m-auto">
                <div class="content">
                    <div class="row justify-content-center mb-5">
                        <div class="col-12 content-head">
                            <div class="mbr-section-head">
                                <h4 class="mbr-section-title mbr-fonts-style align-center mb-0 display-2">
                                    <strong>Frequently Asked Questions</strong>
                                </h4>
                                
                            </div>
                        </div>
                    </div>
                    <div id="bootstrap-accordion_6" class="panel-group accordionStyles accordion" role="tablist" aria-multiselectable="true">
                        <div class="card">
                            <div class="card-header" role="tab" id="headingOne">
                                <a role="button" class="panel-title collapsed" data-toggle="collapse" data-bs-toggle="collapse" data-core="" href="#collapse1_6" aria-expanded="false" aria-controls="collapse1">
                                    <h6 class="panel-title-edit mbr-semibold mbr-fonts-style mb-0 display-5">What is InvestSmart?</h6>
                                    <span class="sign mbr-iconfont mobi-mbri-arrow-down"></span>
                                </a>
                            </div>
                            <div id="collapse1_6" class="panel-collapse noScroll collapse" role="tabpanel" aria-labelledby="headingOne" data-parent="#accordion" data-bs-parent="#bootstrap-accordion_6">
                                <div class="panel-body">
                                    <p class="mbr-fonts-style panel-text display-7">A cutting-edge platform for tracking investments.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" role="tab" id="headingOne">
                                <a role="button" class="panel-title collapsed" data-toggle="collapse" data-bs-toggle="collapse" data-core="" href="#collapse2_6" aria-expanded="false" aria-controls="collapse2">
                                    <h6 class="panel-title-edit mbr-semibold mbr-fonts-style mb-0 display-5">How secure is my data?</h6>
                                    <span class="sign mbr-iconfont mobi-mbri-arrow-down"></span>
                                </a>
                            </div>
                            <div id="collapse2_6" class="panel-collapse noScroll collapse" role="tabpanel" aria-labelledby="headingOne" data-parent="#accordion" data-bs-parent="#bootstrap-accordion_6">
                                <div class="panel-body">
                                    <p class="mbr-fonts-style panel-text display-7">Top-notch encryption keeps your info safe.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" role="tab" id="headingOne">
                                <a role="button" class="panel-title collapsed" data-toggle="collapse" data-bs-toggle="collapse" data-core="" href="#collapse3_6" aria-expanded="false" aria-controls="collapse3">
                                    <h6 class="panel-title-edit mbr-semibold mbr-fonts-style mb-0 display-5">Can I access it on mobile?</h6>
                                    <span class="sign mbr-iconfont mobi-mbri-arrow-down"></span>
                                </a>
                            </div>
                            <div id="collapse3_6" class="panel-collapse noScroll collapse" role="tabpanel" aria-labelledby="headingOne" data-parent="#accordion" data-bs-parent="#bootstrap-accordion_6">
                                <div class="panel-body">
                                    <p class="mbr-fonts-style panel-text display-7">Absolutely! It's mobile-friendly and responsive.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" role="tab" id="headingOne">
                                <a role="button" class="panel-title collapsed" data-toggle="collapse" data-bs-toggle="collapse" data-core="" href="#collapse4_6" aria-expanded="false" aria-controls="collapse4">
                                    <h6 class="panel-title-edit mbr-semibold mbr-fonts-style mb-0 display-5">Is there a money-back guarantee?</h6>
                                    <span class="sign mbr-iconfont mobi-mbri-arrow-down"></span>
                                </a>
                            </div>
                            <div id="collapse4_6" class="panel-collapse noScroll collapse" role="tabpanel" aria-labelledby="headingOne" data-parent="#accordion" data-bs-parent="#bootstrap-accordion_6">
                                <div class="panel-body">
                                    <p class="mbr-fonts-style panel-text display-7">Yes, 30-day satisfaction guarantee included.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" role="tab" id="headingOne">
                                <a role="button" class="panel-title collapsed" data-toggle="collapse" data-bs-toggle="collapse" data-core="" href="#collapse5_6" aria-expanded="false" aria-controls="collapse5">
                                    <h6 class="panel-title-edit mbr-semibold mbr-fonts-style mb-0 display-5">How do I get started?</h6>
                                    <span class="sign mbr-iconfont mobi-mbri-arrow-down"></span>
                                </a>
                            </div>
                            <div id="collapse5_6" class="panel-collapse noScroll collapse" role="tabpanel" aria-labelledby="headingOne" data-parent="#accordion" data-bs-parent="#bootstrap-accordion_6">
                                <div class="panel-body">
                                    <p class="mbr-fonts-style panel-text display-7">Sign up and start tracking your investments!</p>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</body>
</html>
<?php include '../includes/footer.php'; ?>