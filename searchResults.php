<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<?php


$results = [];
$query = "";
if(isset($_GET["search"])){
  $query = $_GET["search"];
}

if (isset($_GET["search"]) && !empty($query)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM Products WHERE name like :q AND visibility = 1 LIMIT 10");

    $r = $stmt->execute([":q" => "%$query%"]);
    if ($r) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_export($results);
    }
    else {
        flash("There was a problem fetching the results " . var_export($stmt->errorInfo(), true));
    }
}
//var_export($results);


?>
<h1>SEARCH RESULTS</h1>
<div class="row" style= "margin-left: 4em;">
<?php if (count($results) > 0): ?>
    <?php foreach ($results as $r): ?>
      <div   class="card" style="width: 20rem; margin: 1em;">
        <img src="" class="card-img-top" alt="...">
        <div class="card-body">
          <a href = "ViewProduct.php?id=<?php safer_echo($r['id']); ?>" <h5 class="card-title"><?php safer_echo($r["name"]); ?></h5></a>
          <h6 class="card-title"><?php safer_echo($r["price"]); ?></h6>
          <p class="card-text"><?php safer_echo($r["description"]); ?></p>
          <?php if (has_role("Admin")): ?>
            <a href="test_view_product.php?id=<?php safer_echo($r['id']); ?>" class="btn btn-primary">Edit</a>
          <?php endif; ?>
          </div>
        </div>
<?php endforeach; ?>
<?php endif; ?>
</div>
