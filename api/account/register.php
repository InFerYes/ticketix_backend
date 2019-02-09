<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// get database connection
include_once '../config/database.php';
include_once '../objects/person.php';
include_once '../objects/account.php';
require(realpath(__DIR__ . '/../../vendor/autoload.php'));
 
$database = new Database();
$db = $database->getConnection();
 
$auth = new \Delight\Auth\Auth($db);

$account = new account($db);
$person = new person($db);

$data = json_decode(file_get_contents("php://input"));

if(
    !empty($data->person) &&
    !empty($data->account) &&
    !empty($data->account->email) &&
    !empty($data->account->password) &&
    !empty($data->person->firstname) &&
    !empty($data->person->lastname) &&
    !empty($data->person->nickname) &&
    !empty($data->person->email)
){
    $account->email = $data->account->email;
    $account->username = null;
    $account->password = $data->account->password;

    $person->firstname = $data->person->firstname;
    $person->lastname = $data->person->lastname;
    $person->nickname = $data->person->nickname;
    $person->email = $data->person->email;
    $person->createdate = date('Y-m-d H:i:s');
    $person->modifdate = date('Y-m-d H:i:s');
    $person->hasagreedtoprivacypolicy = $data->person->hasagreedtoprivacypolicy;
    $person->hasorderedticket = $data->person->hasorderedticket;
    $person->haspaid = $data->person->haspaid;

    try {
        
        $person->iduser = $auth->register($account->email, $account->password, $account->username);
        //$newUserId = $userId = $auth->register($account->email, $account->password, $account->username, function ($selector, $token) use ($person, $auth) {});

        if($person->iduser !== null && $person->iduser > 0 && $person->create() !==false){
            http_response_code(201);

            echo json_encode(array("message" => "Account and profile have been created."));
        }
        else{
            http_response_code(503);

            echo json_encode(array("message" => "Unable to create profile, but account was created."));
        }
    }
    catch (\Delight\Auth\InvalidEmailException $e) {
        http_response_code(406);

        echo json_encode(
            array("message" => "Not Acceptable: invalid email.")
        );

        die();
    }
    catch (\Delight\Auth\InvalidPasswordException $e) {
        http_response_code(406);
        
        echo json_encode(
            array("message" => "Not Acceptable: invalid password.")
        );

        die();
    }
    catch (\Delight\Auth\UserAlreadyExistsException $e) {
        http_response_code(409);
        
        echo json_encode(
            array("message" => "Conflict: user already exists.")
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