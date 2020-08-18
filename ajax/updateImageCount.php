<?php
include("../config.php");

if(isset($_POST["imageUrl"])) { // if the link has been sent then we go update the value
    $query = $con->prepare("UPDATE image SET clicks = clicks +1 WHERE imageUrl = :imageUrl");
    $query->bindParam(":imageUrl", $_POST["imageUrl"]);

    $query ->execute();
}
else {
    echo "No image URL passed to page";
}
?>