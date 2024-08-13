<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyber Book Store</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f0f0;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            line-height: 1.6;
        }
        .navbar {
            background-color: #20232a;
            color: #61dafb;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1050;
        }
        .navbar a, .navbar a:visited {
            color: #61dafb;
            text-decoration: none;
            padding: 10px;
            transition: color 0.3s ease;
        }
        .navbar a:hover, .navbar .menu a:hover {
            color: #ffffff;
            background-color: #333;
        }
        .navbar .brand {
            display: flex;
            align-items: center;
            font-weight: bold;
            font-size: 1.5rem;
        }
        .navbar .menu {
            display: flex;
            align-items: center;
        }
        .menu{
            margin-right:30px;
        }
        .navbar .menu a {
            border-radius: 5px;
        }
        .menu > a{
            height:25px;
            padding:10px 20px;
        }
        .hamburger {
            display: none;
            font-size: 2rem;
            cursor: pointer;
        }
        .header {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding-top: 80px;
            background: linear-gradient(to bottom right, #4e54c8, #8f94fb);
            color: white;
            min-height: 60vh;
        }
        .header h1 {
            margin-bottom: 10px;
            font-size: 2.5rem;
        }
        .header p {
            margin-bottom: 20px;
            font-size: 1.2rem;
        }
        h2 {
            text-align: center;
            color: #333;
            padding: 20px 0;
        }
        .books-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 20px;
            background-color: #fff;
            margin: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .book {
            background-color: white;
            margin: 10px;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 180px;
            display: flex;
            flex-direction: column;
            cursor: pointer;
            text-align: center;
        }
        .book:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 16px rgba(0, 0, 0, 0.2);
        }
        .book img {
            width: 100%;
            height: auto;
            border-bottom: 1px solid #ddd;
        }
        .about-section {
            padding: 2rem;
            text-align: center;
            background-color: #f8f9fa;
            margin: 20px;
            border-radius: 10px;
        }
        footer {
            background-color: #20232a;
            color: #61dafb;
            text-align: center;
            padding: 1rem;
            margin-top: auto;
        }
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
            }
            .navbar .menu {
                flex-direction: column;
                display: none;
            }
            .hamburger {
                display: block;
            }
            .menu a {
                display: block;
                width: 100%;
                text-align: center;
            }
        }
        #logo_s {
            width: 70px;
            height:70px;
        }
        #logo_s:hover{
            background:#2B2B2B;
        }
        #organization{
            background: url('images/background.jpg') no-repeat center/cover;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="index.php" class="brand"><img id="logo_s" src="images/logo.jpg.png" alt="Logo of Cyberbooks"></a>
        <div class="hamburger">&#9776;</div>
        <div class="menu">
            <a href="index.php">Home</a>
            <a href="#books">Books</a>
            <a href="#about">About</a>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        </div>
    </div>
    <div id="organization" class="header">
        <h1>Welcome to the Cyber Book Store</h1>
        <p>Explore our vast collection of cybersecurity books.</p>
    </div>
    <h2>Featured Books</h2>
    <div class="books-container" id="books">
        <!-- Books are dynamically listed here -->
        <div class="book">
            <img src="images/image1.jpg" alt="Web Application Hacker's Handbook" onclick="window.location='login.php';">
        </div>
        <div class="book">
            <img src="images/image2.jpg" alt="Hacking: The Art of Exploitation" onclick="window.location='login.php';">
        </div>
        <div class="book">
            <img src="images/image3.jpg" alt="Metasploit: The Penetration Tester's Guide" onclick="window.location='login.php';">
        </div>
        <div class="book">
            <img src="images/image4.jpg" alt="Practical Malware Analysis" onclick="window.location='login.php';">
        </div>
        <div class="book">
            <img src="images/image5.jpg" alt="The Basics of Hacking and Penetration Testing" onclick="window.location='login.php';">
        </div>
        <div class="book">
            <img src="images/image6.jpg" alt="Applied Cryptography" onclick="window.location='login.php';">
        </div>
        <div class="book">
            <img src="images/image7.jpg" alt="Security Engineering" onclick="window.location='login.php';">
        </div>
        <div the="book">
            <img src="images/image8.jpg" alt="Cryptography and Network Security" onclick="window.location='login.php';">
        </div>
        <div class="book">
            <img src="images/image9.jpg" alt="Penetration Testing" onclick="window.location='login.php';">
        </div>
        <div class="book">
            <img src="images/image10.jpg" alt="Cybersecurity and Cyberwar" onclick="window.location='login.php';">
        </div>
    </div>
    <div class="about-section" id="about">
        <h2>About Cyber Book Store</h2>
        <p>
            Cyber Book Store is dedicated to providing enthusiasts, professionals, and students with the most comprehensive selection of cybersecurity literature. From textbooks on software security to guides on ethical hacking, our store offers resources essential for education and professional development in the field of cybersecurity. Enjoy secure online shopping with our user-friendly website and expert support team. Join our community to access exclusive content, discounts, and updates on the latest in cybersecurity.
        </p>
    </div>
    <footer>
        <p>&copy; 2024 Cyber Book Store. All rights reserved.</p>
        <a href="admin_login.php" style="color: #61dafb;">Admin Portal</a>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var hamburger = document.querySelector('.hamburger');
            var menu = document.querySelector('.menu');
            hamburger.addEventListener('click', function() {
                menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
            });
        });
    </script>
</body>
</html>
