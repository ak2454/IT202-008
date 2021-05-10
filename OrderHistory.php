<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You must be logged in to access this page");
    die(header("Location: login.php"));
}

?>
<h1> PAST ORDERS </h1>
<?php
$db = getDB();
$id = get_user_id();
$results = [];

$per_page = 10;
$q = "SELECT COUNT(*) as total from Orders where user_id = :id";

if(has_role("Admin")){
	$stmt = $db->prepare("SELECT Orders.user_id ,Orders.id, Users.username as username, Orders.created from Orders join Users on Users.id =Orders.user_id LIMIT 10");
	
	$stmt->bindValue(":count", $per_page, PDO::PARAM_INT);

	$r = $stmt->execute();
	if ($r) {
    		$orders = $stmt->fetchall(PDO::FETCH_ASSOC);
	}
	else {
    		flash("There was a problem fetching the results ");
	}
}
else{

	$stmt = $db->prepare("SELECT user_id ,id, created from Orders where user_id = :id ORDER BY created DESC  LIMIT :count"); //DESC

	$stmt->bindValue(":count", $per_page, PDO::PARAM_INT);
	$stmt->bindValue(":id", $id);
	$r = $stmt->execute();
	if ($r) {
	    $orders = $stmt->fetchall(PDO::FETCH_ASSOC);
	}
	else {
	    flash("There was a problem fetching the results ");
	}
}

?>

<?php foreach($orders as $o):?>
<div class="card">
  <div class="card-header">

  </div>
  <div class="card-body">
    <h5 class="card-title">Order ID: <?php echo $o["id"]; ?> </h5>
    <p class="card-text">order placed on <?php echo $o["created"]; ?> </p>
    <p class="card-text">order placed by: <?php echo $o["username"]; ?></a></p>
    <a href="orderDetails.php?id=<?php safer_echo($o['id']); ?>" class="btn btn-primary">view details</a>
  </div>
</div>


<?php endforeach;?>

<nav aria-label="bla">
    <ul class="pagination justify-content-center">
        <li class="page-item <?php echo ($page-1) < 1?"disabled":"";?>">
            <a class="page-link" href="?page=<?php echo $page-1;?>" tabindex="-1">Previous</a>
        </li>
        <?php for($i = 0; $i < 0; $i++):?>
            <li class="page-item <?php echo ($page-1) == $i?"active":"";?>"><a class="page-link" href="?page=<?php echo ($i+1);?>"><?php echo ($i+1);?></a></li>
        <?php endfor; ?>
        <li class="page-item <?php echo ($page) >= $total_pages?"disabled":"";?>">
            <a class="page-link" href="?page=<?php echo $page+1;?>">Next</a>
        </li>
    </ul>
</nav>
<?php require_once(__DIR__ . "/partials/flash.php"); ?>
