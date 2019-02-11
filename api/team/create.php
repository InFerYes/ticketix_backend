<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// get database connection
include_once '../config/database.php';
 
// instantiate team object
include_once '../objects/team.php';

require(realpath(__DIR__ . '/../../vendor/autoload.php'));

$database = new Database();
$db = $database->getConnection();

$auth = new \Delight\Auth\Auth($db);

$team = new team($db);

if ($auth->isLoggedIn()) {

    // get posted data
    $data = json_decode(file_get_contents("php://input"));

    // make sure data is not empty
    if(
        !empty($data->name)
    ){

        // set team property values
        $team->name = $data->name;
        $team->createdate = date('Y-m-d H:i:s');
        $team->modifdate = date('Y-m-d H:i:s');
        $team->authid = $auth->getUserId();

        // create the team
        if($team->create()){
        

            // set response code - 201 created
            http_response_code(201);

            // tell the user
            echo json_encode(array("message" => "Team was created."));
        }

        // if unable to create the team, tell the user
        else{

            // set response code - 503 service unavailable
            http_response_code(503);

            // tell the user
            echo json_encode(array("message" => "Unable to create team."));
        }
    }

    // tell the user data is incomplete
    else{

        // set response code - 400 bad request
        http_response_code(400);

        // tell the user
        echo json_encode(array("message" => "Unable to create team. Data is incomplete."));
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