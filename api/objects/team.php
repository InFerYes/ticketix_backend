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
    public $isinvitationopen;
    public $ismember;
    public $idmember;
 
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
            p.NickName,
            t.IdPersonLeader AS idleader,
            tm.IsInvitationOpen,
            tm.IsMember
        FROM teammembers tm
        INNER JOIN team t ON t.Id = tm.IdTeam
        INNER JOIN person p ON p.Id = t.IdPersonLeader
        WHERE tm.IsMember = 1 
        AND tm.IsInvitationOpen = 0 
        AND tm.IdPersonMember = (SELECT p.id FROM person p WHERE p.iduser = ?)
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
        $this->idleader = $row['idleader'];
        $this->isinvitationopen = $row['IsInvitationOpen'];
        $this->ismember = $row['IsMember'];
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
    // function search($keywords){
 
    //     // select all query
    //     $query = "SELECT
    //     p.firstname, p.id, p.lastname, p.nickname, p.email, p.hasorderedticket, p.haspaid, p.hasagreedtoprivacypolicy, p.idticket, t.name AS team, p.createdate, p.modifdate
    //     FROM
    //     " . $this->table_name . " p
    //     LEFT JOIN teammembers tm ON tm.IdteamMember = p.id
    //     LEFT JOIN team t ON tm.IdTeam = t.id
    //     WHERE
    //                 p.firstname LIKE ? OR p.lastname LIKE ? OR p.nickname LIKE ? OR p.nickname LIKE ? OR t.name LIKE ?
    //             ORDER BY
    //                 p.createdate DESC";
    
    //     // prepare query statement
    //     $stmt = $this->conn->prepare($query);
    
    //     // sanitize
    //     $keywords=htmlspecialchars(strip_tags($keywords));
    //     $keywords = "%{$keywords}%";
    
    //     // bind
    //     $stmt->bindParam(1, $keywords);
    //     $stmt->bindParam(2, $keywords);
    //     $stmt->bindParam(3, $keywords);
    //     $stmt->bindParam(4, $keywords);
    //     $stmt->bindParam(5, $keywords);
    
    //     // execute query
    //     $stmt->execute();
    
    //     return $stmt;
    // }

    function check_invitations($authid){
        $query= "
        SELECT 
            t.name,
            t.id
        FROM teammembers tm 
        INNER JOIN person p ON p.Id = tm.IdPersonMember
        INNER JOIN " . $this->table_name . " t ON t.Id = tm.IdTeam
        WHERE p.IdUser = ? 
          AND tm.isinvitationopen = 1 
          AND tm.ismember = 0
        ";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $authid);

        $stmt->execute();
    
        return $stmt;
    }

    function accept_invitation($idteam, $authid){
        //is user leader of a team?
        $query="
        SELECT 
            t.Id 
        FROM team t 
        WHERE t.IdPersonLeader = (SELECT p.Id FROM person p WHERE p.IdUser = :authid)
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":authid", $authid);

        $stmt->execute();

        $num = $stmt->rowCount();

        //is this the team that has an open invitation?
        $querycorrectinvitation="
        SELECT 
            tm.Id 
        FROM teammembers tm 
        INNER JOIN person p ON p.Id = tm.IdPersonMember
        WHERE p.IdUser = :authid
          AND tm.IdTeam = :idteam
          AND tm.IsInvitationOpen = 1 
          AND tm.IsMember = 0
        ";

        $stmtci = $this->conn->prepare($querycorrectinvitation);
        $stmtci->bindParam(":authid", $authid);
        $stmtci->bindParam(":idteam", $idteam);

        $stmtci->execute();

        $numci = $stmtci->rowCount();

        if ($num==0 && $numci==1) {

            $query2="
            DELETE
            FROM teammembers
            WHERE IdPersonMember = (SELECT p.Id FROM person p WHERE p.IdUser = :authid)
              AND IsMember = 1
            ";

            $stmt2 = $this->conn->prepare($query2);
            $stmt2->bindParam(":authid", $authid);

            $stmt2->execute();

            if ($stmt2->rowCount() > 0) {
                $query3="
                    UPDATE teammembers tm 
                    INNER JOIN team t ON t.Id = tm.IdTeam
                    SET
                        IsInvitationOpen = 0,
                        IsMember = 1
                    WHERE tm.idpersonmember = (SELECT p.Id FROM person p WHERE p.IdUser = :authid)
                    AND tm.IdTeam = :idteam
                ";

                $stmt3 = $this->conn->prepare($query3);
                $stmt3->bindParam(":authid", $authid);
                $stmt3->bindParam(":idteam", $idteam);

                $stmt3->execute();

                if ($stmt3->rowCount() > 0) {
                    return true;
                }
            
                return false;
            }

            return false;
        }

        return false;
    }

    function decline_invitation($idteam, $authid){
        $query="
        DELETE FROM teammembers
        WHERE IdPersonMember = (SELECT p.Id FROM person p WHERE p.IdUser = :authid)
            AND IsInvitationOpen = 1 AND IsMember = 0 AND IdTeam = :idteam
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":authid", $authid);
        $stmt->bindParam(":idteam", $idteam);

        $stmt->execute();

        $num = $stmt->rowCount();

        if ($num>0) {

            return true;
        }

        return false;
    }

    function remove_teammember(){
        if ($this->user_is_leader($this->authid, $this->id)) {
            $query="
            DELETE FROM teammembers
            WHERE IdPersonMember = :userid
              AND IdTeam = :idteam
            ";
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":userid", $this->idmember);
            $stmt->bindParam(":idteam", $this->id);
    
            $stmt->execute();
    
            $num = $stmt->rowCount();
    
            if ($num>0) {
    
                return true;
            }
    
            return false;
        }

        return false;
    }

    function user_with_mail_exists($email){
        $query = "SELECT Id FROM person WHERE Email = :email";

        $stmt = $this->conn->prepare($query);
        //$stmt->bindParam(":authid", $this->authid);
        $stmt->bindParam(":email", $email);

        $stmt->execute();

        $num = $stmt->rowCount();

        if ($num>0) {
            return true;
        }

        return false;
    }

    function user_is_leader($authid, $idteam){
        $query = "
            SELECT 
                Id 
            FROM team 
            WHERE IdPersonLeader = (SELECT p.Id FROM person p WHERE p.IdUser = :authid) 
            AND Id = :idteam
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":authid", $authid);
        $stmt->bindParam(":idteam", $idteam);

        $stmt->execute();

        $num = $stmt->rowCount();

        if ($num>0) {
            return true;
        }

        return false;
    }

    function create_invitation(){
        if ($this->user_is_leader($this->authid, $this->id)) {
            //maybe fix leader can't invite himself

            $query = "
                INSERT INTO teammembers 
                (IdPersonMember, IdTeam, IsMember, IsInvitationOpen) 
                VALUES 
                ((SELECT p.Id FROM person p WHERE p.IdUser = :authid),:id,0,1)
            ";
    
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":authid", $this->authid);
            $stmt->bindParam(":id", $this->id);
    
            // execute query
            if($stmt->execute()){
                return true;
            }
        
            return false;
        }

        return false;
        
    }

    function send_invitation_mail() {
        echo "Please open your mail client lol";
    }
}
?>