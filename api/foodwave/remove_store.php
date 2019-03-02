<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/foodwave.php';
require(realpath(__DIR__ . '/../../vendor/autoload.php'));

// get database connection
$database = new Database();
$db = $database->getConnection();

$auth = new \Delight\Auth\Auth($db);

if ($auth->isLoggedIn() && $auth->hasRole(\Delight\Auth\Role::ADMIN)) {

    // prepare foodwave object
    $foodwave = new foodwave($db);
    
    // get id of foodwave to be edited
    $data = json_decode(file_get_contents("php://input"));
    
    // set ID property of foodwave to be edited
    $foodwave->store_id = $data->store_id;
    
    // update the foodwave
    if($foodwave->remove_store()){
    
        // set response code - 200 ok
        http_response_code(200);
    
        // tell the user
        echo json_encode(array("message" => "Store was removed."));
    }
    
    // if unable to update the foodwave, tell the user
    else{
    
        // set response code - 503 service unavailable
        http_response_code(503);
    
        // tell the user
        echo json_encode(array("message" => "Unable to remove store."));
    }
}
else {
    // set response code - 404 Not found
    http_response_code(401);
 
    // tell the user no foodwave found
    echo json_encode(
        array("message" => "Unauthorized.")
    );
}
?>