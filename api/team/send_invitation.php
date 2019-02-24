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
        !empty($data->invitationemail)
    ){
        if($team->user_with_mail_exists($data->invitationemail)) {
            $team->authid = $auth->getUserId();
            $team->id = $data->id;

            if($team->create_invitation()){
                http_response_code(201);

                echo json_encode(array("message" => "Invitation was created."));
            }

            else{
                http_response_code(503);

                echo json_encode(array("message" => "Unable to create invitation."));
            }
        }
        else {
            if($team->send_invitation_mail()) {
                http_response_code(412);

                echo json_encode(array("message" => "Invitation was sent."));
            }
            else{
                http_response_code(503);

                echo json_encode(array("message" => "Unable to send invitation."));
            }
        }
        
    }
    else{
        http_response_code(400);

        echo json_encode(array("message" => "Unable to create invitation. Data is incomplete."));
    }
}
else {
    http_response_code(401);
 
    echo json_encode(
        array("message" => "Unauthorized.")
    );
}
?>