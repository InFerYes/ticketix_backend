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

    $team->authid = $auth->getUserId();
    $team->id = isset($_GET['idteam']) ? $_GET['idteam'] : die();
    $team->idmember = isset($_GET['idmember']) ? $_GET['idmember'] : die();

    //is 'm leader? zo ja, mag verwijderen

    // create the team
    if($team->remove_teammember()){
    

        // set response code - 201 created
        http_response_code(201);

        // tell the user
        echo json_encode(array("message" => "Member removed."));
    }

    // if unable to create the team, tell the user
    else{

        // set response code - 503 service unavailable
        http_response_code(503);

        // tell the user
        echo json_encode(array("message" => "Unable to remove member."));
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