<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<form method="POST">
        <div class="form-group">
            <label for="input">Email or Username:</label>
            <input class="form-control" type="input" id="input" name="input" required/>
        </div>
        <div class="form-group">
            <label for="p1">Password:</label>
            <input class="form-control" type="password" id="p1" name="password" required/>
        </div>
        <input class="btn btn-primary" type="submit" name="login" value="Login"/>
    </form>

<?php
if (isset($_POST["login"])) {
    $password = null;
    $username = null;
    $input = null;
    $email = null;

    if (isset($_POST["input"])) {
        $input = $_POST["input"];
    }

    if (isset($_POST["password"])) {
        $password = $_POST["password"];
    }
    $isValid = true;
    if (!isset($input) || !isset($password)) {
        $isValid = false;
    }
    if (!strpos($input, "@")) {
	$username = $input;
    }else{
	$email = $input;
    }



    if ($isValid) {
        $db = getDB();
        if (isset($db)) {
	    if(isset($email)){
            	$stmt = $db->prepare("SELECT id, email, password from Users WHERE email = :email LIMIT 1");
	        $params = array(":email" => $email);
	    }
	    if(isset($username)){
	     	$stmt = $db->prepare("SELECT id, username, email, password from Users WHERE username = :username LIMIT 1");
	        $params = array(":username" => $username);
	    }
            $r = $stmt->execute($params);
            $e = $stmt->errorInfo();
            if ($e[0] != "00000") {
                echo "uh oh something went wrong: " . var_export($e, true);
            }
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result && isset($result["password"])) {
                $password_hash_from_db = $result["password"];
                if (password_verify($password, $password_hash_from_db)) {
                    $stmt = $db->prepare("
SELECT Roles.name FROM Roles JOIN UserRoles on Roles.id = UserRoles.role_id where UserRoles.user_id = :user_id and Roles.is_active = 1 and UserRoles.is_active = 1");
                    $stmt->execute([":user_id" => $result["id"]]);
                    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    unset($result["password"]);//remove password so we don't leak it beyond this page
                    //let's create a session for our user based on the other data we pulled from the table
                    $_SESSION["user"] = $result;//we can save the entire result array since we removed password
                    if ($roles) {
                        $_SESSION["user"]["roles"] = $roles;
                    }
                    else {
                        $_SESSION["user"]["roles"] = [];
                    }
                    //on successful login let's serve-side redirect the user to the home page.
                    header("Location: home.php");
                }
                else {
                    echo "<br>Invalid password, try again<br>";
                }
            }
            else {
                echo "<br>This account does not exist. Try again or create a new account<br>";
            }
        }
    }
    else {
        echo "There was a validation issue";
    }
}
?>
