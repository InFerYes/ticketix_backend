<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/team.php';
require(realpath(__DIR__ . '/../../vendor/autoload.php'));

// get database connection
$database = new Database();
$db = $database->getConnection();

$auth = new \Delight\Auth\Auth($db);

if ($auth->isLoggedIn()) { //} && $auth->hasRole(\Delight\Auth\Role::ADMIN)) {

    // prepare team object
    $team = new team($db);
    
    // get id of team to be edited
    $data = json_decode(file_get_contents("php://input"));
    
    // set ID property of team to be edited
    $team->authid = $auth->getUserId();
    
    // set team property values
    $team->idleader = $data->idleader;
    $team->name = $data->name;
    $team->modifdate = date('Y-m-d H:i:s');
    

    // update the team
    if($team->update()){
    
        // set response code - 200 ok
        http_response_code(200);
    
        // tell the user
        echo json_encode(array("message" => "team was updated."));
    }
    
    // if unable to update the team, tell the user
    else{
    
        // set response code - 503 service unavailable
        http_response_code(503);
    
        // tell the user
        echo json_encode(array("message" => "Unable to update team."));
    }
}
else {
    // set response code - 404 Not found
    http_response_code(401);
 
    // tell the user no team found
    echo json_encode(
        array("message" => "Unauthorized.")
    );
}
?>