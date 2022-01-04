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
		print_r($_SESSION["favProducts"]);
	break;
	case "save":
		$tempsavedinfo=$db_handle->updateQuery("UPDATE customer SET firstName='" . $_POST["FirstName"] . "', lastName='" . $_POST["LastName"] . "', email='" . $_POST["Email"] . "', phn_no='" . $_POST["PhoneNo"] . "'WHERE customer_id='" . $_SESSION["user-id"]."'");
		$_SESSION["account-info"]["firstName"] = $_POST["FirstName"];
		$_SESSION["account-info"]["lastName"] = $_POST["LastName"];
		$_SESSION["account-info"]["email"] = $_POST["Email"];
		header( "Location: " );
	break;
	case "changepass":
		if (password_verify($_POST["CurrentPassword"], $_SESSION["account-info"]["passHash"])) {
			$newhash=password_hash($_POST["NewPassword"], PASSWORD_DEFAULT);
			$db_handle->updateQuery("UPDATE customer SET passHash='" . $newhash . "' WHERE customer_id='" . $_SESSION["user-id"]."'");
			echo 'Password is valid!';
			$_SESSION["account-info"]["passHash"] = $newhash;
		} else {
			echo 'Invalid password.';
		}
		header( "Location: " );
	break;
}
}
?>
<button onclick="document.location='index.php'">menu</button>
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
	<link href="./style.css" type="text/css" rel="stylesheet" />

</head>
<body>
	<div class="container">
		<h1>My Profile</h1>
		<!-- <hr> -->
		<h2>Rating: <?php echo $_SESSION["user-level"] ?></h2>
		<div class="forms-container">
			<div class="account-info">
				<h2>Account Info</h2>
				<hr>
				<form method="POST" action="?action=save">
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
			</div>

			<div class="changepass">
				<h2>Change Password</h2>
				<hr>
				<form method="POST" action="?action=changepass">
					<h3 class="current-password-label label">Current Password</h3>
					<input type="password" class="current-password" name="CurrentPassword" placeholder="Current Password">
					<h3 class="new-password-label label">New Password</h3>
					<input type="password" class="new-password" name="NewPassword" placeholder="New Password">
					<!-- change button -->
					<button type="submit">Change</button>
				</form>
			</div>
		</div>
			
		<div class="favfoods">
			<h2>Favorite Foods</h2>
			<hr>
			<?php
			$favstring = "(";
			foreach($_SESSION["favProducts"] as $favkey=>$fav){
				$favstring=$favstring."'".$fav."', ";
			}
			$favstring = rtrim($favstring, ", ");
			$favstring = $favstring.")";
			$product_array = $db_handle->selectQuery("SELECT * FROM product WHERE product_id in ".$favstring." ORDER BY product_id ASC");
			if (!empty($product_array)) { 
				foreach($product_array as $key=>$value){
			?>
			<div class="product-item">
				<form method="post" action="?action=add&product_id=<?php echo $product_array[$key]["product_id"]; ?>">
				<div class="product-image"><img src="<?php echo $product_array[$key]["image"]; ?>"></div>
				<?php if (isset($_SESSION["user-id"])) {?><div class="favorite-switch" style="--star-color:<?php if (in_array($product_array[$key]['product_id'], $_SESSION["favProducts"])) echo "red"; else echo "black" ?>;" onclick="document.location='index.php?action=switch-favorite&product_id=<?php echo $product_array[$key]['product_id']; ?>'"></div><?php }?>
				<div class="product-tile-footer">
					<div class="product-title"><?php echo $product_array[$key]["name"]; ?></div>
					<div class="product-price"><?php echo "৳".$product_array[$key]["price"]; ?></div>
				</div>
				</form>
			</div>
			<?php
			}
			}
			?>
		</div>
	</div>
</body>
</html>
<?php } else echo "<p>You don't have access<p>"; ?>

