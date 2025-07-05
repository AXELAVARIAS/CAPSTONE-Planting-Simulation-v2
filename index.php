<?php
  session_start();
  // Fetch real counts from the database
  require_once 'php/connection.php';
  $membersCount = 0;
  $postsCount = 0;
  $modulesCount = 0;
  // Members: count active users
  $result = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM users WHERE status = 'active'");
  if ($row = mysqli_fetch_assoc($result)) {
    $membersCount = (int)$row['cnt'];
  }
  // Posts: count questions
  $result = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM questions");
  if ($row = mysqli_fetch_assoc($result)) {
    $postsCount = (int)$row['cnt'];
  }
  // Modules: count modules
  $result = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM modules");
  if ($row = mysqli_fetch_assoc($result)) {
    $modulesCount = (int)$row['cnt'];
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teen-Anim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/homepage.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
   
</head>
<body>
<?php include 'php/navbar.php'; ?>
    <section class="container-fluid hero-bg d-flex flex-column justify-content-start align-items-center position-relative" style="padding-top: 60px; min-height: 40vh;">
        <div class="text-center text-white d-flex flex-column justify-content-center align-items-center" style="z-index:2;">
          <h1 class="slide-in mb-3">Welcome to Teen-Anim</h1>
          <p class="lead my-2 fs-3" data-aos="fade-up">Empowering the next generation of farmers</p>
          <p class="mb-4 fs-4" data-aos="fade-up" data-aos-delay="100">Join us in exploring the exciting world of agriculture. Learn, grow, and connect with fellow young farmers. Together, we can cultivate a sustainable future.</p>
          <?php if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true): ?>
              <a href="php/signup.php" class="btn btn-lg btn-warning px-5 py-2" data-aos="zoom-in" data-aos-delay="200">Get Started</a>
          <?php else: ?>
              <a href="php/dashboard.php" class="btn btn-lg btn-success px-5 py-2" data-aos="zoom-in" data-aos-delay="200">
                <i class="bi bi-speedometer2"></i> Go to Dashboard
              </a>
          <?php endif; ?>
        </div>
        
    </section>
    <section class="container py-5">
      <div class="row g-4">
        <div class="col-md-4" data-aos="fade-up">
          <div class="card feature-card h-100 text-center p-4">
            <div class="mb-3"><i class="bi bi-rocket-takeoff fs-1 text-success"></i></div>
            <h3>Seamless Start</h3>
            <p>Farming Made Easy. Get started with our simple online resources, tools, and community support to kickstart your journey in agriculture.</p>
            <button class="btn btn-outline-success mt-2" onclick="scrollToSection('module-carousel')">Learn More</button>
          </div>
        </div>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
          <div class="card feature-card h-100 text-center p-4">
            <div class="mb-3"><i class="bi bi-award fs-1 text-success"></i></div>
            <h3>Our Promise</h3>
            <p>Empowering Your Success. Whether you're planting your first seed or scaling your garden, we're here to support every step of the way.</p>
            <button class="btn btn-outline-success mt-2" onclick="scrollToSection('about-page')">Learn More</button>
          </div>
        </div>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
          <div class="card feature-card h-100 text-center p-4">
            <div class="mb-3"><i class="bi bi-lightbulb fs-1 text-success"></i></div>
            <h3>Guided Growth</h3>
            <p>Learn from the Best. Access expert tips, articles, and videos on sustainable practices, modern farming techniques, and how to grow your own food.</p>
            <button class="btn btn-outline-success mt-2" onclick="scrollToSection('community-page')">Learn More</button>
          </div>
        </div>
      </div>
    </section>
    <section id="module-carousel" class="container py-5">
      <h2 class="text-center mb-4" data-aos="fade-right">Start Your Journey with our Farming Modules</h2>
      <div id="modulesCarousel" class="carousel slide" data-bs-ride="carousel" data-aos="zoom-in">
        <div class="carousel-inner">
          <div class="carousel-item active">
            <img src="images/ModulePage.png" class="d-block w-100 carousel-img" alt="Module 1">
            <div class="carousel-caption d-none d-md-block bg-success bg-opacity-75 rounded-3 p-3">
              <h5>Soil Preparation</h5>
              <p>Learn the basics of preparing your soil for planting success.</p>
            </div>
          </div>
          <div class="carousel-item">
            <img src="html/moduleimages/planning.avif" class="d-block w-100 carousel-img" alt="Module 2">
            <div class="carousel-caption d-none d-md-block bg-success bg-opacity-75 rounded-3 p-3">
              <h5>Planning & Planting</h5>
              <p>Discover how to plan your garden and plant efficiently.</p>
            </div>
          </div>
          <div class="carousel-item">
            <img src="html/moduleimages/technique.jpg" class="d-block w-100 carousel-img" alt="Module 3">
            <div class="carousel-caption d-none d-md-block bg-success bg-opacity-75 rounded-3 p-3">
              <h5>Modern Techniques</h5>
              <p>Explore innovative and sustainable farming techniques.</p>
            </div>
          </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#modulesCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#modulesCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>
      <div class="text-center mt-4">
        <a class="btn btn-success" href="php/modulepage.php" role="button">Learn Now</a>
      </div>
    </section>
    <section id="community-page" class="container py-5">
      <div class="row align-items-center">
        <div class="col-md-6" data-aos="fade-right">
          <h2 class="mb-4">Join the Community!</h2>
          <p class="fs-4">Log in to connect with fellow growers, access exclusive resources, and unlock all the tools you need to thrive in farming. Together, we're building a supportive space for learning, sharing, and growing!</p>
          <div class="d-flex gap-4 mt-4">
            <div class="text-center">
              <div class="counter" id="membersCounter">0</div>
              <div>Members</div>
            </div>
            <div class="text-center">
              <div class="counter" id="postsCounter">0</div>
              <div>Posts</div>
            </div>
            <div class="text-center">
              <div class="counter" id="modulesCounter">0</div>
              <div>Modules</div>
            </div>
          </div>
          <a class="btn btn-success mt-5" href="php/Forum/community.php" role="button">Join Now</a>
        </div>
        <div class="col-md-6 text-center" data-aos="fade-left">
          <img src="images/AboutPage.png" alt="about" class="img-fluid rounded-4 shadow" style="max-width: 90%;">
        </div>
      </div>
    </section>
    <section id="about-page" class="container py-5">
      <h2 class="text-center mb-4" data-aos="fade-up">About Teen-Anim</h2>
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="accordion" id="aboutAccordion">
            <div class="accordion-item faq-item">
              <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                  Our Mission
                </button>
              </h2>
              <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#aboutAccordion">
                <div class="accordion-body">
                  Empowering young minds to lead the future of sustainable farming. At Teen-Anim, we believe agriculture should be exciting, innovative, and accessible for today's youth. Our mission is to spark interest in farming by offering insights, resources, and a supportive community to explore modern agriculture.
                </div>
              </div>
            </div>
            <div class="accordion-item faq-item">
              <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                  Why Focus on Youth?
                </button>
              </h2>
              <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#aboutAccordion">
                <div class="accordion-body">
                  Young people need guidance on where to start, how to grow, and what role technology can play in farming. They want to understand sustainable practices, build practical skills, and discover the opportunities agriculture offers for a brighter, greener future.
                </div>
              </div>
            </div>
            <div class="accordion-item faq-item">
              <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                  What Does Teen-Anim Provide?
                </button>
              </h2>
              <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#aboutAccordion">
                <div class="accordion-body">
                  Teen-Anim provides youth with the knowledge and tools to make informed decisions, experiment with hands-on techniques, and see the impact they can make through farming. We're here to cultivate the next generation of leaders in agriculture, one step at a time.
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section id="contact-page" class="container py-5">
      <div class="row align-items-center">
        <div class="col-md-6" data-aos="fade-right">
          <h2 class="mb-3">Contact Us</h2>
          <p class="fs-5">At Teen-Anim, we're always looking to improve and grow, just like you. Have ideas on how we can make farming more exciting for young people? Want to see new features, resources, or topics covered? We'd love to hear your thoughts!</p>
          <p class="fs-5 mb-1"><strong>Email:</strong> teenanim2024@gmail.com</p>
          <p class="fs-5"><strong>Phone Number:</strong> 09956957814</p>
        </div>
        <div class="col-md-6" data-aos="fade-left">
          <h3 class="mb-3">Share Your Ideas</h3>
          <form id="suggestionForm" action="php/suggestion.php" method="post">
            <label for="suggestion" class="form-label">Enter your suggestions:</label>
            <textarea class="form-control" id="suggestion" rows="7" name="message" required></textarea>
            <button type="submit" class="btn btn-success mt-3">Send Suggestion</button>
            <div id="suggestionMsg" class="mt-2"></div>
          </form>
        </div>
      </div>
    </section>
    <div class="contact-fab" onclick="scrollToSection('contact-page')" title="Contact Us">
      <i class="bi bi-chat-dots"></i>
    </div>
    <footer>
      <div class="container-fluid footer-bg py-3 mt-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center px-5">
          <p class="mb-2 mb-md-0">Copyright 2024</p>
          <img src="images/clearteenalogo.png" class="teenanimlogo mb-2" alt="TEENANIM LOGO">
          <p class="mb-0">Terms & Conditions / Privacy Policy</p>
        </div>
      </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.js"></script>
    <script>
      // AOS init
      AOS.init();

      // Animated counters
      function animateCounter(id, end, duration) {
        let start = 0;
        let step = Math.ceil(end / (duration / 20));
        let el = document.getElementById(id);
        let interval = setInterval(function() {
          start += step;
          if (start >= end) {
            el.textContent = end;
            clearInterval(interval);
          } else {
            el.textContent = start;
          }
        }, 20);
      }
      // Example values, replace with dynamic values if available
      document.addEventListener('DOMContentLoaded', function() {
        animateCounter('membersCounter', <?php echo $membersCount; ?>, 1200);
        animateCounter('postsCounter', <?php echo $postsCount; ?>, 1200);
        animateCounter('modulesCounter', <?php echo $modulesCount; ?>, 1200);
      });

      // Scroll to section
      function scrollToSection(id) {
        const el = document.getElementById(id);
        if (el) {
          el.scrollIntoView({ behavior: 'smooth' });
        }
      }

      // AJAX suggestion form
      $(function() {
        $('#suggestionForm').on('submit', function(e) {
          e.preventDefault();
          var form = $(this);
          var msgDiv = $('#suggestionMsg');
          $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
              msgDiv.html('<div class="alert alert-success">Thank you for your suggestion!</div>');
              form[0].reset();
            },
            error: function() {
              msgDiv.html('<div class="alert alert-danger">There was an error. Please try again later.</div>');
            }
          });
        });
      });
    </script>
</body>
</html>