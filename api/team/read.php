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
    include_once '../objects/team.php';
    
    // initialize object
    $team = new team($db);
    
    // query team
    $stmt = $team->read();
    $num = $stmt->rowCount();
    
    // check if more than 0 record found
    if($num>0){
    
        // team array
        $team_arr=array();
        $team_arr["records"]=array();
    
        // retrieve our table contents
        // fetch() is faster than fetchAll()
        // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            // extract row
            // this will make $row['name'] to
            // just $name only
            extract($row);

            $team_item=array(
                "id" => $id,
                "name" => $name,
                "createdate" => $createdate,
                "modifdate" => $modifdate,
                "leadernickname" => $leadernickname,
                "idpersonleader" => $idpersonleader
            );
    
            array_push($team_arr["records"], $team_item);
        }
    
        // set response code - 200 OK
        http_response_code(200);
    
        // show team data in json format
        echo json_encode($team_arr);
    }
    else{
    
        // set response code - 404 Not found
        http_response_code(404);
    
        // tell the user no team found
        echo json_encode(
            array("message" => "No team found.")
        );
    }
}
else {
    // set response code - 404 Not found
    http_response_code(401);
 
    // tell the user no team found
    echo json_encode(
        array("message" => "Unauthorized.")
    );
}
?>