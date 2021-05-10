<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You must be logged in to access this page");
    die(header("Location: login.php"));
}
?>
<h1> YOURE ORDER HAS BEEN PLACED! </h1>

<?php
$db = getDB();
$id = get_user_id();
$stmt = $db->prepare("SELECT MAX(id) as last_order_id from Orders where user_id = :id ");
$r = $stmt->execute([":id" => $id]);
if ($r) {
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
}
else {
    flash("There was a problem fetching the results " . var_export($stmt->errorInfo(), true));
}
echo "your order number: " . $order["last_order_id"];
 ?>
