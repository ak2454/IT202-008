<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
if (isset($_POST["register"])) {
    $email = null;
    $password = null;
    $confirm = null;
    $username = null;

    $isValid = true;
    if (isset($_POST["email"])) {
        $email = $_POST["email"];
    }
    if (isset($_POST["password"])) {
        $password = $_POST["password"];
        if(strlen($password) > 60){
      		flash("<br>password should be less than 60 characters<br>");
      		$isvalid = false;
        }
    }
    if (isset($_POST["confirm"])) {
        $confirm = $_POST["confirm"];

    }
    if (isset($_POST["username"])) {
        $username = $_POST["username"];
	      if (strpos($username, "@")){
          flash("cannot have '@' in your username");
            $isValid = false;
        }
    }
    //check if passwords match on the server side
    if ($password == $confirm) {
        //not necessary to show
        echo "Passwords match <br>";
    }
    else {
        flash("Passwords don't match");
        $isValid = false;
    }
    if (!isset($email) || !isset($password) || !isset($confirm) ||!isset($username) ) {
        $isValid = false;
    }
    //TODO other validation as desired, remember this is the last line of defense
    if ($isValid) {
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $db = getDB();
        if (isset($db)) {
            //here we'll use placeholders to let PDO map and sanitize our data
            $stmt = $db->prepare("INSERT INTO Users(email, username, password) VALUES(:email,:username, :password)");
            //here's the data map for the parameter to data
            $params = array(":email" => $email, ":username" => $username, ":password" => $hash);
            $r = $stmt->execute($params);
            $e = $stmt->errorInfo();
            if ($e[0] == "00000") {
                flash("Successfully registered! Please login.");
            }
            else {
                if ($e[0] == "23000") {//code for duplicate entry
                    flash("Username or email already exists, please try a different one.");
                }
                else {
                    flash("An error occurred, please try again");
                }
            }
          }
    }
    else {
        flash( "There was a validation issue");
    }
}
//safety measure to prevent php warnings
if (!isset($email)) {
    $email = "";
}
if (!isset($username)) {
    $username = "";
}
?>

<div class="signup-form" style="margin-left: 400;width: 400; color: grey;
border-radius: 3px;
margin-bottom: 15px;
background: rgb(181, 226, 197);
box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
padding: 30px;">
    <form method="post">
		<h2 style = "text-align: center;">~ Register ~</h2>
		<p class="hint-text">Create your account here to view all our products!</p>
        <div class="form-group">
          <input class="form-control"  placeholder="Username" id="user" name="username" required maxlength="60" value="<?php safer_echo($username); ?>"</div>
        </div>
        <div class="form-group">
        	<input class="form-control" placeholder="Email" type="email" id="email" name="email" required value="<?php safer_echo($email); ?>">
        </div>
		<div class="form-group">
            <input class="form-control" placeholder="Password" type="password" id="p1" name="password" required>
        </div>
		<div class="form-group">
            <input  class="form-control" placeholder=" Comfirm Password"  type="password" id="p2" name="confirm" required>
        </div>

		<div class="form-group">
            <button class="btn btn-secondary btn-lg btn-block" type="submit" name="register" value="Register" style="background-color: white; color: grey; border-color: #ffffff;" >Register</button>
        </div>
    </form>
	<div class="text-center">Already have an account? <a href="login.php">Sign in</a></div>
</div>
<?php flash("rememeber: <br> cannot have '@' in your username <br> Password cannot exceed 60 characters ");?>
<?php require(__DIR__ . "/partials/flash.php");
