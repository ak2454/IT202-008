<?php
//since API is 100% server, we won't include navbar or flash
require_once(__DIR__ . "/lib/helpers.php");
if (!is_logged_in()) {
    die(header(':', true, 403));
}
$cartID = 0;

if(isset($_POST["product_id"])){
    $product_id = (int)$_POST["product_id"];
    $db = getDB();
    $stmt = $db->prepare("SELECT name, price from Products where id = :id");
    $stmt->execute([":id"=>$product_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if($result) {
        $name = $result["name"];
        $price = $result["price"];
         $stmt = $db->prepare("INSERT INTO Cart (user_id, product_id, price, quantity) VALUES(:user_id, :product_id, :price, 1) ON DUPLICATE KEY UPDATE quantity = quantity +1, price = :price");
        $r = $stmt->execute([":user_id"=>get_user_id(), ":product_id"=>$product_id, ":price"=>$price]);
        if ($r) {
            $response = ["status" => 200, "message" => "Added $name to cart"];
            echo json_encode($response);
            die();
        }
        else{
            $response = ["status" => 400, "error" => "There was an error adding $name to cart"];
            echo json_encode($response);
            die();
        }
    }
    else{
        $response = ["status" => 404, "error" => "Item $product_id not found"];
        echo json_encode($response);
        die();
    }
}
else{
    $response = ["status" => 400, "error" => "An unexpected error occurred, please try again"];
    echo json_encode($response);
    die();
}
?>
