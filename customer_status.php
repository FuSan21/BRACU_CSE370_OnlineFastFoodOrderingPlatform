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

  .btnUpdate {
    padding: 5px 10px;
    margin-left: 5px;
    background-color: #efefef;
    border: #E0E0E0 1px solid;
    color: #211a1a;
    text-align: center;
    text-decoration: none;
    border-radius: 3px;
    cursor: pointer;
  }
</style>


<?php



//queries diye db theke fetch korteso
$sql = "SELECT order_id,date,time,status from orders where status != 'completed' AND status!= 'canceled' AND customer_id = '" . $_SESSION["user-id"] . "'";
$result = $db_handle->selectQuery($sql);

//pushing values into array
if (!empty($result)) {
  echo "<table class='myTable'>";
  echo "<th>Queue No </th>";
  echo "<th>Order ID </th>";
  echo "<th>Product Name </th>";
  echo "<th>Orderd On </th>";
  echo "<th>Cancel </th>";


  // output data of each row
  foreach($result as $key => $row){
    echo "<tr><td>" . $db_handle->orderQueue($row['order_id']) . "</td><td>" . $row['order_id'] . "</td><td>";
    $db_handle->printProducts4orderid($row['order_id']);
    echo "</td><td>" . $row["date"] . "<br>" . $row["time"] . "<td><form method='POST' action='?action=update_order_status&order_id=" . $row['order_id'] . "'><input class='btnUpdate' type='submit' value='Cancel Order'> </form></td>" . "</td></tr>";
  }
  echo "</table>";
} else {
  echo "No Ongoing Orders!";
}
?>