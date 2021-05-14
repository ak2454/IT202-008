<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You must be logged in to access this page");
    die(header("Location: login.php"));
}

?>

<?php
$db = getDB();
$id = get_user_id();
$results = [];

$stmt = $db->prepare("SELECT (ordersItems.quantity *  ordersItems.price) as sub, ordersItems.quantity,ordersItems.product_id as pid, ordersItems.price, Users.username,ordersItems.user_id, ordersItems.order_id as oid,DATE(ordersItems.created) as created, Products.name from ordersItems join Users on Users.id =ordersItems.user_id join Orders on Orders.id = ordersItems.order_id join Products on Products.id = ordersItems.product_id "); //DESC
$r = $stmt->execute([":id" => $id]);
if ($r) {
    $results = $stmt->fetchall(PDO::FETCH_ASSOC);
}
else {
    flash("There was a problem fetching the results ");
}
//var_export($results);

if (isset($_POST["category"])) {
  $cat = $_POST["category"];
  $stmt = $db->prepare("SELECT (ordersItems.quantity *  ordersItems.price) as sub, ordersItems.quantity,ordersItems.product_id as pid, ordersItems.price,
    Users.username,ordersItems.user_id, ordersItems.order_id as oid,DATE(ordersItems.created) as created,
     Products.name from ordersItems join Users on Users.id =ordersItems.user_id join Orders on Orders.id = ordersItems.order_id
      join Products on Products.id = ordersItems.product_id WHERE Products.category =:q");
  $r = $stmt->execute([":q" => $cat]);
  $results = $stmt->fetchall(PDO::FETCH_ASSOC);
}


if (isset($_POST["search"])) {
  $min = date('Y-m-d H:i:s', strtotime($_POST["min"]));
  $max = date('Y-m-d H:i:s', strtotime($_POST["max"]));

  $stmt = $db->prepare("SELECT (ordersItems.quantity *  ordersItems.price) as sub, ordersItems.quantity,ordersItems.product_id as pid, ordersItems.price,
    Users.username,ordersItems.user_id, ordersItems.order_id as oid,DATE(ordersItems.created) as created,
     Products.name from ordersItems join Users on Users.id =ordersItems.user_id join Orders on Orders.id = ordersItems.order_id
      join Products on Products.id = ordersItems.product_id WHERE ordersItems.created BETWEEN '$min' and '$max' ");
  $r = $stmt->execute();
  $results = $stmt->fetchall(PDO::FETCH_ASSOC);
}



$total = 0;
foreach ($results as $r){
  if ($r["sub"]){
    $total += $r["sub"];
  }
}



$stmt = $db->prepare("SELECT DISTINCT category FROM Products");
$r = $stmt->execute();
if ($r) {
    $category = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
?>
<h1> RECENT ORDERS </h1>
<h3> TOTAL:$<?php echo $total;?> </h3>


<div>
<form method="POST" style="float: right; margin-right: 2em;" id = "form1">
  <div class="dropdown">
  <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Categories
  </button>
  <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
    <?php foreach ($category as $c): ?>
    <button type="submit" class="dropdown-item" name = "category" value = "<?php echo $c["category"];?>" ><?php safer_echo($c["category"]);?></button>
  <?php endforeach; ?>
  </div>
</div>
</form>
</div>



<form method="POST" style = "display: inline-block;">
  <label for="min">min date</label>
  <input type="datetime-local" name = "min" id="meeting-time" min="2020-01-01T00:00">
  <label for="max">max date</label>
  <input type="datetime-local" name = "max" id="meeting-time" min="2020-01-01T00:00">
  <button  type="submit" name="search" value="search" class="btn btn-primary">search</button>

</form>

<?php foreach($results as $o):?>
<div class="card">
  <div class="card-header">
  </div>
  <div class="card-body">
    <h5 class="card-title">product: <a href= "ViewProduct.php?id=<?php echo $o["pid"]; ?>"> <?php echo $o["name"]; ?> </a></h5>
    <p class="card-title">Order ID: <a href="orderDetails.php?id=<?php safer_echo($o['oid']); ?>"><?php safer_echo($o['oid']); ?></a> </p>
    <p class="card-title">price: $<?php echo $o["price"]; ?> </p>
    <p class="card-title">quantity: <?php echo $o["quantity"]; ?> </p>
    <p class="card-text">order placed on <?php echo $o["created"]; ?> </p>
    <p class="card-text">order placed by:<a href= "profile.php?id="<?php echo $o["user_id"]; ?>> <?php echo $o["username"]; ?></a></p>

  </div>
</div>
<?php endforeach;?>
<?php require_once(__DIR__ . "/partials/flash.php"); ?>
