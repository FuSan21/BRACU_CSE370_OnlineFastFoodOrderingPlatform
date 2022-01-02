<?php
class DBController {
	private $host = "localhost";
	private $user = "root";
	private $password = "";
	private $database = "online_fastfood_ordering_platform";
	private $conn;
	
	function __construct() {
		$this->conn = $this->connectDB();
	}
	
	function connectDB() {
		$conn = mysqli_connect($this->host,$this->user,$this->password,$this->database);
		return $conn;
	}
	
	function selectQuery($query) {
		$result = mysqli_query($this->conn,$query);
		while($row=mysqli_fetch_assoc($result)) {
			$resultset[] = $row;
		}
		if(!empty($resultset))
			return $resultset;
	}

	function insertQuery($query) {
		mysqli_query($this->conn, $query);
	}

	function deleteQuery($query) {
		mysqli_query($this->conn, $query);
	}

	function findIndexTable($table) {
		$query = "SELECT `AUTO_INCREMENT` FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '".$this->database."' AND TABLE_NAME = '".$table."'";
		$result = mysqli_query($this->conn,$query) -> fetch_row();
		return $result[0] ;
	}

	function product_rating($prdct) {
		$rating_sql = "SELECT `product_id` , ROUND(AVG(`given_rating`), 2) AS avgRating FROM `orders`,`order_productsandaddons` WHERE `orders`.`order_id`=`order_productsandaddons`.`order_id` and `product_id`='".$prdct."' and `given_rating` IS NOT NULL GROUP BY `product_id`";
		$result  = mysqli_query($this->conn,$rating_sql);
		$resultset = mysqli_fetch_assoc($result);
		if(!empty($resultset))
			return $resultset["avgRating"];
	}

	function dropqueryparam() {
		$base_url = strtok($_SERVER['REQUEST_URI'], '?');
		header('Location: '.$base_url);
	}
}
?>