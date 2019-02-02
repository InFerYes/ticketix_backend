<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
// database connection will be here
// include database and object files
include_once '../config/database.php';
include_once '../objects/person.php';
 
// instantiate database and person object
$database = new Database();
$db = $database->getConnection();
 
// initialize object
$person = new Person($db);
 
// query person
$stmt = $person->read();
$num = $stmt->rowCount();
 
// check if more than 0 record found
if($num>0){
 
    // person array
    $person_arr=array();
    $person_arr["records"]=array();
 
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
            "firstname" => $firstname,
            "lastname" => $lastname,
            "nickname" => $nickname,
            "hasagreedtoprivacypolicy" => $hasagreedtoprivacypolicy,
            "hasorderedticket" => $hasorderedticket,
            "haspaid" => $haspaid,
            "idticket" => $idticket,
            "email" => $email,
            "modifdate" => $modifdate,
            "createdate" => $createdate,
            "teamname" => $teamname
        );
 
        array_push($person_arr["records"], $person_item);
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
