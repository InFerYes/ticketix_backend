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

try {
    $auth->logOutEverywhere();
    http_response_code(200);

    echo json_encode(
        array("message" => "OK.")
    );
}
catch (\Delight\Auth\NotLoggedInException $e) {
    http_response_code(401);

    echo json_encode(
        array("message" => "Expectation Failed: Not logged in.")
    );
    die('');
}
?>