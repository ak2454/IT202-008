<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php

if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("Login to view your cart");
    die(header("Location: login.php"));
}
?>
<?php
$db = getDB();
$id = get_user_id();

if (isset($_POST["quantity"])){
  if ($_POST["quantity"] == 0){
    $stmt = $db->prepare("DELETE FROM Cart where id = :id AND user_id = :uid");
    $r = $stmt->execute([":id"=>$_POST["cartId"], ":uid"=>get_user_id()]);
    if($r){
        flash("Deleted item from cart since quantity = 0", "success");
    }

  }

}




if(isset($_POST["clear"])){
    $stmt = $db->prepare("DELETE FROM Cart where user_id = :uid");
    $r = $stmt->execute([":uid"=>get_user_id()]);
    if($r){
        flash("Deleted all items from cart", "success");
    }
}


if(isset($_POST["delete"])){
    $stmt = $db->prepare("DELETE FROM Cart where id = :id AND user_id = :uid");
    $r = $stmt->execute([":id"=>$_POST["cartId"], ":uid"=>get_user_id()]);
    if($r){
        flash("Deleted item from cart", "success");
    }
}


if(isset($_POST["update"])){
    $stmt = $db->prepare("UPDATE Cart set quantity = :q where id = :id AND user_id = :uid");
    $r = $stmt->execute([":id"=>$_POST["cartId"], ":q"=> $_POST["quantity"], ":uid"=> $id]);
    if($r){
        flash("Updated quantity", "success");
    }
}

if (isset($id)) {
    $stmt = $db->prepare("SELECT Cart.*,Products.name, Products.description, Users.username ,
    (Products.price * Cart.quantity) as sub from Cart JOIN Users on Users.id = Cart.user_id JOIN Products on Products.id = Cart.product_id
     WHERE Users.id = :q LIMIT 10");

    $r = $stmt->execute([":q" => $id]);
    if ($r) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        flash("There was a problem fetching the results " . var_export($stmt->errorInfo(), true));
    }
} else{flash("You do not have a valid ID");}
$total = 0;
foreach($results as $a){
  if ($a["sub"]){
    $total += $a["sub"];

  }
}

?>
<h1>MY CART<h1>
<form method = "POST">
  <button style= "margin: 1em; float: right;" type="submit" class="btn btn-danger" name="clear">clear cart</button>
</form>
<a href = "checkout.php"> <button style= "margin: 1em; float: right;" type="submit" class="btn btn-success" name="clear">checkout</button> </a>

<table class="table table-striped">
  <thead>
    <tr>
      <th scope="col"></th>
      <th scope="col">Product Name</th>
      <th scope= "col">Price</th>
      <th scope="col">quantity</th>
      <th scope="col">description</th>
      <th scope="col">Subtotal</th>
      <th scope="col"></th>
    </tr>
  </thead>
  <tbody>

    <?php if (count($results) > 0): ?>
      <?php foreach ($results as $r): ?>
    <tr>
      <th scope="row"> <img src="..." class="card-img" alt="..."><p class="card-text"><small class="text-muted"> <?php safer_echo($r["modified"])?></small></p></th>
      <td><a href = "ViewProduct.php?id=<?php safer_echo($r['product_id']); ?>"> <?php safer_echo($r["name"])?></a></td>
      <td>$<?php safer_echo($r["price"])?></td>
      <td><form method = "POST"  id = "1" style = "display: flex;">
        <input  style = "width: 50;" type="number" min="0" name="quantity" value="<?php echo $r["quantity"];?>"/>
        <input type="hidden" name="cartId" value="<?php echo $r["id"];?>"/>
        <button type="submit" class="btn btn-success" name="update">update</button>
      </form></td>
      <td><?php safer_echo($r["description"])?></td>
      <td>$<?php safer_echo($r["sub"])?></td>
      <td><button form= "1" type="submit" class="btn btn-danger" name="delete" value="Delete Cart Item">Delete item</button></td>
    </tr>
  <?php endforeach; ?>
  <tr>
    <td>total: $<?php safer_echo($total)?></td>
  </tr>
  <?php else: ?>
      <p>No results, Cart is Empty</p>
  <?php endif; ?>
  </tbody>
</table>
<?php require_once(__DIR__ . "/partials/flash.php"); ?>
