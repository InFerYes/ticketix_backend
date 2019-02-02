<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/person.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// prepare person object
$person = new person($db);
 
// set ID property of record to read
$person->id = isset($_GET['id']) ? $_GET['id'] : die();
 
// read the details of person to be edited
$person->readOne();
 
if($person->id!=null){
    // create array
    $person_arr = array(
        "id" => $person->id,
        "firstname" => $person->firstname,
        "lastname" => $person->lastname,
        "nickname" => $person->nickname,
        "hasagreedtoprivacypolicy" => $person->hasagreedtoprivacypolicy,
        "hasorderedticket" => $person->hasorderedticket,
        "haspaid" => $person->haspaid,
        "idticket" => $person->idticket,
        "email" => $person->email,
        "modifdate" => $person->modifdate,
        "createdate" => $person->createdate,
        "teamname" => $person->teamname
    );
 
    // set response code - 200 OK
    http_response_code(200);
 
    // make it json format
    echo json_encode($person_arr);
}
 
else{
    // set response code - 404 Not found
    http_response_code(404);
 
    // tell the user person does not exist
    echo json_encode(array("message" => "person does not exist."));
}
?>