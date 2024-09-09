<?php 

try {

	$connection = new PDO("mysql:dbname=notgoogle;host=localhost:3307", "root", "1234");
	$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
}
catch(PDOExeption $e) {
	echo "Connection failed: " . $e->getMessage();
}
?>