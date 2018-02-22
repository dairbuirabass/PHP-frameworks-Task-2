<?php
header("Access-Control-Allow-Origin: *");

try
{
 $dsn = "mysql:host=mysli.oamk.fi;dbname=opisk_t6bada00";
 $db = new PDO ($dsn, "t6bada00", "ninja-game");
}
catch (PDOException $e)
{
 print ("Cannot connect to server\n");
 print ("Error code: " . $e->getCode() . "\n");
 print ("Error message: " . $e->getMessage() . "\n");
}

$statement=$db->prepare("SELECT * FROM markers");
$statement->execute();
$results=$statement->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo $json=json_encode($results);

?>
