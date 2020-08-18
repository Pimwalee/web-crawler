<?php
include("../config.php");

if(isset($_POST["linkId"])) { // if the link has been sent then we go update the value
    $query = $con->prepare("UPDATE sites SET clicks = clicks +1 WHERE id = :id");
    $query->bindParam(":id", $_POST["linkId"]);

    $query ->execute();
}
else {
    echo "No link passed to page";
}
?>