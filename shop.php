<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php

$results = [];
$cat = 0;
$db = getDB();
$per_page = 10;

$sort = extractData("sort");
$rating = extractData("rating");
$category = extractData ("category");
$query = "SELECT id,name, price, description, (SELECT AVG(rating) from Ratings where product_id = Products.id GROUP BY product_id) as ratings FROM Products WHERE quantity > 0 and visibility = 1";
$q = "SELECT count(*) as total FROM Products";

  if(isset($category)){
    $query .= " AND category = '$category'";
    $q = "SELECT count(*) as total FROM Products WHERE category = '$category'";
  }


  if (isset($sort)){
    $query .= " ORDER BY $sort";
  }


    $query .= " LIMIT :offset, :count";


    paginate($q, [], $per_page);

    $stmt = $db->prepare($query);
    $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->bindValue(":count", $per_page, PDO::PARAM_INT);
    $r = $stmt->execute();

if ($r) {
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
else {
    flash("There was a problem fetching the products ");
}



// feching category to populate dropdown
$stmt = $db->prepare("SELECT DISTINCT category  FROM Products where visibility = 1 LIMIT 10");
$r = $stmt->execute();
if ($r) {
    $category = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }


?>

<script>
    //php will exec first so just the value will be visible on js side
    function addToCart(product_id){
        //https://www.w3schools.com/xml/ajax_xmlhttprequest_send.asp
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                let json = JSON.parse(this.responseText);
                if (json) {
                    if (json.status == 200) {
                        alert(json.message);
                    } else {
                        alert(json.error);
                    }
                }
            }
        };
        xhttp.open("POST", "<?php echo "add_to_cart.php";?>", true);
        //this is required for post ajax calls to submit it as a form
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        //map any key/value data similar to query params
        xhttp.send("product_id="+product_id);
    }
</script>




  <div>
  <form method="POST" style="float: right; margin-top: 1em; display: inline-flex; margin-right: 2em;" id = "form1">

    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      sort by
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
      <button type="submit" class="dropdown-item" name = "sort" value = "price ASC" >price low-high</button>
      <button type="submit" class="dropdown-item" name = "sort" value = "price DESC" >price high-low</button>

      <button type="submit" class="dropdown-item" name = "sort" value = "Ratings ASC" > rating low-high</button>
      <button type="submit" class="dropdown-item" name = "sort" value = "Ratings DESC" > rating high-low</button>
    </div>



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
<h1 style="margin-left: 2em;">PRODUCTS</h1>
<div class="row" style= "margin-left: 4em;">
<?php if (count($results) > 0): ?>
    <?php foreach ($results as $r): ?>
      <div   class="card" style="width: 20rem; margin: 1em;">
        <div class="card-body">
          <a href = "ViewProduct.php?id=<?php safer_echo($r['id']); ?>" <h5 class="card-title"><?php safer_echo($r["name"]); ?></h5></a>
          <h6 class="card-title"><?php safer_echo($r["price"]); ?></h6>
          <p class="card-text"><?php safer_echo($r["description"]); ?></p>
          <?php if (is_logged_in()): ?>
            <button form = "form1" type="button" onclick="addToCart(<?php echo $r["id"];?>);" class="btn btn-primary btn-lg">Add to Cart</button>
          <?php endif;?>
          <?php if (has_role("Admin")): ?>
            <a href="edit_product.php?id=<?php safer_echo($r['id']); ?>" class="btn btn-primary">Edit</a>
          <?php endif; ?>
          </div>
        </div>
<?php endforeach; ?>
<?php endif; ?>

</div>


<nav aria-label="bla">
    <ul class="pagination justify-content-center">
        <li class="page-item <?php echo ($page-1) < 1?"disabled":"";?>">
            <a class="page-link" href="?page=<?php echo $page-1;?>" tabindex="-1">Previous</a>
        </li>
        <?php for($i = 0; $i < $total_pages; $i++):?>
            <li class="page-item <?php echo ($page-1) == $i?"active":"";?>"><a class="page-link" href="?page=<?php echo ($i+1);?>"><?php echo ($i+1);?></a></li>
        <?php endfor; ?>
        <li class="page-item <?php echo ($page) >= $total_pages?"disabled":"";?>">
            <a class="page-link" href="?page=<?php echo $page+1;?>">Next</a>
        </li>
    </ul>
</nav>

<?php require_once(__DIR__ . "/partials/flash.php"); ?>
