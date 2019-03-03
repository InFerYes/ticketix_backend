<?php
class Foodwave{
 
    // database connection and table name
    private $conn;
 
    // object properties
    public $store_id;
    public $store_name;
    public $store_telephone;
    public $store_email;
    public $store_address;
    public $store_createdate;
    public $store_modifdate;
    public $store_isactive;
    public $product_id;
    public $product_price;
    public $product_name;
    public $products;
 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    function create_store(){
        $query = "
        INSERT INTO foodwave_store 
        SET
            Name=:store_name, 
            Telephone=:store_telephone, 
            Email=:store_email, 
            Address=:store_address,
            CreateDate=:store_createdate,
            ModifDate=:store_modifdate
        ";
        
        // prepare query
        $stmt = $this->conn->prepare($query);
        
        // sanitize
        $this->store_name=htmlspecialchars(strip_tags($this->store_name));
        $this->store_telephone=htmlspecialchars(strip_tags($this->store_telephone));
        $this->store_email=htmlspecialchars(strip_tags($this->store_email));
        $this->store_address=htmlspecialchars(strip_tags($this->store_address));
        $this->store_createdate=htmlspecialchars(strip_tags($this->store_createdate));
        $this->store_modifdate=htmlspecialchars(strip_tags($this->store_modifdate));
    
        // bind values
        $stmt->bindParam(":store_name", $this->store_name);
        $stmt->bindParam(":store_telephone", $this->store_telephone);
        $stmt->bindParam(":store_email", $this->store_email);
        $stmt->bindParam(":store_address", $this->store_address);
        $stmt->bindParam(":store_createdate", $this->store_createdate);
        $stmt->bindParam(":store_modifdate", $this->store_modifdate);
    
        // execute query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

    function remove_store(){
    
        // update query
        // query to insert record
        $query = "
        UPDATE foodwave_store
        SET
            IsActive=0
        WHERE
            Id = :store_id
        ";

        //$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        // prepare query
        $stmt = $this->conn->prepare($query);

        $this->store_id=htmlspecialchars(strip_tags($this->store_id));

        // bind values
        $stmt->bindParam(":store_id", $this->store_id, PDO::PARAM_INT);

        // execute query
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    function get_active_foodwave(){
        $activewavequery = "
            SELECT 
                s.Name AS store_name,
                s.Id AS store_id
            FROM foodwave_store s
            INNER JOIN foodwave_allowed_stores a ON a.IdStore = s.Id
            INNER JOIN foodwave f ON f.Id = a.IdFoodwave
            WHERE NOW() BETWEEN f.StartTime AND f.EndTime
            ";

        $stmt = $this->conn->prepare( $activewavequery );

        $stmt->execute();

        return $stmt;
    }

    function get_foodwave_products($store_id) {
        $productquery = "
            SELECT 
                p.Name AS product_name, 
                p.Price AS product_price,
                p.Id AS product_id
            FROM foodwave_product p
            INNER JOIN foodwave_store s ON s.Id = p.IdStore
            WHERE s.Id = ?
            ";

        $stmt = $this->conn->prepare( $productquery );

        $stmt->bindParam(1, $store_id);

        $stmt->execute();

        return $stmt;
    }
}
?>