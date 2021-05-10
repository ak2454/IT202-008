<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>

<form method="POST" style = "margin-left: 250;">
	<label>Name</label>
	<input name="name" placeholder="name"/>

  <label>quantity</label>
	<input type="number" min="0" name="quantity"/>

	<label>price</label>
	<input type="number" min="1" name="price"/>

	<label>description</label>
	<input type="TEXT"  name="description"/>

  <label>category</label>
	<input type="TEXT"  name="category"/>

  <label>visibility (1-> visibile   0-> not visible)</label>
	<input type="number" min="0" max= "1" name="visibility"/>


	<input type="submit" name="save" value="Create"/>
</form>

<?php
if(isset($_POST["save"])){
	//TODO add proper validation/checks
	$name = $_POST["name"];
	$quantity = $_POST["quantity"];
	$price = $_POST["price"];
	$description = $_POST["description"];
	$user = get_user_id();
  $category = $_POST["category"];
  $visibility = $_POST["visibility"];

	$db = getDB();
	$stmt = $db->prepare("INSERT INTO Products (name, quantity, price, description, user_id, category, visibility) VALUES(:name, :quantity, :price, :description, :user_id, :category, :visibility)");
	$r = $stmt->execute([
		":name"=>$name,
		":quantity"=>$quantity,
		":price"=>$price,
		":description"=>$description,
		":user_id"=>$user,
    ":category"=>$category,
    ":visibility"=>$visibility,


	]);
	if($r){
		flash("Created successfully with id: " . $db->lastInsertId());
	}
	else{
		$e = $stmt->errorInfo();
		flash("Error creating: " . var_export($e, true));
	}
}
?>
<?php require(__DIR__ . "/partials/flash.php");
