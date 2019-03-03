<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/foodwave.php';
require(realpath(__DIR__ . '/../../vendor/autoload.php'));

// get database connection
$database = new Database();
$db = $database->getConnection();

$auth = new \Delight\Auth\Auth($db);

if ($auth->isLoggedIn()) {
    $foodwave = new foodwave($db);
        
    // read the details of foodwave to be edited
    $stmt = $foodwave->get_active_foodwave();

    $num = $stmt->rowCount();
    
    if($num>0){
        $foodwave_arr = array();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            
            extract($row);
            $products = $foodwave->get_foodwave_products($store_id);

            $num_products = $products->rowCount();

            if ($num_products>0) {
                $product_arr=array();

                while($product_row = $products->fetch(PDO::FETCH_ASSOC)) {
                    extract($product_row);

                    $product_item=array(
                        "product_id" => $product_id,
                        "product_price" => $product_price,
                        "product_name" => $product_name
                    );

                    array_push($product_arr, $product_item);
                }
            }

            $foodwave_item = array(
                "store_name" => $store_name,
                "store_id" => $store_id,
                "products" => $product_arr
            );

            array_push($foodwave_arr, $foodwave_item);
        }
    
        // set response code - 200 OK
        http_response_code(200);
    
        // make it json format
        echo json_encode($foodwave_arr);
    }
    
    else{
        // set response code - 404 Not found
        http_response_code(404);
    
        // tell the user foodwave does not exist
        echo json_encode(array("message" => "There is no active foodwave"));
    }
}
else {
    // set response code - 404 Not found
    http_response_code(401);
 
    // tell the user no foodwave found
    echo json_encode(
        array("message" => "Unauthorized.")
    );
}
?>