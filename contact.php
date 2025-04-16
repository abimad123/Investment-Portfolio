

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact-Portfolio</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
</head>
<body>
    <div class="cont">
        <div class="d-flex justify-content-between align-items-center">
    
            <!-- <a href="index.php" class="btn btn-primary">Back</a> -->
        </div>    
        <form id="contact-form" class="php-email-form">
  <div class="row">
    <div class="col-md-6 form-group">
      <input type="text" name="name" class="form-control" placeholder="Your Name" required>
    </div>
    <div class="col-md-6 form-group mt-3 mt-md-0">
      <input type="email" name="email" class="form-control" placeholder="Your Email" required>
    </div>
  </div>
  <div class="form-group mt-3">
    <input type="text" name="subject" class="form-control" placeholder="Subject" required>
  </div>
  <div class="form-group mt-3">
    <textarea name="message" class="form-control" rows="8" placeholder="Message" required></textarea>
  </div>
  <div class="text-center">
    <button type="submit" class="btn btn-primary">Send Message</button>
  </div>
  <div id="form-response" class="mt-3 text-success text-center" style="display: none; font-size: 1.2em;">
    ✅ Message sent successfully!
  </div>
</form>

<script>
  const form = document.getElementById("contact-form");
  const responseMessage = document.getElementById("form-response");

  form.addEventListener("submit", async function (e) {
    e.preventDefault();

    const formData = new FormData(form);
    const response = await fetch("https://formspree.io/f/xdkeynqp", {
      method: "POST",
      body: formData,
      headers: {
        'Accept': 'application/json'
      }
    });

    if (response.ok) {
      responseMessage.style.display = "block";
      form.reset();
    } else {
      responseMessage.innerText = "❌ Something went wrong. Please try again.";
      responseMessage.classList.remove("text-success");
      responseMessage.classList.add("text-danger");
      responseMessage.style.display = "block";
    }
  });
</script>


    </div>

   <!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>