<?php
ob_start();

try {

    $con = new PDO("mysql:dbname=goowalee;host=localhost", "root", ""); 
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);//show err and stop executing
}
catch(PDOExeption $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>