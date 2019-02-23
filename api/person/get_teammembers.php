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

if ($auth->isLoggedIn()) {
    include_once '../objects/person.php';    
    
    // initialize object
    $person = new Person($db);
    $person->authid = $auth->getUserId();
    
    // query person
    $stmt = $person->getTeamMembers();
    $num = $stmt->rowCount();
    
    // check if more than 0 record found
    if($num>0){
    
        // person array
        $person_arr=array();
    
        // retrieve our table contents
        // fetch() is faster than fetchAll()
        // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            // extract row
            // this will make $row['name'] to
            // just $name only
            extract($row);
    
            $person_item=array(
                "id" => $id,
                "nickname" => $nickname
            );
    
            array_push($person_arr, $person_item);
        }
    
        // set response code - 200 OK
        http_response_code(200);
    
        // show person data in json format
        echo json_encode($person_arr);
    }
    else{
    
        // set response code - 404 Not found
        http_response_code(404);
    
        // tell the user no person found
        echo json_encode(
            array("message" => "No person found.")
        );
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