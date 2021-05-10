<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
//Note: we have this up here, so our update happens before our get/fetch
//that way we'll fetch the updated data and have it correctly reflect on the form below
//As an exercise swap these two and see how things change
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You must be logged in to access this page");
    die(header("Location: login.php"));
}

$isValid = true;
$db = getDB();
$stmt = $db->prepare("SELECT password from Users WHERE username = :username LIMIT 1");
$params = array(":username" =>  $_SESSION["user"]['username']);
$r = $stmt->execute($params);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result && isset($result["password"])) {
    $password_hash_from_db = $result["password"];
    if (isset($_POST["current"]) &&  !password_verify($_POST["current"], $password_hash_from_db)) {
	    flash ("Current passoword is not correct");
	    $isValid = false;
    }
}


if (isset($_POST["public"])){
  $stmt = $db->prepare("UPDATE Users set privacy = 1 where id = :id");
  $r = $stmt->execute([":id" => get_user_id()]);
  flash("profile updated to public");
}
if (isset($_POST["private"])){
  $stmt = $db->prepare("UPDATE Users set privacy = 0 where id = :id");
  $r = $stmt->execute([":id" => get_user_id()]);
  flash("profile updated to private");
}

if (isset($_GET["id"])){
  $stmt = $db->prepare("SELECT email, username, created, privacy from Users where id = :id");
  $r = $stmt->execute([":id" => $_GET["id"]]);
  $result = $stmt->fetchall(PDO::FETCH_ASSOC);
  if ($result[0]["privacy"] == 0){
    flash("Sorry, this account is private");
  }
}


//save data if we submitted the form
if (isset($_POST["saved"]) && $isValid) {
    $isValid = true;
    //check if our email changed
    $newEmail = get_email();
    if (get_email() != $_POST["email"]) {
        //TODO we'll need to check if the email is available
        $email = $_POST["email"];
        $stmt = $db->prepare("SELECT COUNT(1) as InUse from Users where email = :email");
        $stmt->execute([":email" => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $inUse = 1;//default it to a failure scenario
        if ($result && isset($result["InUse"])) {
            try {
                $inUse = intval($result["InUse"]);
            }
            catch (Exception $e) {

            }
        }
        if ($inUse > 0) {
            flash("Email already in use");
            //for now we can just stop the rest of the update
            $isValid = false;
        }
        else {
            $newEmail = $email;
        }
    }
    $newUsername = get_username();
    if (get_username() != $_POST["username"]) {
        $username = $_POST["username"];
        $stmt = $db->prepare("SELECT COUNT(1) as InUse from Users where username = :username");
        $stmt->execute([":username" => $username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $inUse = 1;//default it to a failure scenario
        if ($result && isset($result["InUse"])) {
            try {
                $inUse = intval($result["InUse"]);
            }
            catch (Exception $e) {

            }
        }
	if(strpos($username, "@")){
		flash("cannot have '@' in username");
		$isValid = false;
	}

        if ($inUse > 0) {
            flash("Username already in use");
            //for now we can just stop the rest of the update
            $isValid = false;
        }
        else {
            $newUsername = $username;
        }
    }
    if ($isValid) {
        $stmt = $db->prepare("UPDATE Users set email = :email, username= :username where id = :id");
        $r = $stmt->execute([":email" => $newEmail, ":username" => $newUsername, ":id" => get_user_id()]);
        if ($r) {
            flash("Updated profile");
        }
        else {
            flash("Error updating profile");
        }
        //password is optional, so check if it's even set
        //if so, then check if it's a valid reset request
        if (!empty($_POST["password"]) && !empty($_POST["confirm"])) {
            if ($_POST["password"] == $_POST["confirm"]) {
                $password = $_POST["password"];
                $hash = password_hash($password, PASSWORD_BCRYPT);
                //this one we'll do separate
                $stmt = $db->prepare("UPDATE Users set password = :password where id = :id");
                $r = $stmt->execute([":id" => get_user_id(), ":password" => $hash]);
                if ($r) {
                    flash("Reset Password");
                }
                else {
                    flash("Error resetting password");
                }
            }
        }
//fetch/select fresh data in case anything changed
        $stmt = $db->prepare("SELECT email, username from Users WHERE id = :id LIMIT 1");
        $stmt->execute([":id" => get_user_id()]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $email = $result["email"];
            $username = $result["username"];
            //let's update our session too
            $_SESSION["user"]["email"] = $email;
            $_SESSION["user"]["username"] = $username;
        }
    }
    else {
        //else for $isValid, though don't need to put anything here since the specific failure will output the message
    }
}

?>
<?php if(isset($_GET["id"]) && $result[0]["privacy"] == 1 ):?>
  <h1>  <?php echo $result[0]["username"]?>'s PROFILE </h1>
  <form>
<fieldset disabled style = "margin-left: 5px; width:30%;">
  <div class="mb-3">
    <label for="disabledTextInput" class="form-label">username</label>
    <input type="text" id="disabledTextInput" class="form-control" placeholder="<?php echo $result[0]["username"]?>">
  </div>
  <div class="mb-3">
    <label for="disabledTextInput" class="form-label">joined</label>
    <input type="text" id="disabledTextInput" class="form-control" placeholder="<?php echo $result[0]["created"]?>">
  </div>
</fieldset>
</form>
<?php endif?>

<?php if(!isset($_GET["id"])):?>
<h1> PROFILE </h1>
<h5> Hello, <?php safer_echo(get_username()); ?> </h5>
    <form method="POST" style = "margin-left: 5px; width:30%;">
        <label for="email">Email</label>
        <input class="form-control form-control-lg"  type="email" name="email" value="<?php safer_echo(get_email()); ?>"/>
        <label for="username">Username</label>
        <input class="form-control" type="text" maxlength="60" name="username" value="<?php safer_echo(get_username()); ?>"/>
        <!-- DO NOT PRELOAD PASSWORD-->
        <label for="pw">Password</label>
        <input class="form-control" type="password" name="password"/>
        <label for="cpw">Confirm Password</label>
        <input class="form-control" type="password" name="confirm"/>

        <label for="currentpw">Current Password</label>
        <input class="form-control" class="form-control"   type="password" name="current"/>

	 <input class="btn btn-primary" type="submit" name="saved" value="Save Profile"/>
    </form>

  <form method="POST" style = "margin-left: 5px; width:30%;">
    <div class="btn-group" role="group" aria-label="Basic mixed styles example" style = "position: relative; top: -450; left: 400;">
      <button type="submit" class="btn btn-danger" name = "private">private</button>
      <button type="submit" class="btn btn-success"name = "public" >public</button>
    </div>
  </form>
	<?php echo"Remember: <br> Cannot have '@' in your username <br> password cannot exceed 60 characters<br>";?>
  <?php endif?>
<?php require(__DIR__ . "/partials/flash.php");
