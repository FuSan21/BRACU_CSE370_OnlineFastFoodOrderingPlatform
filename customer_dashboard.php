<button onclick="document.location='?action=login'">login</button>
<button onclick="document.location='?action=logout'">logout</button>
<?php
session_start();
require_once("dbcontroller.php");
$db_handle = new DBController();
if (!empty($_GET["action"])) {
    switch ($_GET["action"]) {
        case "logout":
            unset($_SESSION["user-id"]);
            unset($_SESSION["user-level"]);
            unset($_SESSION["cart_item"]);
            unset($_SESSION["cupon"]);
            unset($_SESSION["order_price"]);
            unset($_SESSION["favProducts"]);
            unset($_SESSION["account-info"]);
            session_destroy();
            $db_handle->dropqueryparam();
            break;
        case "login":
            $_SESSION["user-id"] = '1';
            $_SESSION["user-level"] = '1';
            $_SESSION["favProducts"] = array();
            $tempfav = $db_handle->selectQuery("SELECT * FROM accinfo_favorites WHERE customer_id='" . $_SESSION["user-id"] . "' and customer_level='" . $_SESSION["user-level"] . "'");
            foreach ($tempfav as $key => $value) {
                $_SESSION["favProducts"][$value["favorites"]] = $value["favorites"];
            }
            $_SESSION["account-info"] = array();
            $tempaccinfo = $db_handle->selectQuery("SELECT * FROM customer, accinfo WHERE customer.customer_id = accinfo.customer_id and accinfo.customer_id ='" . $_SESSION["user-id"] . "' and customer_level='" . $_SESSION["user-level"] . "'");
            $_SESSION["account-info"] = $tempaccinfo[0];
            $db_handle->dropqueryparam();
            break;
        case "update_order_status":
            $db_handle->executequery("UPDATE `orders` SET status = 'canceled' WHERE `order_id` = '" . $_GET['order_id'] . "'");
            $db_handle->dropqueryparam();
            break;
        case "update_rating":
            $db_handle->executequery("UPDATE `orders` SET `given_rating` = '" . $_POST['rating'] . "' WHERE `order_id` = '" . $_GET['order_id'] . "'");
            $db_handle->dropqueryparam();
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php
if (isset($_SESSION["user-id"])) {
?>

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Customer Dashboard</title>
        <style>
            .topnav {
                overflow: hidden;
                background-color: #333;
            }

            .topnav a {
                float: left;
                color: #f2f2f2;
                text-align: center;
                padding: 14px 16px;
                text-decoration: none;
                font-size: 17px;
            }

            .topnav a:hover {
                background-color: #ddd;
                color: black;
            }

            .topnav a.active {
                background-color: #04AA6D;
                color: white;
            }
        </style>
    </head>

    <body>
        <div class="topnav">
            <a href=".">Browse</a>
            <a href="./profile.php">Profile</a>
            <a class="active" href="./customer_dashboard.php">Customer Dashboard</a>
            <a href="./seller-dashboard.php">Seller Dashboard</a>
        </div>
        <div class="menu text-right">
            <div class="wrapper">
            </div>
        </div>
        <h1> Customer Dashboard </h1>
        <div class="col text-center">
            <h1>Ongoing</h1>
            <br>
            <div class="heading">
                <?php include('customer_status.php'); ?>
            </div>
            <br>
            <br>
            <div class="heading">
                <h1>Completed Order History</h1>
            </div>
            <br>
            <div>
                <?php include('customer_order_history_fromDB.php'); ?>
            </div>
            <br>
            <br>
        </div>
        </div>
    <?php } else echo "<p>You don't have access<p>"; ?>