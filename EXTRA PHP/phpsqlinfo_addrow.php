<?php

try
{
 $dsn = "mysql:host=mysli.oamk.fi;dbname=opisk_t6bada00";
 $db = new PDO ($dsn, "t6bada00", "ninja-game");
 echo ("Connected");
}
catch (PDOException $e)
{
 print ("Cannot connect to server\n");
 print ("Error code: " . $e->getCode() . "\n");
 print ("Error message: " . $e->getMessage() . "\n");
}

$stmt=$db->prepare("INSERT INTO markers (info, lat, lng, img) VALUES (:info, :lat, :lng, :img)");
		$stmt->bindParam(':info', $info);
    $stmt->bindParam(':lat', $lat);
    $stmt->bindParam(':lng', $lng);
    $stmt->bindParam(':img', $img);

    // Gets data from URL parameters.
    $info = $_GET['info'];
    $lat = $_GET['lat'];
    $lng = $_GET['lng'];
    $img = $_GET['img'];

	  $stmt->execute();
?>
