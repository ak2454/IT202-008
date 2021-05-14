<link rel="stylesheet" href="static/css/styles.css">
<?php
//we'll be including this on most/all pages so it's a good place to include anything else we want on those pages
require_once(__DIR__ . "/../lib/helpers.php");
?>


<!-- CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

<!-- jQuery and JS bundle w/ Popper.js -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>


<nav style= "background-color:#a3d2ca;" class="navbar navbar-expand-lg text-white" {
background-color: #4c885b!important;
}"  >

  <a href="Cart.php" style="color:white; position: relative;left: 80%;"> Cart
      <button style="color: White;"  type="button" class = "btn pull-right">
        <svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-cart4" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5zM3.14 5l.5 2H5V5H3.14zM6 5v2h2V5H6zm3 0v2h2V5H9zm3 0v2h1.36l.5-2H12zm1.11 3H12v2h.61l.5-2zM11 8H9v2h2V8zM8 8H6v2h2V8zM5 8H3.89l.5 2H5V8zm0 5a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0zm9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0z"></path>
        </svg>
      </button>
    </a>

  <a class="navbar-brand" style="color:white;" href="home.php">Home</a>
  <button class="navbar-toggler"  type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>


  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <?php if (!is_logged_in()): ?>
      <li class="nav-item">
        <a class="nav-link" style="color:white;" href="login.php">Login</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" style="color:white;"  href="register.php">Sign up</a>
      </li>


      <?php endif; ?>


  
      <?php if (is_logged_in()): ?>

    <li class="nav-item">
        <a class="nav-link" style="color:white;" href="shop.php">Shop</a>
      </li>
 

         <li class="nav-item">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" style="color:white;" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Account
              </a>
              <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="OrderHistory.php">Order History</a>
                <a class="dropdown-item" href="profile.php">Profile</a>
                <div class="dropdown-divider"></div>

              </div>
            </li>
          </li>

          <li class="nav-item">
            <a class="nav-link"  style="color:white;" href="logout.php">Sign out</a>
          </li>

      <?php endif; ?>
      <?php if (has_role("Admin")): ?>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" style="color:white;" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Admin Tools
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="add_product.php">Create Product</a>
          <a class="dropdown-item" href="adminProducts.php">Product List</a>
          <a class="dropdown-item" href="adminOrders.php">Orders</a>
          <a class="dropdown-item" href="adminOrderItems.php">Ordered Items</a>
          <a class="dropdown-item" href="Cart.php">View Cart</a>
      </li>
      <?php endif; ?>
    </ul>


  </div>
</nav>
