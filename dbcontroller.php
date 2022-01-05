<?php
class DBController
{
    private $host = "localhost";
    private $user = "root";
    private $password = "";
    private $database = "online_fastfood_ordering_platform";
    private $conn;

    function __construct()
    {
        $this->conn = $this->connectDB();
    }

    function connectDB()
    {
        $conn = mysqli_connect($this->host, $this->user, $this->password, $this->database);
        return $conn;
    }

    function selectQuery($query)
    {
        $result = mysqli_query($this->conn, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        if (!empty($resultset))
            return $resultset;
    }

    function executeQuery($query)
    {
        mysqli_query($this->conn, $query);
    }

    function findIndexTable($table)
    {
        $query = "SELECT `AUTO_INCREMENT` FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '" . $this->database . "' AND TABLE_NAME = '" . $table . "'";
        $result = mysqli_query($this->conn, $query)->fetch_row();
        return $result[0];
    }

    function product_rating($prdct)
    {
        $rating_sql = "SELECT `product_id` , ROUND(AVG(`given_rating`), 2) AS avgRating FROM `orders`,`order_productsandaddons` WHERE `orders`.`order_id`=`order_productsandaddons`.`order_id` and `product_id`='" . $prdct . "' and `given_rating` IS NOT NULL GROUP BY `product_id`";
        $result  = mysqli_query($this->conn, $rating_sql);
        $resultset = mysqli_fetch_assoc($result);
        if (!empty($resultset))
            return $resultset["avgRating"];
    }

    function dropqueryparam()
    {
        $base_url = strtok($_SERVER['REQUEST_URI'], '?');
        header('Location: ' . $base_url);
    }

    function printProducts4orderid($id)
    {
        $query = "SELECT `product`.`name` AS 'product',`order_productsandaddons`.`name` AS 'addon',`quantity` FROM `product`,`order_productsandaddons` WHERE `product`.`product_id`=`order_productsandaddons`.`product_id` AND `order_productsandaddons`.`order_id`=" . $id;
        $result = mysqli_query($this->conn, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            echo $row['product'];
            if ($row['addon'] != "None") {
                echo "+" . $row['addon'];
            }
            echo " X " . $row['quantity'];
            echo "<br>";
        }
    }
    function status_update($order_id, $order_status)
    {
        if ($order_status == 'pending') {
            $sql = "UPDATE orders SET `status`='on queue' WHERE `order_id`=$order_id ";
        } elseif ($order_status == 'on queue') {
            $sql = "UPDATE orders SET `status`='preparing' WHERE `order_id`=$order_id ";
        } elseif ($order_status == 'preparing') {
            $sql = "UPDATE orders SET `status`='completed' WHERE `order_id`=$order_id ";
        }
        mysqli_query($this->conn, $sql);
    }
    function orderQueue($id)
    {
        $query = "SELECT ROW_NUMBER() OVER(ORDER BY `order_id`) AS `num_row`, `order_id` FROM `orders` WHERE `status` != 'completed' AND STATUS != 'canceled'";
        $result  = mysqli_query($this->conn, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[$row["order_id"]] = $row["num_row"];
        }
        return ($resultset[$id]);
    }
}
