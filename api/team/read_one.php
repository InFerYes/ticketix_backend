<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/team.php';
require(realpath(__DIR__ . '/../../vendor/autoload.php'));

// get database connection
$database = new Database();
$db = $database->getConnection();

$auth = new \Delight\Auth\Auth($db);

if ($auth->isLoggedIn()) {
    // prepare team object
    $team = new team($db);
    
    // set ID property of record to read
    $team->authid = $auth->getUserId();//isset($_GET['authid']) ? $_GET['authid'] : die();
    
    // read the details of team to be edited
    $team->readOne();
    
    if($team->id!=null){
        // create array
        $team_arr = array(
            "id" => $team->id,
            "name" => $team->name,
            "leadernickname" => $team->leadernickname,
            "modifdate" => $team->modifdate,
            "createdate" => $team->createdate,
            "idleader" => $team->idleader,
            "isinvitationopen" => $team->isinvitationopen,
            "ismember" => $team->ismember
        );
    
        // set response code - 200 OK
        http_response_code(200);
    
        // make it json format
        echo json_encode($team_arr);
    }
    
    else{
        // set response code - 404 Not found
        http_response_code(404);
    
        // tell the user team does not exist
        echo json_encode(array("message" => "team does not exist."));
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