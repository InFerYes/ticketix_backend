<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/person.php';
require(realpath(__DIR__ . '/../../vendor/autoload.php'));

// get database connection
$database = new Database();
$db = $database->getConnection();

$auth = new \Delight\Auth\Auth($db);

if ($auth->isLoggedIn() && $auth->hasRole(\Delight\Auth\Role::ADMIN)) {

    // prepare person object
    $person = new person($db);
    
    // get id of person to be edited
    $data = json_decode(file_get_contents("php://input"));
    
    // set ID property of person to be edited
    $person->id = $data->id;
    
    // set person property values
    $person->firstname = $data->firstname;
    $person->lastname = $data->lastname;
    $person->nickname = $data->nickname;
    $person->email = $data->email;
    $person->modifdate = date('Y-m-d H:i:s');
    $person->idticket = $data->idticket;
    $person->hasagreedtoprivacypolicy = $data->hasagreedtoprivacypolicy;
    $person->hasorderedticket = $data->hasorderedticket;
    $person->haspaid = $data->haspaid;

    // update the person
    if($person->update()){
    
        // set response code - 200 ok
        http_response_code(200);
    
        // tell the user
        echo json_encode(array("message" => "person was updated."));
    }
    
    // if unable to update the person, tell the user
    else{
    
        // set response code - 503 service unavailable
        http_response_code(503);
    
        // tell the user
        echo json_encode(array("message" => "Unable to update person."));
    }
}
else {
    // set response code - 404 Not found
    http_response_code(401);
 
    // tell the user no person found
    echo json_encode(
        array("message" => "Unauthorized.")
    );
}
?>