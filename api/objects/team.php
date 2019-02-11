<?php
class team{
 
    // database connection and table name
    private $conn;
    private $table_name = "team";
 
    // object properties
    public $id;
    public $idleader;
    public $name;
    public $createdate;
    public $modifdate;
    public $leadernickname;
    public $authid;
 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    // read team
    function read(){
        // select all query
        $query = "
            SELECT
                t.id,
                t.name,
                t.createdate,
                t.modifdate,
                p.nickname AS leadernickname,
                t.idpersonleader
            FROM
            " . $this->table_name . " t
                INNER JOIN person p ON p.Id = t.idpersonleader
            ";                   
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute query
        $stmt->execute();
    
        return $stmt;
    }

    // function getmembers($idteam){
    //      // select all query
    //      $query = "
    //         SELECT
    //             p.Id,
    //             p.NickName,
    //             p.FirstName,
    //             p.LastName
    //         FROM person p
    //         INNER JOIN teammembers tm ON tm.IdPersonMember = p.Id
    //         WHERE tm.IdTeam = ?";             
    
    //     // prepare query statement
    //     $stmt = $this->conn->prepare( $query );
        
    //     // bind id of team to be updated
    //     $stmt->bindParam(1, $idteam);
    
    //     // execute query
    //     $stmt->execute();
    
    //     // get retrieved row
    //     $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    //     // set values to object properties
    //     $this->members[] = $row;
    //     // $this->lastname = $row['lastname'];
    //     // $this->nickname = $row['nickname'];
    //     // $this->hasagreedtoprivacypolicy = $row['hasagreedtoprivacypolicy'];
    //     // $this->hasorderedticket = $row['hasorderedticket'];
    //     // $this->haspaid = $row['haspaid'];
    //     // $this->idticket = $row['idticket'];
    //     // $this->email = $row['email'];
    //     // $this->modifdate = $row['modifdate'];
    //     // $this->createdate = $row['createdate'];
    // }

    // create team
    function create(){
 
    // query to insert record
        $query = "
            INSERT INTO " . $this->table_name . " 
                SET
                name=:name, 
                IdPersonLeader=(SELECT p.Id FROM person p INNER JOIN users u ON u.Id = p.IdUser WHERE u.Id = :authid), 
                modifdate=:modifdate, 
                createdate=:createdate
            ";
        
        //$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        // prepare query
        $stmt = $this->conn->prepare($query);
        
        // sanitize
        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->authid=htmlspecialchars(strip_tags($this->authid));
        $this->modifdate=htmlspecialchars(strip_tags($this->modifdate));
        $this->createdate=htmlspecialchars(strip_tags($this->createdate));
    
        // bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":authid", $this->authid);
        $stmt->bindParam(":modifdate", $this->modifdate);
        $stmt->bindParam(":createdate", $this->createdate);
    
        // execute query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

    // used when filling up the update team form
    function readOne(){
    
        // query to read single record
        $query = "
        SELECT 
            t.Id, 
            t.Name, 
            t.CreateDate, 
            t.ModifDate,
            p.NickName
        FROM " . $this->table_name . " t
        INNER JOIN person p ON p.Id = t.IdPersonLeader
        WHERE p.IdUser = ?
        ";
    
        // prepare query statement
        $stmt = $this->conn->prepare( $query );
        
        // bind id of team to be updated
        $stmt->bindParam(1, $this->authid);
    
        // execute query
        $stmt->execute();
    
        // get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // set values to object properties
        $this->id = $row['Id'];
        $this->name = $row['Name'];
        $this->modifdate = $row['ModifDate'];
        $this->createdate = $row['ModifDate'];
        $this->leadernickname = $row['NickName'];
    }

    // update the team
    function update(){
    
        // update query
        // query to insert record
        $query = "UPDATE " . $this->table_name . "
                    SET
                    idpersonleader=:idleader, 
                    name=:name, 
                    modifdate=:modifdate
                    WHERE
                        idpersonleader = (SELECT p.Id FROM person p INNER JOIN users u ON u.Id = p.IdUser WHERE u.Id = :authid)";

        //$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->idleader=htmlspecialchars(strip_tags($this->idleader));
        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->modifdate=htmlspecialchars(strip_tags($this->modifdate));
        $this->authid=htmlspecialchars(strip_tags($this->authid));

        // bind values
        $stmt->bindParam(":idleader", $this->idleader);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":modifdate", $this->modifdate);
        $stmt->bindParam(":authid", $this->authid, PDO::PARAM_INT);

        // execute query
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    // delete the team
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

    // search teams
    function search($keywords){
 
        // select all query
        $query = "SELECT
        p.firstname, p.id, p.lastname, p.nickname, p.email, p.hasorderedticket, p.haspaid, p.hasagreedtoprivacypolicy, p.idticket, t.name AS team, p.createdate, p.modifdate
        FROM
        " . $this->table_name . " p
        LEFT JOIN teammembers tm ON tm.IdteamMember = p.id
        LEFT JOIN team t ON tm.IdTeam = t.id
        WHERE
                    p.firstname LIKE ? OR p.lastname LIKE ? OR p.nickname LIKE ? OR p.nickname LIKE ? OR t.name LIKE ?
                ORDER BY
                    p.createdate DESC";
    
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
}
?>