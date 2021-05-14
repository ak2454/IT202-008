<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
$db = getDB();
$results =[];

if (isset($_GET["id"])) {
    $id = $_GET["id"];
      $stmt = $db->prepare("SELECT ordersItems.product_id,ordersItems.quantity, (Products.price * ordersItems.quantity) as sub, Products.name, Products.description, Products.price from ordersItems JOIN Products on Products.id=ordersItems.product_id WHERE ordersItems.order_id = :id AND Products.visibility = 1");

    $r = $stmt->execute([":id" => $id]);
    if ($r) {
      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
    else {
        flash("There was a problem fetching the results " . var_export($stmt->errorInfo(), true));
    }
    $total = 0;
    foreach($results as $a){
      if ($a["sub"]){
        $total += $a["sub"];

      }
    }

}

?>
<h1>CART WITH ID: <?php echo $id;?><h1>
<table class="table table-striped">
  <thead>
    <tr>
      <th scope="col"></th>
      <th scope="col">Product Name</th>
      <th scope= "col">Price</th>
      <th scope="col">quantity</th>
      <th scope="col">description</th>
      <th scope="col">Subtotal</th>

    </tr>
  </thead>
  <tbody>

    <?php if (count($results) > 0): ?>
      <?php foreach ($results as $r): ?>
    <tr>
      <th scope="row"> <img src="..." class="card-img" alt="..."></th>
      <td><a href = "ViewProduct.php?id=<?php safer_echo($r['product_id']); ?>"> <?php safer_echo($r["name"])?></a></td>
      <td>$<?php safer_echo($r["price"])?></td>
      <td><form method = "POST"  id = "1" style = "display: flex;">
        <input  style = "width: 50;" type="number" min="0" name="quantity" value="<?php echo $r["quantity"];?>"/>
        <input type="hidden" name="cartId" value="<?php echo $r["id"];?>"/>

      </form></td>
      <td><?php safer_echo($r["description"])?></td>
      <td>$<?php safer_echo($r["sub"])?></td>
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
