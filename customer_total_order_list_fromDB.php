<style>
  .myTable {
    width: 100%;
    text-align: left;
    background-color: lemonchiffon;
    border-collapse: collapse;
  }

  .myTable th {
    background-color: goldenrod;
    color: white;
    text-align: center;
  }

  .myTable td,
  .myTable th {
    padding: 10px;
    border: 1px solid goldenrod;
  }

  .rating {
    float: left;
    height: 46px;
    padding: 0 10px;
  }

  .rating:not(:checked)>input {
    position: absolute;
    top: -9999px;
  }

  .rating:not(:checked)>label {
    float: right;
    width: 1em;
    overflow: hidden;
    white-space: nowrap;
    cursor: pointer;
    font-size: 30px;
    color: #ccc;
  }

  .rating:not(:checked)>label:before {
    content: '★ ';
  }

  .rating>input:checked~label {
    color: #ffc700;
  }

  .rating:not(:checked)>label:hover,
  .rating:not(:checked)>label:hover~label {
    color: #deb217;
  }

  .rating>input:checked+label:hover,
  .rating>input:checked+label:hover~label,
  .rating>input:checked~label:hover,
  .rating>input:checked~label:hover~label,
  .rating>label:hover~input:checked~label {
    color: #c59b08;
  }

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

<?php
session_start();
require_once("dbcontroller.php");
$db_handle = new DBController();
if (!empty($_GET["action"])) {
  switch ($_GET["action"]) {
    case "update_rating":
      $db_handle->executequery("UPDATE `orders` SET `given_rating` = '" . $_POST['rating'] . "' WHERE `order_id` = '" . $_GET['order_id'] . "'");
      $db_handle->dropqueryparam();
      break;
  }
}
?>
<div class="topnav">
  <a href=".">Browse</a>
  <a href="./profile.php">Profile</a>
  <a class="active" href="./customer_dashboard.php">Customer Dashboard</a>
  <a href="./seller-dashboard.php">Seller Dashboard</a>
</div>
<?php
$sql =  "SELECT `product`.name AS 'product', `order_productsandaddons`.`name` AS 'addon', `order_productsandaddons`.`quantity`, `orders`.`order_id`, `orders`.`date`, `orders`.`time`, `orders`.`given_rating`
FROM `orders`,`order_productsandaddons`, `product`
WHERE `orders`.`order_id`=`order_productsandaddons`.`order_id` 
AND `order_productsandaddons`.`product_id` = `product`.`product_id` 
AND `orders`.`status` = 'completed'
AND `orders`.customer_id = '" . $_SESSION["user-id"] . "' ";
$result = $db_handle->selectQuery($sql);



if (!empty($result)) {
  echo "<table class='myTable'>";
  echo "<th>Order ID</th>";
  echo "<th>Product Name</th>";
  echo "<th>Orderd On</th>";
  echo "<th>Given Rating</th>";
  foreach ($result as $key => $row) {
    echo "<tr><td>" . $row['order_id'] . "</td><td>" . $row["product"];
    if ($row['addon'] != "None") {
      echo "+" . $row['addon'];
    }
    echo " X" . $row["quantity"] . "</td><td>" . $row["date"] . " <br>Time: " . $row["time"] . "</td><td>";
?>



    <form class='rating' method="POST" action="?action=update_rating&order_id=<?php echo $row['order_id']; ?>">
      <input type='radio' id='<?php echo $row['order_id']; ?>star5' name='rating' value='5' onclick='this.form.submit();' <?php if ($row["given_rating"] == '5') {
                                                                                                                            echo "checked";
                                                                                                                          } ?> />
      <label for='<?php echo $row['order_id']; ?>star5' title='text'>5 stars</label>
      <input type='radio' id='<?php echo $row['order_id']; ?>star4' name='rating' value='4' onclick='this.form.submit();' <?php if ($row["given_rating"] == '4') {
                                                                                                                            echo "checked";
                                                                                                                          } ?> />
      <label for='<?php echo $row['order_id']; ?>star4' title='text'>4 stars</label>
      <input type='radio' id='<?php echo $row['order_id']; ?>star3' name='rating' value='3' onclick='this.form.submit();' <?php if ($row["given_rating"] == '3') {
                                                                                                                            echo "checked";
                                                                                                                          } ?> />
      <label for='<?php echo $row['order_id']; ?>star3' title='text'>3 stars</label>
      <input type='radio' id='<?php echo $row['order_id']; ?>star2' name='rating' value='2' onclick='this.form.submit();' <?php if ($row["given_rating"] == '2') {
                                                                                                                            echo "checked";
                                                                                                                          } ?> />
      <label for='<?php echo $row['order_id']; ?>star2' title='text'>2 stars</label>
      <input type='radio' id='<?php echo $row['order_id']; ?>star1' name='rating' value='1' onclick='this.form.submit();' <?php if ($row["given_rating"] == '1') {
                                                                                                                            echo "checked";
                                                                                                                          } ?> />
      <label for='<?php echo $row['order_id']; ?>star1' title='text'>1 star</label>
    </form>
    </td>
    </tr>
<?php
  }
  echo "</table>";
} else {
  echo "No previous order FOUND!";
}

?>