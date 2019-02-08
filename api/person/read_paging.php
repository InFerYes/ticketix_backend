<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
// include database and object files
include_once '../config/core.php';
include_once '../shared/utilities.php';
include_once '../config/database.php';
include_once '../objects/person.php';
require(realpath(__DIR__ . '/../../vendor/autoload.php'));

// utilities
$utilities = new Utilities();
 
// instantiate database and person object
$database = new Database();
$db = $database->getConnection();

$auth = new \Delight\Auth\Auth($db);

if ($auth->isLoggedIn() && $auth->hasRole(\Delight\Auth\Role::ADMIN)) {
    // initialize object
    $person = new person($db);
    
    // query persons
    $stmt = $person->readPaging($from_record_num, $records_per_page);
    $num = $stmt->rowCount();
    
    // check if more than 0 record found
    if($num>0){
    
        // persons array
        $persons_arr=array();
        $persons_arr["records"]=array();
        $persons_arr["paging"]=array();
    
        // retrieve our table contents
        // fetch() is faster than fetchAll()
        // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            // extract row
            // this will make $row['name'] to
            // just $name only
            extract($row);
    
            $person_item = array(
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
    
            array_push($persons_arr["records"], $person_item);
        }
    
    
        // include paging
        $total_rows=$person->count();
        $page_url="{$home_url}person/read_paging.php?";
        $paging=$utilities->getPaging($page, $total_rows, $records_per_page, $page_url);
        $persons_arr["paging"]=$paging;
    
        // set response code - 200 OK
        http_response_code(200);
    
        // make it json format
        echo json_encode($persons_arr);
    }
    
    else{
    
        // set response code - 404 Not found
        http_response_code(404);
    
        // tell the user persons does not exist
        echo json_encode(
            array("message" => "No persons found.")
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