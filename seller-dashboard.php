<?php
session_start();
require_once("dbcontroller.php");
$db_handle = new DBController();
if (!empty($_GET["action"])) {
    switch ($_GET["action"]) {
        case "update_order_status":
            $db_handle->status_update($_GET['order_id'], $_GET['current_status']);
            $db_handle->dropqueryparam();
            break;
        case "create_cupon":
            $sql = "INSERT INTO `cupons` (`amount`, `min_spend`, `cupon_id`, `customer_id`, `seller_id`) VALUES ('" . $_POST['amount'] . "','" . $_POST['min_spend'] . "','" . $_POST['cupon_id'] . "','" . $_POST['customer_id'] . "','1')";
            $db_handle->executeQuery($sql);
            $db_handle->dropqueryparam();
            break;
        case "create_product":
            $currIndex = $db_handle->findIndexTable("product");
            $sql = "INSERT INTO `product` (`price`,`product_id`,`name`,`image`,`category`,`seller_id`) VALUES('" . $_POST['price'] . "','" . $currIndex . "','" . $_POST['name'] . "','" . $_POST['image'] . "','" . $_POST['category'] . "','1')";
            $db_handle->executeQuery($sql);
            $sql2 = "INSERT INTO `addon` (`name`,`price`,`product_id`,`seller_id`) VALUES ('None','0','" . $currIndex . "','1')";
            $db_handle->executeQuery($sql2);
            $db_handle->dropqueryparam();
            break;
        case "create_addon":
            $sql = "INSERT INTO `addon` (`name`,`price`,`product_id`,`seller_id`) VALUES ('" . $_POST['name'] . "','" . $_POST['price'] . "','" . $_POST['product_id'] . "','1')";
            $db_handle->executeQuery($sql);
            $db_handle->dropqueryparam();
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard</title>
    <link rel="stylesheet" href="seller_dashboard.css">
</head>

<body>
    <div class="topnav">
        <a href=".">Browse</a>
        <a href="./profile.php">Profile</a>
        <a href="./customer_dashboard.php">Customer Dashboard</a>
        <a class="active" href="./seller-dashboard.php">Seller Dashboard</a>
    </div>
    <h1> Seller Dashboard </h1>
    <div class="txt-heading">View Order History</div>
    <div class="order-list">
        <table class="order-table" cellpadding="10" cellspacing="1">

            <tr>
                <th style="text-align:center;">Queue</th>
                <th style="text-align:center;">Order ID</th>
                <th style="text-align:center;">Order Name</th>
                <th style="text-align:center;">Order date</th>
                <th style="text-align:center;">Time of order placement</th>
                <th style="text-align:center;">Order status</th>
                <th style="text-align:center;">Update status</th>
            </tr>
            <?php
            $sql = "SELECT order_id,date,time,status from orders where status != 'completed' and status != 'canceled'";
            $result = $db_handle->selectQuery($sql);
            if (!empty($result)) {
                foreach ($result as $key => $rows) {
            ?>
                    <tr>
                        <td style="text-align:center;"><?php echo $key + 1; ?></td>
                        <td style="text-align:center;"><?php echo $rows["order_id"]; ?></td>
                        <td style="text-align:center;"><?php $db_handle->printProducts4orderid($rows["order_id"]); ?></td>
                        <td style="text-align:center;"><?php echo $rows["date"]; ?></td>
                        <td style="text-align:center;"><?php echo $rows["time"]; ?></td>
                        <td style="text-align:center;"><?php echo $rows["status"]; ?></td>
                        <td style="text-align:center;">
                            <form method="post" action="?action=update_order_status&order_id=<?php echo $rows["order_id"]; ?>&current_status=<?php echo $rows["status"]; ?>"><input class="btnUpdate" type="submit" value="Update Status"> </form>
                        </td>
                    </tr>

            <?php
                }
            }
            ?>
        </table>
    </div>
    <div class="txt-heading">Create Cupon</div>
    <div class="section">
        <div class="section-border">
            <form method="post" action="?action=create_cupon">
                <label for="amount">Amount:</label>
                <input type="text" name="amount"><br><br>
                <label for="min_spend">Minimum Spend:</label>
                <input type="text" name="min_spend"><br><br>
                <label for="cupon_id">Cupon No:</label>
                <input type="text" name="cupon_id"><br><br>
                <label for="customer_id">Customer ID:</label>
                <input type="text" name="customer_id"><br><br>
                <input class="btnUpdate" type="submit" value="Create Cupon">
            </form>
        </div>
    </div>
    <br>
    <div class="txt-heading">Create Product</div>
    <div class="section">
        <div class="section-border">
            <form method="post" action="?action=create_product">
                <label for="price">Price:</label>
                <input type="text" name="price"><br><br>
                <label for="name">Name:</label>
                <input type="text" name="name"><br><br>
                <label for="image">Image:</label>
                <input type="text" name="image"><br><br>
                <label for="category">Category</label>
                <input type="text" name="category"><br><br>
                <input class="btnUpdate" type="submit" value="Create Product">
            </form>
        </div>
    </div>
    <br>
    <div class="txt-heading">Create Addon</div>
    <div class="section">
        <div class="section-border">
            <form method="post" action="?action=create_addon">
                <label for="name">Name:</label>
                <input type="text" name="name"><br><br>
                <label for="price">Price:</label>
                <input type="text" name="price"><br><br>
                <label for="product_id">Product ID:</label>
                <input type="text" name="product_id"><br><br>
                <input class="btnUpdate" type="submit" value="Create Addon">
            </form>
        </div>
    </div>