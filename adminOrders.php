<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You must be logged in to access this page");
    die(header("Location: login.php"));
}

?>
<h1> 10 MOST RECENT ORDERS </h1>
<?php
$db = getDB();
$id = get_user_id();
$results = [];


$stmt = $db->prepare("SELECT Orders.user_id ,Orders.id, Users.username as username, Orders.created from Orders join Users on Users.id =Orders.user_id LIMIT 10"); //DESC
$r = $stmt->execute([":id" => $id]);
if ($r) {
    $orders = $stmt->fetchall(PDO::FETCH_ASSOC);
}
else {
    flash("There was a problem fetching the results ");
}

?>

<?php foreach($orders as $o):?>
<div class="card">
  <div class="card-header">

  </div>
  <div class="card-body">
    <h5 class="card-title">Order ID: <?php echo $o["id"]; ?> </h5>
    <p class="card-text">order placed on <?php echo $o["created"]; ?> </p>
    <p class="card-text">order placed by:<a href= "profile.php?id="<?php echo $o["user_id"]; ?>> <?php echo $o["username"]; ?></a></p>
    <a href="orderDetails.php?id=<?php safer_echo($o['id']); ?>" class="btn btn-primary">view details</a>
  </div>
</div>
<?php endforeach;?>
