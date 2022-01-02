<?php
session_start();
require_once("dbcontroller.php");
$db_handle = new DBController();
if(!empty($_GET["action"])) {
switch($_GET["action"]) {
	case "logout":
		unset($_SESSION["user-id"]);
		unset($_SESSION["user-level"]);
		unset($_SESSION["cart_item"]);
		unset($_SESSION["cupon"]);
		unset($_SESSION["order_price"]);
		unset($_SESSION["favProducts"]);
		session_destroy();
		header( "Location: " );
	break;
	case "login":
		$_SESSION["user-id"] = '1';
		$_SESSION["user-level"] = '1';
		$_SESSION["favProducts"] = array();
		$tempfav=$db_handle->selectQuery("SELECT * FROM accinfo_favorites WHERE customer_id='" . $_SESSION["user-id"] . "' and customer_level='".$_SESSION["user-level"]."'");
		foreach ($tempfav as $key=>$value) {
			$_SESSION["favProducts"][$value["favorites"]] = $value["favorites"];
		}
		$_SESSION["account-info"] = array();
		$tempaccinfo=$db_handle->selectQuery("SELECT * FROM customer, accinfo WHERE customer.customer_id = accinfo.customer_id and accinfo.customer_id ='" . $_SESSION["user-id"] . "' and customer_level='".$_SESSION["user-level"]."'");
		$_SESSION["account-info"] = $tempaccinfo[0];
		print_r($_SESSION["account-info"]);
	break;
}
}
?>
<button onclick="document.location='?action=login'">login</button>
<button onclick="document.location='?action=logout'">logout</button>
<?php
	if(isset($_SESSION["user-id"])) {
	
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Profile</title>
</head>
<body>
	<div class="container">
		<h1>My Profile</h1>
		<hr>
		<div class="account-info">
			<h2>Account Info</h2>
			<h2>Rating: 4.3</h2>
			<hr>
			<form method="POST">
				<h3 class="first-name-label label">First Name</h3>
				<input type="text" class="first-name" name="FirstName" placeholder="First Name" value="<?php echo $_SESSION['account-info']['firstName']; ?>">
				<h3 class="last-name-label label">Last Name</h3>
				<input type="text" class="last-name" name="LastName" placeholder="Last Name" value="<?php echo $_SESSION['account-info']['lastName']; ?>">
				<h3 class="email-label label">Email</h3>
				<input type="text" class="email" name="Email" placeholder="Email" value="<?php echo $_SESSION['account-info']['email']; ?>">
				<h3 class="phone-no-label label">Phone No</h3>
				<input type="text" class="phone-no" name="PhoneNo" placeholder="Phone No" value="<?php echo $_SESSION['account-info']['phn_no']; ?>">
				<!-- save button -->
				<button type="submit">Save</button>
			</form>
<?php echo $_POST["FirstName"]; ?> 
			<h2>Change Password</h2>
			<hr>
			<form method="POST">
				<h3 class="previous-password-label label">Previous Password</h3>
				<input type="password" class="previous-password" name="PreviousPassword" placeholder="Previous Password">
				<h3 class="new-password-label label">New Password</h3>
				<input type="password" class="new-password" name="NewPassword" placeholder="New Password">
				<!-- change button -->
				<button type="submit">Change</button>
			</form>

			<h2>Favorite Foods</h2>

		</div>
	</div>
</body>
</html>
<?php } else echo "<p>You don't have access<p>"; ?>

<?php echo password_hash("asd", PASSWORD_DEFAULT); ?>