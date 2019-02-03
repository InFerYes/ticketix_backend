<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// get database connection
include_once '../config/database.php';
require(realpath(__DIR__ . '/../../vendor/autoload.php'));
 
$database = new Database();
$db = $database->getConnection();
 
$auth = new \Delight\Auth\Auth($db);

$data = json_decode(file_get_contents("php://input"));
include_once '../objects/account.php';    

$account = new Account();

if(
    !empty($data->email) &&
    !empty($data->password)
){
    $account->email = $data->email;
    $account->username = null;
    $account->password = $data->email;

    try {
        $auth->login($account->email, $account->password);
    
        http_response_code(200);

        echo json_encode(
            array("message" => "OK.")
        );
    }
    catch (\Delight\Auth\InvalidEmailException $e) {
        http_response_code(403);

        echo json_encode(
            array("message" => "Forbidden: wrong email.")
        );

        die();
    }
    catch (\Delight\Auth\InvalidPasswordException $e) {
        http_response_code(403);

        echo json_encode(
            array("message" => "Forbidden: wrong password.")
        );

        die();
    }
    catch (\Delight\Auth\EmailNotVerifiedException $e) {
        http_response_code(412);
        
        echo json_encode(
            array("message" => "Precondition Failed: Email not verified.")
        );

        die();
    }
    catch (\Delight\Auth\TooManyRequestsException $e) {
        http_response_code(429);
        
        echo json_encode(
            array("message" => "Too Many Requests.")
        );
        
        die();
    }
}
else {
    http_response_code(400);
        
        echo json_encode(
            array("message" => "Bad Request.")
        );
}
?>