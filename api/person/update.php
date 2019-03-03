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

if ($auth->isLoggedIn()) {

    // prepare person object
    $person = new person($db);
    
    // get id of person to be edited
    $data = json_decode(file_get_contents("php://input"));
    
    // set ID property of person to be edited
    $person->authid = $auth->getUserId();
    
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
    
        $person->iduser = $auth->getUserId();//= isset($_GET['id']) ? $_GET['id'] : die();
    
        // read the details of person to be edited
        $person->readOne();
        
        if($person->iduser!=null){
            // create array
            $person_arr = array(
                "firstname" => $person->firstname,
                "lastname" => $person->lastname,
                "nickname" => $person->nickname,
                "hasagreedtoprivacypolicy" => (bool)$person->hasagreedtoprivacypolicy,
                "hasorderedticket" => (bool)$person->hasorderedticket,
                "haspaid" => (bool)$person->haspaid,
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