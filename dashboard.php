<?php
session_start();  
// Include the database configuration file
include 'config.php'; // This should contain your database connection setup

// Add to Cart functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $book_id = $_POST['book_id'];
    $title = $_POST['title'];
    $price = $_POST['price'];
    $cover_image = $_POST['cover_image'];

    // Check if the book is already in the cart
    $query = "SELECT * FROM cart WHERE book_id = $book_id";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // If the book is already in the cart, increase the quantity
        $query = "UPDATE cart SET quantity = quantity + 1 WHERE book_id = $book_id";
        $conn->query($query);
    } else {
        // If the book is not in the cart, add it
        $stmt = $conn->prepare("INSERT INTO cart (book_id, title, price, cover_image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('isds', $book_id, $title, $price, $cover_image);
        $stmt->execute();
        $stmt->close();
    }
}

// Remove from Cart functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_from_cart'])) {
    $cart_id = $_POST['cart_id'];

    // Remove the item from the cart
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ?");
    $stmt->bind_param('i', $cart_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch books from the database
$query = "SELECT * FROM books";
$result = $conn->query($query);
$books = $result->fetch_all(MYSQLI_ASSOC);

// Fetch cart items from the database
$cart_query = "SELECT * FROM cart";
$cart_result = $conn->query($cart_query);
$cart_items = $cart_result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyber Book Store</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .w3-sidebar a {font-family: "Roboto", sans-serif}
        body,h1,h2,h3,h4,h5,h6,.w3-wide {font-family: "Montserrat", sans-serif;}
        .success-message { color: green; display: none; }
        
        /* Custom CSS to center the button */
        .w3-display-container {
            position: relative;
        }

        .centered-button {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: black;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }

        .centered-button:hover {
            background-color: #444;
        }

        /* Cart dropdown/modal */
        #cart-modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 50%;
            max-width: 400px;
            background-color: white;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.3);
            z-index: 10;
            padding: 20px;
            border-radius: 8px;
        }

        #cart-modal h4 {
            margin-top: 0;
        }

        #cart-modal ul {
            list-style: none;
            padding: 0;
        }

        #cart-modal ul li {
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #cart-modal .close-btn {
            cursor: pointer;
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: red;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
        }

        .remove-button {
            background-color: red;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
        }

        .remove-button:hover {
            background-color: darkred;
        }

        /* Checkout button */
        .checkout-button {
            background-color: black;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            display: block;
            margin: 20px auto 0 auto;
        }

        .checkout-button:hover {
            background-color: #444;
        }

        /* Checkout form modal */
        #checkout-modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80%;
            max-width: 600px;
            background-color: white;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.3);
            z-index: 10;
            padding: 20px;
            border-radius: 8px;
        }

        #checkout-modal h4 {
            margin-top: 0;
        }

        #checkout-modal .close-btn {
            cursor: pointer;
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: red;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
        }

        #checkout-form input,
        #checkout-form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        #checkout-form button {
            background-color: black;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #checkout-form button:hover {
            background-color: #444;
        }
    </style>
</head>
<body class="w3-content" style="max-width:1200px">

<!-- Sidebar/menu -->
<nav id="overall" class="w3-sidebar w3-bar-block w3-white w3-collapse w3-top" style="z-index:3;width:250px">
    <div class="w3-container w3-display-container w3-padding-16">
        <i onclick="w3_close()" class="fa fa-remove w3-hide-large w3-button w3-display-topright"></i>
        <h3 class="w3-wide"><b>Cyber Book Store</b></h3>
    </div>
    <div class="w3-padding-64 w3-large w3-text-grey" style="font-weight:bold">
    <a href="#books" class="w3-bar-item w3-button">Books</a>
    <a href="javascript:void(0)" class="w3-bar-item w3-button" onclick="showProfileForm()">Profile</a>
    <a href="javascript:void(0)" class="w3-bar-item w3-button" onclick="showCart()">Cart</a>
    <a href="javascript:void(0)" class="w3-bar-item w3-button" onclick="showContactForm()">Contact</a>
    <a href="javascript:void(0)" class="w3-bar-item w3-button" onclick="showLogoutConfirm()">Logout</a>
</div>

</nav>

<!-- Top menu on small screens -->
<header class="w3-bar w3-top w3-hide-large w3-black w3-xlarge">
    <div class="w3-bar-item w3-padding-24 w3-wide">Cyber Book Store</div>
    <a href="javascript:void(0)" class="w3-bar-item w3-button w3-padding-24 w3-right" onclick="w3_open()"><i class="fa fa-bars"></i></a>
</header>

<!-- Overlay effect when opening sidebar on small screens -->
<div class="w3-overlay w3-hide-large" onclick="w3_close()" style="cursor:pointer" title="close side menu" id="myOverlay"></div>

<!-- !PAGE CONTENT! -->
<div class="w3-main" style="margin-left:250px">
    <!-- Push down content on small screens -->
    <div class="w3-hide-large" style="margin-top:83px"></div>
    
    <!-- Top header -->
    <header class="w3-container w3-xlarge">
        <p class="w3-left">Books</p>
        <p class="w3-right">
            <i class="fa fa-search"></i>
        </p>
    </header>

    <!-- Image header -->
    <div class="w3-display-container w3-container">
        <img src="images/background.jpg" alt="Books" style="width:100%">
        <div class="w3-display-topleft w3-text-white" style="padding:24px 48px">
            <h1 class="w3-jumbo w3-hide-small">New arrivals</h1>
            <h1 class="w3-hide-large w3-hide-medium">New arrivals</h1>
            <h1 class="w3-hide-small">COLLECTION 2023</h1>
            <p><a href="#books" class="w3-button w3-black w3-padding-large w3-large">Purchase</a></p>
        </div>
    </div>

    <div class="w3-container w3-text-grey" id="books">
        <p id="book-count"><?php echo count($books); ?> items</p>
    </div>

    <!-- Product grid -->
    <div class="w3-row w3-grayscale" id="book-grid">
        <?php foreach ($books as $book): ?>
            <div class="w3-col l3 s6">
                <div class="w3-container">
                    <div class="w3-display-container">
                        <img src="uploads/<?php echo htmlspecialchars($book['cover_image']); ?>" alt="Book Cover" style="width:100%">
                        <form method="POST" class="centered-button">
                            <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                            <input type="hidden" name="title" value="<?php echo htmlspecialchars($book['title']); ?>">
                            <input type="hidden" name="price" value="<?php echo htmlspecialchars($book['price']); ?>">
                            <input type="hidden" name="cover_image" value="<?php echo htmlspecialchars($book['cover_image']); ?>">
                            <button type="submit" name="add_to_cart" class="centered-button">Add to Cart</button>
                        </form>
                    </div>
                    <p><?php echo htmlspecialchars($book['title']); ?><br><b>$<?php echo htmlspecialchars($book['price']); ?></b></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<!-- Cart modal -->
<div id="cart-modal" data-item-count="<?php echo count($cart_items); ?>">
    <button class="close-btn" onclick="closeCart()">X</button>
    <h4>Your Cart</h4>
    <ul>
        <?php foreach ($cart_items as $item): ?>
            <li>
                <?php echo htmlspecialchars($item['title']); ?> - $<?php echo htmlspecialchars($item['price']); ?> (x<?php echo $item['quantity']; ?>)
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                    <button type="submit" name="remove_from_cart" class="remove-button">Remove</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
    <button class="checkout-button" onclick="showCheckout()">Checkout</button>
</div>

<!-- Checkout form modal -->
<div id="checkout-modal">
    <button class="close-btn" onclick="closeCheckout()">X</button>
    <h4>Checkout</h4>
    <form id="checkout-form" method="POST" action="process_checkout.php">
    <input type="text" name="fullname" placeholder="Full Name" required>
    <input type="text" name="address" placeholder="Address" required>
    <input type="text" name="city" placeholder="City" required>
    <input type="text" name="country" placeholder="Country" required>
    <input type="text" name="zip" placeholder="Zip Code" required>
    <select name="payment_method" required>
        <option value="paypal">PayPal</option>
        <option value="visa">Visa</option>
        <option value="mastercard">MasterCard</option>
        <option value="amex">American Express</option>
        <option value="discover">Discover</option>
    </select>
    <input type="text" name="credit_card_number" placeholder="Credit Card Number" required>
    <input type="text" name="cvv" placeholder="CVV" required>
    <input type="text" name="expiry_date" placeholder="MM/YY" required>
    <button type="submit" name="submit_checkout">Submit Payment</button>
</form>

</div>

<!-- Success message modal -->
<div id="success-modal" class="w3-modal" style="display: none;">
    <div class="w3-modal-content w3-animate-opacity w3-display-middle" style="width: 50%; max-width: 500px;">
        <div class="w3-container w3-white w3-center">
            <h4>The purchase was successful</h4>
            <button onclick="closeSuccessModal()" class="w3-button w3-black">OK</button>
        </div>
    </div>
</div>

<!-- Contact Modal -->
<div id="contact-modal" class="w3-modal">
    <div class="w3-modal-content w3-animate-zoom" style="padding:32px">
        <div class="w3-container w3-white w3-center">
            <i onclick="document.getElementById('contact-modal').style.display='none'" class="fa fa-remove w3-right w3-button w3-transparent w3-xxlarge"></i>
            <h2 class="w3-wide">Contact Us</h2>
            <form action="submit_contact.php" method="POST">
                <input type="text" name="name" placeholder="Your Name" required>
                <input type="email" name="email" placeholder="Your Email" required>
                <input type="text" name="subject" placeholder="Subject">
                <textarea name="message" placeholder="Your Message" required></textarea>
                <button type="submit">Send</button>
            </form>
        </div>
    </div>
</div>
<script>
    function processPayment(event) {
    event.preventDefault();
    
    const submitBtn = document.getElementById('submit-btn');
    submitBtn.textContent = 'Processing...';
    submitBtn.disabled = true;
    
    // Simulate payment processing
    setTimeout(() => {
        // Send a request to clear the cart
        fetch('clear_cart.php', {
            method: 'POST',
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Display success modal
                document.getElementById('checkout-modal').style.display = 'none';
                document.getElementById('success-modal').style.display = 'block';
            } else {
                alert('Failed to clear cart.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while clearing the cart.');
        });
    }, 3000);
}


function closeSuccessModal() {
    document.getElementById('success-modal').style.display = 'none';
    // Optionally, you can redirect the user to another page or refresh the current page
    window.location.href = 'dashboard.php'; // Redirect to homepage or another relevant page
}
</script>

    <!-- Footer -->
    <footer class="w3-padding-64 w3-light-grey w3-small w3-center" id="footer">
        <div class="w3-row-padding">
            <div class="w3-col s12">
                <h4>Store</h4>
                <p><i class="fa fa-fw fa-map-marker"></i> Cyber Book Store</p>
                <p><i class="fa fa-fw fa-phone"></i>+977 9840330069</p>
                <p><i class="fa fa-fw fa-envelope"></i>madaraanditachi69@gmail.com</p>
                <h4>We accept</h4>
                <p><i class="fa fa-fw fa-credit-card"></i> Credit Card</p>
                <br>
                <i class="fa fa-facebook-official w3-hover-opacity w3-large"></i>
                <i class="fa fa-instagram w3-hover-opacity w3-large"></i>
                <i class="fa fa-snapchat w3-hover-opacity w3-large"></i>
                <i class="fa fa-pinterest-p w3-hover-opacity w3-large"></i>
                <i class="fa fa-twitter w3-hover-opacity w3-large"></i>
                <i class="fa fa-linkedin w3-hover-opacity w3-large"></i>
            </div>
        </div>
    </footer>

    <div class="w3-black w3-center w3-padding-24">All rights reserved</div>

    <!-- End page content -->
</div>

<!-- Profile modal -->
<div id="profile-modal" class="w3-modal">
    <div class="w3-modal-content w3-animate-opacity w3-display-middle" style="width: 50%; max-width: 500px;">
        <div class="w3-container w3-white">
            <i onclick="closeProfileForm()" class="fa fa-remove w3-right w3-button w3-transparent w3-xxlarge"></i>
            <h2 class="w3-wide">Your Profile</h2>
            <div id="profile-content">
                <div class="w3-center">
                    <img id="profile-picture" src="" alt="Profile Picture" class="w3-circle w3-margin-bottom" style="width:50%">
                </div>
                <!-- Success message -->
                <div id="profile-success-message" class="w3-text-green w3-large success-message" style="display: none;">
                    Profile has been updated successfully.
                </div>

                <form id="profile-form">
                    <p><b>Username:</b> <input class="w3-input w3-border" type="text" name="username" id="username" required></p>
                    <p><b>New Password:</b> <input class="w3-input w3-border" type="password" name="password" placeholder="Enter new password"></p>
                    <p><button type="submit" class="w3-button w3-black">Update Profile</button></p>
                </form>

                <h4 class="w3-wide">Books Bought</h4>
                <div id="bought-books">
                    <!-- Bought books history will be loaded here via JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Modal -->
<div id="contact-modal" class="w3-modal">
    <div class="w3-modal-content w3-animate-zoom" style="padding:32px">
        <div class="w3-container w3-white w3-center">
            <i onclick="document.getElementById('contact-modal').style.display='none'" class="fa fa-remove w3-right w3-button w3-transparent w3-xxlarge"></i>
            <h2 class="w3-wide">Contact Us</h2>
            <form id="contact-form">
              <p><input class="w3-input w3-border" type="text" placeholder="Enter your name" name="name" required></p>
              <p><input class="w3-input w3-border" type="email" placeholder="Enter your email" name="email" required></p>
              <p><input class="w3-input w3-border" type="text" placeholder="Subject" name="subject" required></p>
              <p><textarea class="w3-input w3-border" placeholder="Your message" name="message" required></textarea></p>
              <button type="submit" class="w3-button w3-padding-large w3-red w3-margin-bottom">Send</button>
            </form>
        </div>
    </div>
</div>

<!-- Logout Confirmation Modal -->
<div id="logout-confirm-modal" class="w3-modal">
    <div class="w3-modal-content w3-animate-opacity w3-display-middle" style="width: 30%; max-width: 400px;">
        <div class="w3-container w3-white w3-center">
            <i onclick="closeLogoutConfirm()" class="fa fa-remove w3-right w3-button w3-transparent w3-xxlarge"></i>
            <h2 class="w3-wide">Confirm Logout</h2>
            <p>Are you sure you want to logout?</p>
            <div class="w3-padding-16">
                <button onclick="logoutUser()" class="w3-button w3-red w3-margin-right">Yes</button>
                <button onclick="closeLogoutConfirm()" class="w3-button w3-green">No</button>
            </div>
        </div>
    </div>
</div>

<script>
// Function to add a book to the cart
function addToCart(bookId) {
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ book_id: bookId }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Book added to cart.');
            location.reload(); // Reload the page to update the cart
        } else {
            alert('Failed to add book to cart.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding the book to the cart.');
    });
}

// Function to remove a book from the cart
function removeFromCart(cartId) {
    fetch('remove_from_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ cart_id: cartId }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Book removed from cart.');
            location.reload(); // Reload the page to update the cart
        } else {
            alert('Failed to remove book from cart.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while removing the book from the cart.');
    });
}

// Show the cart modal
function showCart() {
    const cartModal = document.getElementById('cart-modal');
    cartModal.style.display = 'block';
}

// Close the cart modal
function closeCart() {
    const cartModal = document.getElementById('cart-modal');
    cartModal.style.display = 'none';
}

// Show the checkout form modal
// Show the checkout form modal
function showCheckout() {
    const checkoutModal = document.getElementById('checkout-modal');
    const itemCount = document.getElementById('cart-modal').getAttribute('data-item-count');
    
    if (parseInt(itemCount) > 0) {
        checkoutModal.style.display = 'block';
    } else {
        alert("No any books in cart.");
    }
}


// Close the checkout form modal
function closeCheckout() {
    const checkoutModal = document.getElementById('checkout-modal');
    checkoutModal.style.display = 'none';
}

// Show the profile form
function showProfileForm() {
    const modal = document.getElementById('profile-modal');
    modal.classList.add('w3-animate-opacity');
    modal.style.display = 'block';
    fetchProfileInfo();
}

// Close the profile form
function closeProfileForm() {
    const modal = document.getElementById('profile-modal');
    modal.classList.remove('w3-animate-opacity');
    modal.style.display = 'none';
}

// Show the logout confirmation modal
function showLogoutConfirm() {
    const modal = document.getElementById('logout-confirm-modal');
    modal.classList.add('w3-animate-opacity');
    modal.style.display = 'block';
}

// Close the logout confirmation modal
function closeLogoutConfirm() {
    const modal = document.getElementById('logout-confirm-modal');
    modal.classList.remove('w3-animate-opacity');
    modal.style.display = 'none';
}

// Logout user
function logoutUser() {
    window.location.href = 'index.php';
}

// Show the contact form
function showContactForm() {
    const modal = document.getElementById('contact-modal');
    modal.style.display = 'block';
}

// Handle profile update form submission
document.getElementById('profile-form').addEventListener('submit', function(event) {
    event.preventDefault();

    const formData = new FormData(this);
    fetch('update_profile.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('profile-success-message').style.display = 'block';
        } else {
            alert('Failed to update profile.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating your profile. Please try again.');
    });
});
</script>

</body>
</html>
