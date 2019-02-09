<?php
class Person{
 
    // database connection and table name
    private $conn;
    private $table_name = "person";
 
    // object properties
    public $id;
    public $firstname;
    public $lastname;
    public $nickname;
    public $hasagreedtoprivacypolicy;
    public $haspaid;
    public $hasorderedticket;
    public $idticket;
    public $email;
    public $createdate;
    public $modifdate;
    public $teamname;
    public $iduser;
 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    // read person
    function read(){
        // select all query
        $query = "
        SELECT
            p.firstname, 
            p.id, 
            p.lastname, 
            p.nickname, 
            p.email, 
            p.hasorderedticket, 
            p.haspaid, 
            p.hasagreedtoprivacypolicy, 
            p.idticket, 
            t.name AS teamname,
            p.createdate, 
            p.modifdate
        FROM " . $this->table_name . " p
        LEFT JOIN teammembers tm ON tm.IdPersonMember = p.id
        LEFT JOIN team t ON tm.IdTeam = t.id
        ";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute query
        $stmt->execute();
    
        return $stmt;
    }

    // create person
    function create(){
 
    // query to insert record
        $query = "
        INSERT INTO " . $this->table_name . " 
        SET
            firstname=:firstname, 
            lastname=:lastname, 
            nickname=:nickname, 
            email=:email, 
            hasagreedtoprivacypolicy=:hasagreedtoprivacypolicy, 
            hasorderedticket=:hasorderedticket, 
            haspaid=:haspaid, 
            modifdate=:modifdate, 
            createdate=:createdate,
            iduser=:iduser
        ";
        
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        // prepare query
        $stmt = $this->conn->prepare($query);
        
        // sanitize
        $this->firstname=htmlspecialchars(strip_tags($this->firstname));
        $this->lastname=htmlspecialchars(strip_tags($this->lastname));
        $this->nickname=htmlspecialchars(strip_tags($this->nickname));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->hasagreedtoprivacypolicy=htmlspecialchars(strip_tags($this->hasagreedtoprivacypolicy));
        $this->hasorderedticket=htmlspecialchars(strip_tags($this->hasorderedticket));
        $this->haspaid=htmlspecialchars(strip_tags($this->haspaid));
        $this->modifdate=htmlspecialchars(strip_tags($this->modifdate));
        $this->createdate=htmlspecialchars(strip_tags($this->createdate));
        $this->iduser=htmlspecialchars(strip_tags($this->iduser));
    
        // bind values
        $stmt->bindParam(":firstname", $this->firstname);
        $stmt->bindParam(":lastname", $this->lastname);
        $stmt->bindParam(":nickname", $this->nickname);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":hasagreedtoprivacypolicy", $this->hasagreedtoprivacypolicy, PDO::PARAM_BOOL);
        $stmt->bindParam(":hasorderedticket", $this->hasorderedticket, PDO::PARAM_BOOL);
        $stmt->bindParam(":haspaid", $this->haspaid, PDO::PARAM_BOOL);
        $stmt->bindParam(":modifdate", $this->modifdate);
        $stmt->bindParam(":createdate", $this->createdate);
        $stmt->bindParam(":iduser", $this->iduser);
    
        // execute query
        if($stmt->execute()){
            return true;
        }
    
        
        return false;
     
    }

    // used when filling up the update person form
    function readOne(){
    
        // query to read single record
        $query = "
        SELECT
            p.firstname, 
            p.id, 
            p.lastname, 
            p.nickname, 
            p.email, 
            p.hasorderedticket, 
            p.haspaid, 
            p.hasagreedtoprivacypolicy, 
            p.idticket, 
            t.name AS teamname, 
            p.createdate, 
            p.modifdate
        FROM " . $this->table_name . " p
        LEFT JOIN teammembers tm ON tm.IdPersonMember = p.id
        LEFT JOIN team t ON tm.IdTeam = t.id
        WHERE 
            p.id = ?
        ";
    
        // prepare query statement
        $stmt = $this->conn->prepare( $query );
        
        // bind id of person to be updated
        $stmt->bindParam(1, $this->id);
    
        // execute query
        $stmt->execute();
    
        // get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // set values to object properties
        $this->firstname = $row['firstname'];
        $this->lastname = $row['lastname'];
        $this->nickname = $row['nickname'];
        $this->hasagreedtoprivacypolicy = $row['hasagreedtoprivacypolicy'];
        $this->hasorderedticket = $row['hasorderedticket'];
        $this->haspaid = $row['haspaid'];
        $this->idticket = $row['idticket'];
        $this->email = $row['email'];
        $this->modifdate = $row['modifdate'];
        $this->createdate = $row['createdate'];
    }

    // update the person
    function update(){
    
        // update query
        // query to insert record
        $query = "
        UPDATE " . $this->table_name . "
        SET
            firstname=:firstname, 
            lastname=:lastname, 
            nickname=:nickname, 
            email=:email, 
            hasagreedtoprivacypolicy=:hasagreedtoprivacypolicy, 
            hasorderedticket=:hasorderedticket, 
            haspaid=:haspaid, 
            modifdate=:modifdate,
            idticket=:idticket
        WHERE
            id = :id
        ";

        //$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->firstname=htmlspecialchars(strip_tags($this->firstname));
        $this->lastname=htmlspecialchars(strip_tags($this->lastname));
        $this->nickname=htmlspecialchars(strip_tags($this->nickname));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->hasagreedtoprivacypolicy=htmlspecialchars(strip_tags($this->hasagreedtoprivacypolicy));
        $this->hasorderedticket=htmlspecialchars(strip_tags($this->hasorderedticket));
        $this->haspaid=htmlspecialchars(strip_tags($this->haspaid));
        $this->modifdate=htmlspecialchars(strip_tags($this->modifdate));
        $this->idticket=htmlspecialchars(strip_tags($this->idticket));
        $this->id=htmlspecialchars(strip_tags($this->id));

        // bind values
        $stmt->bindParam(":firstname", $this->firstname);
        $stmt->bindParam(":lastname", $this->lastname);
        $stmt->bindParam(":nickname", $this->nickname);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":hasagreedtoprivacypolicy", $this->hasagreedtoprivacypolicy, PDO::PARAM_BOOL);
        $stmt->bindParam(":hasorderedticket", $this->hasorderedticket, PDO::PARAM_BOOL);
        $stmt->bindParam(":haspaid", $this->haspaid, PDO::PARAM_BOOL);
        $stmt->bindParam(":modifdate", $this->modifdate);
        $stmt->bindParam(":idticket", $this->idticket);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        // execute query
        if($stmt->execute()){
            return true;
        }

        return false;

    }

    // delete the person
    function delete(){
 
        // delete query
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->id=htmlspecialchars(strip_tags($this->id));
    
        // bind id of record to delete
        $stmt->bindParam(1, $this->id);
    
        // execute query
        if($stmt->execute()){
            return true;
        }
    
        return false;
     
    }

    // search persons
    function search($keywords){
 
        // select all query
        $query = "
        SELECT
            p.firstname, 
            p.id, 
            p.lastname, 
            p.nickname,
            p.email, 
            p.hasorderedticket, 
            p.haspaid, 
            p.hasagreedtoprivacypolicy, 
            p.idticket, 
            t.name AS teamname, 
            p.createdate, 
            p.modifdate
        FROM " . $this->table_name . " p
        LEFT JOIN teammembers tm ON tm.IdPersonMember = p.id
        LEFT JOIN team t ON tm.IdTeam = t.id
        WHERE
            p.firstname LIKE ? OR 
            p.lastname LIKE ? OR 
            p.nickname LIKE ? OR 
            p.nickname LIKE ? OR 
            t.name LIKE ?
        ORDER BY 
            p.createdate DESC
        ";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $keywords=htmlspecialchars(strip_tags($keywords));
        $keywords = "%{$keywords}%";
    
        // bind
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);
        $stmt->bindParam(4, $keywords);
        $stmt->bindParam(5, $keywords);
    
        // execute query
        $stmt->execute();
    
        return $stmt;
    }

    // read persons with pagination
    public function readPaging($from_record_num, $records_per_page){
    
        // select query
        $query = "
        SELECT
            p.firstname, 
            p.id, 
            p.lastname, 
            p.nickname, 
            p.email, 
            p.hasorderedticket, 
            p.haspaid, 
            p.hasagreedtoprivacypolicy, 
            p.idticket, 
            t.name AS teamname, 
            p.createdate, 
            p.modifdate
        FROM " . $this->table_name . " p
        LEFT JOIN teammembers tm ON tm.IdPersonMember = p.id
        LEFT JOIN team t ON tm.IdTeam = t.id
        ORDER BY
            p.createdate DESC
        LIMIT ?, ?
        ";
    
        // prepare query statement
        $stmt = $this->conn->prepare( $query );
    
        // bind variable values
        $stmt->bindParam(1, $from_record_num, PDO::PARAM_INT);
        $stmt->bindParam(2, $records_per_page, PDO::PARAM_INT);
    
        // execute query
        $stmt->execute();
    
        // return values from database
        return $stmt;
    }

    // used for paging persons
    public function count(){
        $query = "SELECT COUNT(*) as total_rows FROM " . $this->table_name . "";
    
        $stmt = $this->conn->prepare( $query );
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $row['total_rows'];
    }
}
?>