<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// get database connection
include_once '../config/database.php';
require(realpath(__DIR__ . '/../../vendor/autoload.php'));
 
$database = new Database();
$db = $database->getConnection();
 
$auth = new \Delight\Auth\Auth($db);
if ($auth->isLoggedIn()) {
    http_response_code(200);
    $isLoggedIn = array("isLoggedIn" => true);

    echo json_encode($isLoggedIn);
}
else {
    http_response_code(401);
    $isLoggedIn = array("isLoggedIn" => false);
    echo json_encode($isLoggedIn);
    die('');
}
?>