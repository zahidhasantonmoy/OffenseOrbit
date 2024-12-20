<?php include("includes/header.php"); ?>
<?php
// Database connection
$host = "sql302.infinityfree.com";
$username = "if0_37729401";
$password = "xOhEjKEk4uwo6AX";
$database = "if0_37729401_crms";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch statistics by incident type
$incident_data = [];
$sql = "SELECT incident_type, COUNT(*) as total FROM crime_reports GROUP BY incident_type";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $incident_data[] = $row;
    }
}


// Fetch the latest 3 news articles from the database
$sql = "SELECT * FROM news ORDER BY created_at DESC LIMIT 3";
$result = $conn->query($sql);

// Fetch data into an array
$posts = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
} else {
    $message = "No news articles available.";
}

// Handle Comment Post Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_submit'])) {
    $news_id = $_POST['news_id'];
    $author = $_POST['author'];
    $comment = $_POST['comment'];

    // Insert the comment into the database
    $comment_sql = "INSERT INTO comments (news_id, author, comment) VALUES ('$news_id', '$author', '$comment')";
    if ($conn->query($comment_sql) === TRUE) {
        $message = "Comment posted successfully!";
    } else {
        $message = "Error posting comment: " . $conn->error;
    }
}



// Fetch wanted persons
$wanted_persons = [];
$sql = "SELECT name, last_known_location, reward, image_path FROM wanted_list ORDER BY id DESC LIMIT 20"; // Fetch up to 20 entries
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $wanted_persons[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OffenseOrbit - Homepage</title>







<style>
        /* Unique CSS for Hero Banner */
        .mealmap-hero {
            position: relative;
            width: 100%;
            height: 100vh;
            overflow: hidden;
        }

        .mealmap-hero .carousel {
            width: 100%;
            height: 100%;
            display: flex;
            transition: transform 1s ease;
        }

        .mealmap-hero .carousel img {
            width: 100%;
            height: 100vh;
            object-fit: cover;
            display: none; /* Hide all images by default */
        }

        .mealmap-hero .carousel img.active {
            display: block; /* Only display the active image */
        }

        .mealmap-hero .hero-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
            z-index: 10;
            padding: 0 15px;
        }

        .mealmap-hero .hero-content h1 {
            font-size: 3em;
            margin-bottom: 20px;
        }

        /* Call-to-Action Buttons */
        .mealmap-hero .hero-content .cta-buttons {
            margin-top: 20px;
        }

        .mealmap-hero .hero-content .cta-buttons a {
            display: inline-block;
            margin: 10px 15px;
            padding: 15px 25px;
            background-color: #46c4d8de;
            color: white;
            text-decoration: none;
            font-size: 1.2em;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            box-sizing: border-box;
        }

        /* Hover effect for CTA buttons */
        .mealmap-hero .hero-content .cta-buttons a:hover {
            background-color: #004256de;
            transform: scale(1.1); /* Button size increase on hover */
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .mealmap-hero .hero-content h1 {
                font-size: 2em;
            }

            .mealmap-hero .hero-content .cta-buttons a {
                font-size: 1.1em;
                padding: 12px 20px;
            }
        }

        @media (max-width: 480px) {
            .mealmap-hero .hero-content h1 {
                font-size: 1.6em;
            }

            .mealmap-hero .hero-content .cta-buttons a {
                font-size: 1em;
                padding: 10px 15px;
            }
        }

        /* Simple carousel controls */
        .mealmap-hero .carousel-nav {
            position: absolute;
            top: 50%;
            width: 100%;
            display: flex;
            justify-content: space-between;
            z-index: 20;
        }

        .mealmap-hero .carousel-nav button {
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
        }

        .mealmap-hero .carousel-nav button:hover {
            background-color: rgba(0, 0, 0, 0.7);
        }
     
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <!-- Hero Banner Section -->
    <div class="mealmap-hero">
        <!-- Image Carousel -->
        <div class="carousel">
            <img src="1.png" alt="Meal 1" class="active">
            <img src="2.png" alt="Meal 2">
            <img src="3.png" alt="Meal 3">
        </div>

        <!-- Hero Content (Text & CTA buttons) -->
        <div class="hero-content">
            <h1 >Bridging Justice and Community with Trust and Technology</h1>

            <div class="cta-buttons">
                <a href="/others/about.php">About Us  </a>
                <a href="/others/updatenews.php">News Update</a>
                <a href="/others/wanted.php">Wanted List</a>
            </div>
        </div>

        <!-- Carousel Navigation -->
        <div class="carousel-nav">
            <button id="prevBtn">❮</button>
            <button id="nextBtn">❯</button>
        </div>
    </div>

    <script>
        // JavaScript for Carousel functionality
        const carousel = document.querySelector('.carousel');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const images = document.querySelectorAll('.carousel img');
        let index = 0;

        // Function to update carousel position
        function updateCarousel() {
            // Remove 'active' class from all images
            images.forEach(img => img.classList.remove('active'));

            // Add 'active' class to the current image
            images[index].classList.add('active');
        }

        // Event listener for next and previous buttons
        nextBtn.addEventListener('click', () => {
            index = (index + 1) % images.length;
            updateCarousel();
        });

        prevBtn.addEventListener('click', () => {
            index = (index - 1 + images.length) % images.length;
            updateCarousel();
        });

        // Automatically change images every 5 seconds
        setInterval(() => {
            index = (index + 1) % images.length;
            updateCarousel();
        }, 5000); // 5000ms = 5 seconds

        // Adjust the carousel width on window resize
        window.addEventListener('resize', updateCarousel);
    </script>





    <style>
        /* General Styles */
       
        /* Marquee Section */
        .marquee-container {
            background: linear-gradient(90deg, #3498db, #2ecc71);
            color: white;
            font-size: 1rem;
            padding: 10px 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .marquee-content {
            display: flex;
            overflow: hidden;
            white-space: nowrap;
            animation: marquee 25s linear infinite;
        }

        .marquee-content span {
            margin-right: 50px;
        }

        @keyframes marquee {
            0% {
                transform: translateX(100%);
            }
            100% {
                transform: translateX(-100%);
            }
        }

        /* Section Container */
        .section {
            margin: 20px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .section h2 {
            margin: 0;
            padding-bottom: 10px;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
        }

        .section p {
            line-height: 1.6;
            color: #333;
        }
    </style>
</head>
<body>

    <!-- Marquee Section -->
    <div class="marquee-container">
        <div class="marquee-content">
            <span>Breaking News: New safety guidelines released for public transport.</span>
            <span>Update: Cybercrime awareness campaign scheduled for next week.</span>
            <span>Alert: Avoid traffic on Main Street due to an ongoing investigation.</span>
        </div>
    </div>









<!-- Redesigned Public and Police Services Section -->
<section id="services" style="background-color: #f9f9f9; padding: 50px 20px;">
  <div style="text-align: center; margin-bottom: 30px;">
    <h2 style="font-size: 2.5rem; color: #2c3e50;">Public and Police Services</h2>
    <p style="color: #7f8c8d;">Explore the services we provide to ensure public safety and law enforcement.</p>
  </div>
  
  <div style="display: flex; justify-content: center; gap: 30px; flex-wrap: wrap;">

    <!-- Public Services -->
    <div style="background-color: #ffffff; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 300px; padding: 20px; text-align: center;">
      <h3 style="font-size: 1.5rem; color: #3498db; margin-bottom: 15px;">Public Services</h3>
      <button id="public-services-btn" style="background-color: #3498db; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; font-size: 1rem;">Explore Services</button>
      <ul id="public-services-list" style="display: none; margin-top: 20px; text-align: left; color: #2c3e50;">
        <li><a href="/other/lost.php" style="text-decoration: none; color: #3498db;">Lost </a></li>
        <li><a href="/other/lost.php" style="text-decoration: none; color: #3498db;"> Found</a></li>
        <li><a href="/dashboard/citizen_dashboard.php" style="text-decoration: none; color: #3498db;">Crime Reporting</a></li>
        <li><a href="others/wanted.php" style="text-decoration: none; color: #3498db;">Wanted list</a></li>
        <li><a href="/others/news.php" style="text-decoration: none; color: #3498db;">News </a></li>
        <li><a href="/others/about.php" style="text-decoration: none; color: #3498db;">About Us</a></li>
      </ul>
    </div>

    <!-- Police Services -->
    <div style="background-color: #ffffff; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 300px; padding: 20px; text-align: center;">
      <h3 style="font-size: 1.5rem; color: #e74c3c; margin-bottom: 15px;">Police Services</h3>
      <button id="police-services-btn" style="background-color: #e74c3c; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; font-size: 1rem;">Explore Services</button>
      <ul id="police-services-list" style="display: none; margin-top: 20px; text-align: left; color: #2c3e50;">
        <li><a href="/other/lost.php" style="text-decoration: none; color: #3498db;">Lost </a></li>
        <li><a href="/other/lost.php" style="text-decoration: none; color: #3498db;"> Found</a></li>
        <li><a href="/dashboard/officer_dashboard.php" style="text-decoration: none; color: #3498db;">Crime Reporting</a></li>
        <li><a href="others/wanted.php" style="text-decoration: none; color: #3498db;">Wanted list</a></li>
        <li><a href="/others/news.php" style="text-decoration: none; color: #3498db;">News </a></li>
        <li><a href="/others/about.php" style="text-decoration: none; color: #3498db;">About Us</a></li>
      </ul>
    </div>

  </div>
</section>

<script>
  // Toggle Public Services List
  document.getElementById("public-services-btn").addEventListener("click", function () {
    const list = document.getElementById("public-services-list");
    list.style.display = list.style.display === "none" || list.style.display === "" ? "block" : "none";
  });

  // Toggle Police Services List
  document.getElementById("police-services-btn").addEventListener("click", function () {
    const list = document.getElementById("police-services-list");
    list.style.display = list.style.display === "none" || list.style.display === "" ? "block" : "none";
  });
</script>






<!-- Photo and Video Gallery Section -->
<section id="gallery" style="padding: 50px 20px; background-color: #f4f6f7;">
    <div style="text-align: center; margin-bottom: 30px;">
      <h2 style="font-size: 2.5rem; color: #2c3e50;">Photo and Video Gallery</h2>
      <p style="color: #7f8c8d;">Explore the latest images and videos from police-community programs, events, and awareness campaigns.</p>
    </div>
  
    <!-- Gallery Container -->
    <div class="gallery-container" style="display: flex; justify-content: space-between; gap: 20px; align-items: center;">
      <!-- Video on Left -->
      <div class="video-container" style="flex: 1; text-align: center;">
        <iframe width="560" height="315" src="https://www.youtube.com/embed/pixdywWX6qc?si=esFc_4f1RcpnCcOj" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        <p style="margin-top: 10px; color: #2c3e50;">Police Outreach Video - A Community Awareness Program</p>
      </div>
      
      <!-- Image on Right -->
      <div class="image-container" style="flex: 1; text-align: center;">
        <img id="galleryImage" src="pg.jpg" alt="Community Awareness Campaign" style="width: 80%; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
        <p id="imageDescription" style="margin-top: 10px; color: #2c3e50;">Community Awareness Campaign</p>
        <!-- Next Image Button -->
        <button onclick="nextImage()" style="margin-top: 10px; background-color: #3498db; color: white; padding: 10px 20px; border-radius: 5px; font-size: 1rem; border: none; cursor: pointer;">Next Image</button>
      </div>
    </div>
  </section>
  
  <script>
    // Array of images and descriptions
    const images = [
      { src: "images/gallery1.jpg", description: "Community Awareness Campaign" },
      { src: "images/gallery2.jpg", description: "Crime Prevention Campaign" },
      { src: "images/gallery3.jpg", description: "Police Training Event" },
      { src: "images/gallery4.jpg", description: "Community Event" }
    ];
  
    let currentIndex = 0;
  
    // Function to change the image and description
    function nextImage() {
      currentIndex = (currentIndex + 1) % images.length; // Loop back to the first image after the last one
      document.getElementById("galleryImage").src = images[currentIndex].src;
      document.getElementById("imageDescription").textContent = images[currentIndex].description;
    }
  </script>
  



<style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px;
            padding: 20px;
            flex-wrap: wrap;
        }

        /* Emergency Contacts and Statistics Styling */
        .emergency-container, .stats-container {
            flex: 1;
            min-width: 300px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .emergency-header, .stats-header {
            text-align: center;
            padding: 15px 0;
            background-color: #007bff;
            color: white;
            border-radius: 10px 10px 0 0;
        }

        .emergency-header h2, .stats-header h2 {
            margin: 0;
            font-size: 22px;
        }

        .emergency-list {
            margin: 0;
            padding: 20px;
            list-style: none;
        }

        .emergency-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        .emergency-item:last-child {
            border-bottom: none;
        }

        .emergency-item h3 {
            margin: 0;
            font-size: 16px;
            color: #333;
        }

        .emergency-item a {
            font-size: 18px;
            font-weight: bold;
            color: #e3342f;
            text-decoration: none;
        }

        .emergency-item a:hover {
            text-decoration: underline;
        }

        /* Crime Statistics Styling */
        .stats-header p {
            color: #555;
        }

        .progress-bar-section {
            margin-top: 20px;
        }

        .progress-item {
            margin-bottom: 20px;
        }

        .progress-label {
            font-size: 16px;
            color: #555;
            margin-bottom: 5px;
        }

        .progress-bar {
            background-color: #e0e0e0;
            border-radius: 20px;
            overflow: hidden;
            position: relative;
            height: 20px;
        }

        .progress-fill {
            height: 100%;
            border-radius: 20px;
            line-height: 20px;
            text-align: center;
            color: white;
            position: absolute;
        }

        .data-counter {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .counter-box {
            text-align: center;
            flex: 1;
            margin: 0 10px;
        }

        .counter-box h3 {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
        }

        .counter-box p {
            color: #777;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Emergency Contacts -->
    <div class="emergency-container">
        <div class="emergency-header">
            <h2>Bangladesh Emergency Contacts</h2>
        </div>
        <ul class="emergency-list">
            <li class="emergency-item">
                <h3>National Emergency Service</h3>
                <a href="tel:999">999</a>
            </li>
            <li class="emergency-item">
                <h3>Fire Service</h3>
                <a href="tel:16163">16163</a>
            </li>
            <li class="emergency-item">
                <h3>Ambulance</h3>
                <a href="tel:199">199</a>
            </li>
            <li class="emergency-item">
                <h3>Women and Child Helpline</h3>
                <a href="tel:109">109</a>
            </li>
            <li class="emergency-item">
                <h3>Cyber Crime Helpline</h3>
                <a href="tel:12345">12345</a>
            </li>
            <li class="emergency-item">
                <h3>Tourism Police</h3>
                <a href="tel:98765">98765</a>
            </li>
            <li class="emergency-item">
                <h3>Road Accident Helpline</h3>
                <a href="tel:1122">1122</a>
            </li>
            <li class="emergency-item">
                <h3>Anti-Corruption Hotline</h3>
                <a href="tel:106">106</a>
            </li>
            <li class="emergency-item">
                <h3>Gas Emergency Hotline</h3>
                <a href="tel:16485">16485</a>
            </li>
            <li class="emergency-item">
                <h3>Electricity Emergency</h3>
                <a href="tel:19113">19113</a>
            </li>
        </ul>
    </div>

    <!-- Crime Statistics -->
    <div class="stats-container">
        <div class="stats-header">
            <h2>Crime Statistics</h2>
            <p>A snapshot of recent crime trends in Bangladesh</p>
        </div>

        <div class="progress-bar-section">
            <?php foreach ($incident_data as $incident) : ?>
                <?php
                // Calculate percentage
                $total_cases = array_sum(array_column($incident_data, 'total'));
                $percentage = round(($incident['total'] / $total_cases) * 100, 2);
                ?>
                <div class="progress-item">
                    <div class="progress-label">
                        <?php echo htmlspecialchars($incident['incident_type']); ?> - <?php echo $percentage; ?>% Reported Cases
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $percentage; ?>%; background-color: <?php echo '#' . dechex(rand(0, 16777215)); ?>;">
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        
    </div>
</div>



 <style>
    /* Unique Styles for Wanted List with Animation */
    .wanted-container {
        max-width: 1200px;
        margin: 50px auto;
        padding: 20px;
        background-color: #f4f4f4;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .wanted-header {
        text-align: center;
        margin-bottom: 30px;
        animation: fadeIn 1s ease-in-out;
    }

    .wanted-header h2 {
        font-size: 2.5rem;
        color: #2c3e50;
    }

    .wanted-header p {
        color: #7f8c8d;
        font-size: 1rem;
    }

    .wanted-slideshow {
        display: flex;
        gap: 20px;
        transition: transform 1s ease-in-out;
    }

    .wanted-card {
        flex: 1;
        min-width: 250px;
        max-width: 250px;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        text-align: center;
        padding: 15px;
    }

    .wanted-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .wanted-card h3 {
        margin: 15px 0 10px;
        font-size: 1.5rem;
        color: #3498db;
    }

    .wanted-card p {
        font-size: 1rem;
        color: #555;
        margin: 5px 0;
    }

    .wanted-controls {
        text-align: center;
        margin-top: 20px;
    }

    .wanted-controls button {
        background-color: #3498db;
        color: white;
        border: none;
        padding: 10px 20px;
        font-size: 1rem;
        border-radius: 5px;
        cursor: pointer;
        margin: 0 10px;
        transition: background-color 0.3s;
    }

    .wanted-controls button:hover {
        background-color: #2980b9;
    }

    @media (max-width: 768px) {
        .wanted-card {
            max-width: 200px;
            min-width: 200px;
        }

        .wanted-card img {
            height: 150px;
        }
    }
</style>

<div class="wanted-container">
    <div class="wanted-header">
        <h2>Most Wanted Persons</h2>
        <p>Help us locate these individuals by providing any useful information.</p>
    </div>

    <div class="wanted-slideshow" id="wantedSlideshow">
        <?php foreach ($wanted_persons as $person) : ?>
            <div class="wanted-card">
                <img src="<?php echo htmlspecialchars('others/' . $person['image_path']); ?>" alt="Wanted Person">

                <h3><?php echo htmlspecialchars($person['name']); ?></h3>
                <p><strong>Last Known Location:</strong> <?php echo htmlspecialchars($person['last_known_location']); ?></p>
                <p><strong>Reward:</strong> ৳<?php echo number_format($person['reward'], 2); ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="wanted-controls">
        <button id="prevSlide">Previous</button>
        <button id="nextSlide">Next</button>
    </div>
</div>

<script>
    const slideshow = document.getElementById("wantedSlideshow");
    const cards = document.querySelectorAll(".wanted-card");
    const visibleCount = 4; // Number of cards visible at a time
    const cardWidth = cards[0].offsetWidth + 20; // Include gap between cards
    let currentIndex = 0;

    // Adjust the slideshow's total width
    slideshow.style.width = `${cardWidth * cards.length}px`;

    // Go to the next slide
    document.getElementById("nextSlide").addEventListener("click", () => {
        if (currentIndex < cards.length - visibleCount) {
            currentIndex++;
            slideshow.style.transform = `translateX(-${cardWidth * currentIndex}px)`;
        }
    });

    // Go to the previous slide
    document.getElementById("prevSlide").addEventListener("click", () => {
        if (currentIndex > 0) {
            currentIndex--;
            slideshow.style.transform = `translateX(-${cardWidth * currentIndex}px)`;
        }
    });

    // Auto-scroll every 5 seconds
    setInterval(() => {
        if (currentIndex < cards.length - visibleCount) {
            currentIndex++;
        } else {
            currentIndex = 0; // Reset to the beginning
        }
        slideshow.style.transform = `translateX(-${cardWidth * currentIndex}px)`;
    }, 5000);
</script>






  <style>
   .feedback-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.15);
        }

        .feedback-title {
            text-align: center;
            color: #34495e;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .feedback-subtitle {
            text-align: center;
            color: #7f8c8d;
            margin-bottom: 30px;
        }

        .feedback-form {
            margin-top: 20px;
        }

        .feedback-field-group {
            margin-bottom: 20px;
        }

        .feedback-label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #2c3e50;
        }

        .feedback-input,
        .feedback-textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccd1d1;
            border-radius: 6px;
            font-size: 16px;
            color: #2c3e50;
        }

        .feedback-textarea {
            resize: none;
            height: 120px;
        }

        .feedback-button {
            background-color: #1abc9c;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 6px;
            width: 100%;
            transition: background-color 0.3s;
        }

        .feedback-button:hover {
            background-color: #16a085;
        }

        .feedback-message-success,
        .feedback-message-error {
            text-align: center;
            margin: 20px 0;
            font-size: 16px;
            font-weight: bold;
        }

        .feedback-message-success {
            color: #27ae60;
        }

        .feedback-message-error {
            color: #e74c3c;
        }
    </style>
</head>
<body>

<div class="feedback-container">
    <h2 class="feedback-title">Feedback and Suggestions</h2>
    <p class="feedback-subtitle">We value your feedback to improve our services and usability.</p>

    <?php
    // Database connection
    $host = "sql302.infinityfree.com";
    $username = "if0_37729401";
    $password = "xOhEjKEk4uwo6AX";
    $database = "if0_37729401_crms";

    $conn = new mysqli($host, $username, $password, $database);

    if ($conn->connect_error) {
        die("<p class='feedback-message-error'>Database connection failed: " . $conn->connect_error . "</p>");
    }

    // PHP Script for handling form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
        $contact_number = htmlspecialchars($_POST['contact_number']);
        $feedback = htmlspecialchars($_POST['feedback']);

        // Insert feedback into the database
        $sql = "INSERT INTO feedback (name, email, contact_number, feedback) 
                VALUES ('$name', '$email', '$contact_number', '$feedback')";

        if ($conn->query($sql) === TRUE) {
            echo "<p class='feedback-message-success'>Thank you for your feedback!</p>";
        } else {
            echo "<p class='feedback-message-error'>Error: " . $conn->error . "</p>";
        }
    }

    $conn->close();
    ?>

    <!-- Feedback Form -->
    <form method="post" action="" class="feedback-form">
        <div class="feedback-field-group">
            <label for="name" class="feedback-label">Name:</label>
            <input type="text" id="name" name="name" class="feedback-input" required placeholder="Your Full Name">
        </div>

        <div class="feedback-field-group">
            <label for="email" class="feedback-label">Email:</label>
            <input type="email" id="email" name="email" class="feedback-input" required placeholder="Your Email Address">
        </div>

        <div class="feedback-field-group">
            <label for="contact_number" class="feedback-label">Contact Number:</label>
            <input type="text" id="contact_number" name="contact_number" class="feedback-input" placeholder="Your Contact Number">
        </div>

        <div class="feedback-field-group">
            <label for="feedback" class="feedback-label">Feedback or Suggestions:</label>
            <textarea id="feedback" name="feedback" class="feedback-textarea" required placeholder="Write your feedback or suggestions here..."></textarea>
        </div>

        <button type="submit" class="feedback-button">Submit Feedback</button>
    </form>
</div>






<!-- Map section -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css">
<style>
    /* General Styles */
    .map-section {
        display: flex;
        justify-content: space-between;
        padding: 40px 0;
        background: #f9f9f9;
        margin: 40px 0;
    }

    .map-container {
        width: 48%;
        background: #ffffff;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .map-header {
        font-size: 1.8em;
        font-weight: bold;
        color: #333;
        margin-bottom: 20px;
        text-align: center;
    }

    #map1, #map2 {
        width: 100%;
        height: 500px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .map-description {
        margin-top: 20px;
        font-size: 1.1em;
        color: #666;
        text-align: center;
    }
</style>

<!-- Interactive Map Section -->
<div class="map-section">
    <!-- Police Stations Block -->
    <div class="map-container">
        <div class="map-header">Police Stations Locations</div>
        <div id="map1"></div>
        <div class="map-description">Locate police stations for emergency services in your area.</div>
    </div>

    <!-- Dangerous Zones Block -->
    <div class="map-container">
        <div class="map-header">Dangerous Zones</div>
        <div id="map2"></div>
        <div class="map-description">View areas categorized by danger severity: Dangerous, More Dangerous, Very Dangerous.</div>
    </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<script>
    // Initialize the map for police stations
    const map1 = L.map('map1').setView([23.8103, 90.4125], 12); // Dhaka coordinates

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map1);

    // Police Stations
    const policeStations = [
        { lat: 23.7806, lng: 90.4225, title: 'Banani Police', description: '24/7 emergency services.' },
        { lat: 23.7454, lng: 90.4083, title: 'Motijheel Police', description: 'Immediate assistance available.' },
        { lat: 23.8295, lng: 90.3532, title: 'Gulshan Police', description: 'Known for quick response times.' }
    ];

    policeStations.forEach(station => {
        L.marker([station.lat, station.lng]).addTo(map1)
            .bindPopup(`
                <div class="popup-content">
                    <div class="popup-title">${station.title}</div>
                    <div class="popup-description">${station.description}</div>
                </div>
            `);
    });

    // Initialize the map for dangerous zones
    const map2 = L.map('map2').setView([23.8103, 90.4125], 12); // Dhaka coordinates

    // Add OpenStreetMap tiles for the second map
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map2);

    // Dangerous Zones with color-coded circles
    const dangerousZones = [
        { lat: 23.8103, lng: 90.4125, severity: 'Dangerous', color: 'orange', description: 'Dhaka Central - High risk of petty crimes.' },
        { lat: 23.7621, lng: 90.3792, severity: 'More Dangerous', color: 'red', description: 'Dhanmondi - Frequent armed robberies and assaults.' },
        { lat: 23.8322, lng: 90.3371, severity: 'Very Dangerous', color: 'darkred', description: 'Mirpur - Ongoing gang violence and street crime.' }
    ];

    dangerousZones.forEach(zone => {
        L.circle([zone.lat, zone.lng], {
            color: zone.color,
            fillColor: zone.color,
            fillOpacity: 0.5,
            radius: 1000
        }).addTo(map2)
            .bindPopup(`
                <div class="popup-content">
                    <div class="popup-title">${zone.severity} Zone</div>
                    <div class="popup-description">${zone.description}</div>
                </div>
            `);
    });
</script>















<!-- Main Content Section -->
<div class="main-content">
    <!-- Blog Section -->
    <div class="blog-section">
        <h1 class="section-title">Latest News and Updates</h1>
        
        <div class="blog-posts">
            <?php foreach ($posts as $index => $post): ?>
                <div class="blog-post" id="post-<?php echo $index + 1; ?>" style="display: <?php echo $index === 0 ? 'block' : 'none'; ?>;">
                    <h2 class="blog-post-title"><?php echo htmlspecialchars($post['title']); ?></h2>
                    <div class="blog-post-meta"><?php echo date('F j, Y', strtotime($post['created_at'])); ?> | By <?php echo htmlspecialchars($post['author']); ?></div>
                    <img src="uploads/<?php echo htmlspecialchars($post['feature_image']); ?>" alt="Feature Image" class="blog-post-image">
                    <div class="blog-post-content">
                        <p><?php echo htmlspecialchars(substr($post['content'], 0, 150)); ?>...</p>
                    </div>
                    
                    <!-- Comments Section -->
                    <div class="comments-section">
                        <div class="comment">
                            <div class="comment-author">Zahid Hasan</div>
                            <div class="comment-text">Great tips! I'll make sure to follow them this winter.</div>
                        </div>
                        <div class="comment-form">
                            <input type="text" placeholder="Add a comment..." class="comment-input">
                            <button type="button" class="comment-btn">Post</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Navigation Buttons -->
            <div class="navigation-btns-container">
                <button id="prevPostBtn" class="navigation-btn">Previous Post</button>
                <button id="nextPostBtn" class="navigation-btn">Next Post</button>
            </div>
        </div>
    </div>
</div>

<!-- Style for Main Content -->
<style>
    /* General Styles */
    .main-content {
        font-family: 'Arial', sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f9f9f9;
    }

    /* Blog Section Styling */
    .blog-section {
        padding: 30px;
        background-color: #fff;
        margin: 20px auto;
        max-width: 1000px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .section-title {
        text-align: center;
        font-size: 2em;
        color: #333;
        margin-bottom: 30px;
    }

    .blog-posts {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .blog-post {
        background-color: #fff;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .blog-post-title {
        font-size: 1.8em;
        color: #333;
        margin-top: 10px;
        font-weight: 600;
    }

    .blog-post-meta {
        font-size: 0.9em;
        color: #777;
        margin-bottom: 10px;
    }

    .blog-post-image {
        width: 100%;
        max-height: 350px;
        object-fit: cover;
        border-radius: 8px;
        margin-top: 15px;
    }

    .blog-post-content {
        font-size: 1em;
        color: #444;
        line-height: 1.6;
        margin-top: 15px;
    }

    .navigation-btns-container {
        display: flex;
        justify-content: center;
        margin-top: 20px;
        gap: 20px;
    }

    .navigation-btn {
        padding: 10px 20px;
        font-size: 1.1em;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .navigation-btn:hover {
        background-color: #0056b3;
    }

    .comments-section {
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px solid #f2f2f2;
    }

    .comment {
        margin: 10px 0;
        background-color: #f7f7f7;
        padding: 12px;
        border-radius: 6px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .comment-author {
        font-weight: bold;
        color: #333;
    }

    .comment-text {
        font-size: 0.9em;
        color: #555;
    }

    .comment-form {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    .comment-input {
        width: 75%;
        padding: 10px;
        font-size: 1em;
        border-radius: 6px;
        border: 1px solid #ccc;
    }

    .comment-btn {
        padding: 10px 15px;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 1em;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .blog-section {
            padding: 20px;
        }

        .section-title {
            font-size: 1.8em;
        }

        .blog-post {
            padding: 12px;
        }

        .blog-post-title {
            font-size: 1.6em;
        }

        .blog-post-content {
            font-size: 0.95em;
        }
    }
</style>

<!-- Script for toggling posts -->
<script>
    let currentPost = 1;
    const totalPosts = <?php echo count($posts); ?>; // Total number of posts

    document.getElementById('nextPostBtn').addEventListener('click', function() {
        // Hide the current post
        document.getElementById(`post-${currentPost}`).style.display = 'none';

        // Move to the next post
        currentPost++;

        if (currentPost > totalPosts) {
            currentPost = 1; // Reset to the first post
        }

        // Show the next post
        document.getElementById(`post-${currentPost}`).style.display = 'block';
    });

    document.getElementById('prevPostBtn').addEventListener('click', function() {
        // Hide the current post
        document.getElementById(`post-${currentPost}`).style.display = 'none';

        // Move to the previous post
        currentPost--;

        if (currentPost < 1) {
            currentPost = totalPosts; // Go to the last post
        }

        // Show the previous post
        document.getElementById(`post-${currentPost}`).style.display = 'block';
    });
</script>

<?php include_once('includes/footer.php'); ?>