<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once '../config/database.php';
require(realpath(__DIR__ . '/../../vendor/autoload.php'));

// database connection will be here
// include database and object files

// instantiate database
$database = new Database();
$db = $database->getConnection();

$auth = new \Delight\Auth\Auth($db);

if ($auth->isLoggedIn() && $auth->hasRole(\Delight\Auth\Role::ADMIN)) {
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
            $userId = $auth->admin()->createUser($account->email, $account->password, $account->username);
        
            //echo 'We have signed up a new user with the ID ' . $userId;
            http_response_code(200);

            echo json_encode(
                array("message" => "OK.")
            );
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