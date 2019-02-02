<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// get database connection
include_once '../config/database.php';
 
// instantiate person object
include_once '../objects/person.php';
 
$database = new Database();
$db = $database->getConnection();
 
$person = new person($db);
 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// make sure data is not empty
if(
    !empty($data->firstname) &&
    !empty($data->lastname) &&
    !empty($data->nickname) &&
    !empty($data->email)
){
 
    // set person property values
    $person->firstname = $data->firstname;
    $person->lastname = $data->lastname;
    $person->nickname = $data->nickname;
    $person->email = $data->email;
    $person->createdate = date('Y-m-d H:i:s');
    $person->modifdate = date('Y-m-d H:i:s');
    $person->idticket = $data->idticket;
    $person->hasagreedtoprivacypolicy = $data->hasagreedtoprivacypolicy;
    $person->hasorderedticket = $data->hasorderedticket;
    $person->haspaid = $data->haspaid;

    // create the person
    if($person->create()){
       
 
        // set response code - 201 created
        http_response_code(201);
 
        // tell the user
        echo json_encode(array("message" => "person was created."));
    }
 
    // if unable to create the person, tell the user
    else{
 
        // set response code - 503 service unavailable
        http_response_code(503);
 
        // tell the user
        echo json_encode(array("message" => "Unable to create person."));
    }
}
 
// tell the user data is incomplete
else{
 
    // set response code - 400 bad request
    http_response_code(400);
 
    // tell the user
    echo json_encode(array("message" => "Unable to create person. Data is incomplete."));
}
?>