<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// get database connection
include_once '../config/database.php';
 
// instantiate foodwave object
include_once '../objects/foodwave.php';

require(realpath(__DIR__ . '/../../vendor/autoload.php'));

$database = new Database();
$db = $database->getConnection();

$auth = new \Delight\Auth\Auth($db);

$foodwave = new foodwave($db);

// get posted data
$data = json_decode(file_get_contents("php://input"));

if ($auth->isLoggedIn() && $auth->hasRole(\Delight\Auth\Role::ADMIN)) {

    // make sure data is not empty
    if(
        !empty($data->store_name) &&
        !empty($data->store_telephone) &&
        !empty($data->store_email) &&
        !empty($data->store_address)
    ){

        // set foodwave property values
        $foodwave->store_name = $data->store_name;
        $foodwave->store_telephone = $data->store_telephone;
        $foodwave->store_email = $data->store_email;
        $foodwave->store_address = $data->store_address;
        $foodwave->store_createdate = date('Y-m-d H:i:s');
        $foodwave->store_modifdate = date('Y-m-d H:i:s');

        // create the foodwave
        if($foodwave->create_store()){

            // set response code - 201 created
            http_response_code(201);

            // tell the user
            echo json_encode(array("message" => "Store was created."));
        }

        // if unable to create the foodwave, tell the user
        else{

            // set response code - 503 service unavailable
            http_response_code(503);

            // tell the user
            echo json_encode(array("message" => "Unable to create store."));
        }
    }

    // tell the user data is incomplete
    else{

        // set response code - 400 bad request
        http_response_code(400);

        // tell the user
        echo json_encode(array("message" => "Unable to create store. Data is incomplete."));
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