<?php
class Actions{
    private $conn;
    function __construct(){
        require_once(realpath(__DIR__.'/../db-connect.php'));
        $this->conn = $conn;
    }
    /**
     * Class Actions
     */
    public function save_mdt(){
        foreach($_POST as $k => $v){
            if(!is_array($_POST[$k]) && !is_numeric($_POST[$k]) && !empty($_POST[$k])){
                $_POST[$k] = addslashes(htmlspecialchars($v));
            }
        }
        extract($_POST);

        if(!empty($id)){
            $check = $this->conn->query("SELECT id FROM `mdt_tbl` where `name` = '{$name}' and `id` != '{$id}' ");
            $sql = "UPDATE `mdt_tbl` set `name` = '{$name}' where `id` = '{$id}'";
        }else{
            
            $check = $this->conn->query("SELECT id FROM `mdt_tbl` where `name` = '{$name}' ");
            $sql = "INSERT `mdt_tbl` set `name` = '{$name}'";
        }
        if($check->num_rows > 0){
            return ['status' => 'error', 'msg' => 'Module Team Name Already Exists!'];
        }else{
            $qry = $this->conn->query($sql);
            if($qry){
                if(empty($id)){
                    $_SESSION['flashdata'] = [ 'type' => 'success', 'msg' => "New Module Team has been added successfully!" ];
                }else{
                    $_SESSION['flashdata'] = [ 'type' => 'success', 'msg' => "Module Team Data has been updated successfully!" ];
                }
                return [ 'status' => 'success'];
            }else{
                if(empty($id)){
                    return ['status' => 'error', 'msg' => 'An error occurred while saving the New Module Team!'];
                }else{
                    return ['status' => 'error', 'msg' => 'An error occurred while updating the Module Team Data!'];
                }
            }    
        }
          
    }
    public function delete_mdt(){
        extract($_POST);
        $delete = $this->conn->query("DELETE FROM `mdt_tbl` where `id` = '{$id}'");
        if($delete){
            $_SESSION['flashdata'] = [ 'type' => 'success', 'msg' => "Module Team has been deleted successfully!" ];
            return [ "status" => "success" ];
        }else{
            $_SESSION['flashdata'] = [ 'type' => 'danger', 'msg' => "Module Team has failed to deleted due to unknown reason!" ];
            return [ "status" => "error", "Module Team has failed to deleted!" ];
        }
    }
    public function list_mdt(){
        $sql = "SELECT * FROM `mdt_tbl` order by `name` ASC";
        $qry = $this->conn->query($sql);
        return $qry->fetch_all(MYSQLI_ASSOC);
    }
    public function get_mdt($id=""){
        $sql = "SELECT * FROM `mdt_tbl` where `id` = '{$id}'";
        $qry = $this->conn->query($sql);
        $result = $qry->fetch_assoc();
        return $result;
    }
    /**
     * member Actions
     */
    
     public function save_member(){
        foreach($_POST as $k => $v){
            if(!is_array($_POST[$k]) && !is_numeric($_POST[$k]) && !empty($_POST[$k])){
                $_POST[$k] = addslashes(htmlspecialchars($v));
            }
        }
        extract($_POST);

        if(!empty($id)){
            $check = $this->conn->query("SELECT id FROM `members_tbl` where `name` = '{$name}' and `mdt_id` = '{$mdt_id}' and `id` != '{$id}' ");
            $sql = "UPDATE `members_tbl` set `name` = '{$name}', `mdt_id` = '{$mdt_id}' where `id` = '{$id}'";
        }else{
            
            $check = $this->conn->query("SELECT id FROM `members_tbl` where `name` = '{$name}' and `mdt_id` = '{$mdt_id}' ");
            $sql = "INSERT `members_tbl` set `name` = '{$name}', `mdt_id` = '{$mdt_id}'";
        }
        if($check->num_rows > 0){
            return ['status' => 'error', 'msg' => 'member Name Already Exists!'];
        }else{
            $qry = $this->conn->query($sql);
            if($qry){
                if(empty($id)){
                    $_SESSION['flashdata'] = [ 'type' => 'success', 'msg' => "New member has been added successfully!" ];
                }else{
                    $_SESSION['flashdata'] = [ 'type' => 'success', 'msg' => "member Data has been updated successfully!" ];
                }
                return [ 'status' => 'success'];
            }else{
                if(empty($id)){
                    return ['status' => 'error', 'msg' => 'An error occurred while saving the New Module Team!'];
                }else{
                    return ['status' => 'error', 'msg' => 'An error occurred while updating the member Data!'];
                }
            }
        }
        
    }
    public function delete_member(){
        extract($_POST);
        $delete = $this->conn->query("DELETE FROM `members_tbl` where `id` = '{$id}'");
        if($delete){
            $_SESSION['flashdata'] = [ 'type' => 'success', 'msg' => "member has been deleted successfully!" ];
            return [ "status" => "success" ];
        }else{
            $_SESSION['flashdata'] = [ 'type' => 'danger', 'msg' => "member has failed to deleted due to unknown reason!" ];
            return [ "status" => "error", "member has failed to deleted!" ];
        }
    }
    public function list_member(){
        $sql = "SELECT `members_tbl`.*, `mdt_tbl`.`name` as `mdt` FROM `members_tbl` inner join `mdt_tbl` on `members_tbl`.`mdt_id` = `mdt_tbl`.`id` order by `members_tbl`.`name` ASC";
        $qry = $this->conn->query($sql);
        return $qry->fetch_all(MYSQLI_ASSOC);
    }
    public function get_member($id=""){
        $sql = "SELECT `members_tbl`.*, `mdt_tbl`.`name` as `mdt` FROM `members_tbl` inner join `mdt_tbl` on `members_tbl`.`mdt_id` = `mdt_tbl`.`id` where `members_tbl`.`id` = '{$id}'";
        $qry = $this->conn->query($sql);
        $result = $qry->fetch_assoc();
        return $result;
    }

    public function attendancemembers($mdt_id = "", $meeting_date = ""){
        if(empty($mdt_id) || empty($meeting_date))
            return [];
        
        // Modified SQL query to include the department name
        $sql = "SELECT 
                    members_tbl.*, 
                    COALESCE((SELECT `status` FROM `attendance_tbl` WHERE `member_id` = `members_tbl`.id AND `meeting_date` = '{$meeting_date}'), 0) AS `status`,
                    dept_tbl.name AS `dept` -- Include department name
                FROM 
                    members_tbl
                JOIN 
                    dept_tbl ON members_tbl.dept_id = dept_tbl.id -- Join with dept_tbl table
                WHERE 
                    members_tbl.mdt_id = '{$mdt_id}' 
                ORDER BY 
                    members_tbl.name ASC";
        
        $qry = $this->conn->query($sql);
        $result = $qry->fetch_all(MYSQLI_ASSOC);
        return $result;
    }
    
    public function attendancemembersMonthly($mdt_id = "", $mdt_month = ""){
        if(empty($mdt_id) || empty($mdt_month))
            return [];
        $sql = "SELECT `members_tbl`.* FROM `members_tbl` where `mdt_id` = '{$mdt_id}' order by `name` ASC";
        $qry = $this->conn->query($sql);
        $result = $qry->fetch_all(MYSQLI_ASSOC);
        foreach($result as $k => $row){
            $att_sql = "SELECT `status`, `meeting_date` FROM `attendance_tbl` where `member_id` = '{$row['id']}' ";
            $att_qry = $this->conn->query($att_sql);
            foreach($att_qry as $att_row){
                $result[$k]['attendance'][$att_row['meeting_date']] = $att_row['status'];
            }
        }
        return $result;
    }
    public function save_attendance(){
        extract($_POST);

        $sql_values = "";
        $errors = "";
        foreach($member_id as $k => $sid){
            $stat = $status[$k] ?? 3;

            $check = $this->conn->query("SELECT id FROM `attendance_tbl` where `member_id` = '{$sid}' and `meeting_date` = '{$meeting_date}'");
            if($check->num_rows > 0){
                
                $result = $check->fetch_assoc();
                $att_id = $result['id'];

                try{
                    $update = $this->conn->query("UPDATE `attendance_tbl` set `status` = '{$stat}' where `id` = '{$att_id}'");

                }catch(Exception $e){
                    if(!empty($errors)) $errors .= "<br>";
                    $errors .= $e->getMessage();
                }
               
            }else{
                if(!empty($sql_values)) $sql_values .= ", ";
                $sql_values .= "( '{$sid}', '{$meeting_date}', '{$stat}' )";
            }
        }
        if(!empty($sql_values))
        {
            try{
                $sql =  $this->conn->query("INSERT INTO `attendance_tbl` ( `member_id`, `meeting_date`, `status` ) VALUES {$sql_values}");
            }catch(Exception $e){
                if(!empty($errors)) $errors .= "<br>";
                $errors .= $e->getMessage();
            }
        }
        if(empty($errors)){
            $resp['status'] = "success";
            $_SESSION['flashdata'] = [ "type" => "success", "msg" => "Module Team Attendance Data has been saved successfully." ];
        }else{
            $resp['status'] = "error";
            $resp['msg'] = $errors;
        }

        return $resp;
    }
    
/**
 * ------------------------------guest Actions------------------------------
 */

 public function save_guest(){
    foreach($_POST as $k => $v){
        if(!is_array($_POST[$k]) && !is_numeric($_POST[$k]) && !empty($_POST[$k])){
            $_POST[$k] = addslashes(htmlspecialchars($v));
        }
    }
    extract($_POST);

    if(!empty($id)){
        $check = $this->conn->query("SELECT id FROM `guests_tbl` where `name` = '{$name}' and `dept_id` = '{$dept_id}' and `id` != '{$id}' ");
        $sql = "UPDATE `guests_tbl` set `name` = '{$name}', `dept_id` = '{$dept_id}' where `id` = '{$id}'";
    }else{
        
        $check = $this->conn->query("SELECT id FROM `guests_tbl` where `name` = '{$name}' and `dept_id` = '{$dept_id}' ");
        $sql = "INSERT `guests_tbl` set `name` = '{$name}', `dept_id` = '{$dept_id}'";
    }
    if($check->num_rows > 0){
        return ['status' => 'error', 'msg' => 'guest Name Already Exists!'];
    }else{
        $qry = $this->conn->query($sql);
        if($qry){
            if(empty($id)){
                $_SESSION['flashdata'] = [ 'type' => 'success', 'msg' => "New guest has been added successfully!" ];
            }else{
                $_SESSION['flashdata'] = [ 'type' => 'success', 'msg' => "guest Data has been updated successfully!" ];
            }
            return [ 'status' => 'success'];
        }else{
            if(empty($id)){
                return ['status' => 'error', 'msg' => 'An error occurred while saving the New Department!'];
            }else{
                return ['status' => 'error', 'msg' => 'An error occurred while updating the guest Data!'];
            }
        }
    }
    
}
    public function delete_guest(){
        extract($_POST);
        $delete = $this->conn->query("DELETE FROM `guests_tbl` where `id` = '{$id}'");
        if($delete){
            $_SESSION['flashdata'] = [ 'type' => 'success', 'msg' => "guest has been deleted successfully!" ];
            return [ "status" => "success" ];
        }else{
            $_SESSION['flashdata'] = [ 'type' => 'danger', 'msg' => "guest has failed to deleted due to unknown reason!" ];
            return [ "status" => "error", "guest has failed to deleted!" ];
        }
    }
    public function list_guest(){
        $sql = "SELECT `guests_tbl`.*, `dept_tbl`.`name` as `dept` FROM `guests_tbl` inner join `dept_tbl` on `guests_tbl`.`dept_id` = `dept_tbl`.`id` order by `guests_tbl`.`name` ASC";
        $qry = $this->conn->query($sql);
        return $qry->fetch_all(MYSQLI_ASSOC);
    }
    public function get_guest($id=""){
        $sql = "SELECT `guests_tbl`.*, `dept_tbl`.`name` as `dept` FROM `guests_tbl` inner join `dept_tbl` on `guests_tbl`.`dept_id` = `dept_tbl`.`id` where `guests_tbl`.`id` = '{$id}'";
        $qry = $this->conn->query($sql);
        $result = $qry->fetch_assoc();
        return $result;
    // }
    // public function attendanceguests($mdt_id = "", $meeting_date = ""){
    //     if(empty($mdt_id) || empty($meeting_date))
    //         return [];
    //     $sql = "SELECT `guests_tbl`.*, COALESCE((SELECT `status` FROM `attendance_tbl` where `guest_id` = `guests_tbl`.id and `meeting_date` = '{$meeting_date}' ), 0) as `status` FROM `guests_tbl` where `mdt_id` = '{$mdt_id}' order by `name` ASC";
    //     $qry = $this->conn->query($sql);
    //     $result = $qry->fetch_all(MYSQLI_ASSOC);
    //     return $result;
    // }

    // public function attendanceguestsMonthly($mdt_id = "", $mdt_month = ""){
    //     if(empty($mdt_id) || empty($mdt_month))
    //         return [];
    //     $sql = "SELECT `guests_tbl`.* FROM `guests_tbl` where `mdt_id` = '{$mdt_id}' order by `name` ASC";
    //     $qry = $this->conn->query($sql);
    //     $result = $qry->fetch_all(MYSQLI_ASSOC);
    //     foreach($result as $k => $row){
    //         $att_sql = "SELECT `status`, `meeting_date` FROM `attendance_tbl` where `guest_id` = '{$row['id']}' ";
    //         $att_qry = $this->conn->query($att_sql);
    //         foreach($att_qry as $att_row){
    //             $result[$k]['attendance'][$att_row['meeting_date']] = $att_row['status'];
    //         }
    //     }
    //     return $result;
    // }
    // public function save_guests_attendance(){
    //     extract($_POST);

    //     $sql_values = "";
    //     $errors = "";
    //     foreach($guest_id as $k => $sid){
    //         $stat = $status[$k] ?? 3;

    //         $check = $this->conn->query("SELECT id FROM `attendance_tbl` where `guest_id` = '{$sid}' and `meeting_date` = '{$meeting_date}'");
    //         if($check->num_rows > 0){
                
    //             $result = $check->fetch_assoc();
    //             $att_id = $result['id'];

    //             try{
    //                 $update = $this->conn->query("UPDATE `attendance_tbl` set `status` = '{$stat}' where `id` = '{$att_id}'");

    //             }catch(Exception $e){
    //                 if(!empty($errors)) $errors .= "<br>";
    //                 $errors .= $e->getMessage();
    //             }
            
    //         }else{
    //             if(!empty($sql_values)) $sql_values .= ", ";
    //             $sql_values .= "( '{$sid}', '{$meeting_date}', '{$stat}' )";
    //         }
    //     }
    //     if(!empty($sql_values))
    //     {
    //         try{
    //             $sql =  $this->conn->query("INSERT INTO `attendance_tbl` ( `guest_id`, `meeting_date`, `status` ) VALUES {$sql_values}");
    //         }catch(Exception $e){
    //             if(!empty($errors)) $errors .= "<br>";
    //             $errors .= $e->getMessage();
    //         }
    //     }
    //     if(empty($errors)){
    //         $resp['status'] = "success";
    //         $_SESSION['flashdata'] = [ "type" => "success", "msg" => "Module Team Attendance Data has been saved successfully." ];
    //     }else{
    //         $resp['status'] = "error";
    //         $resp['msg'] = $errors;
    //     }

    //     return $resp;
    }

// Department
    public function save_dept(){
        foreach($_POST as $k => $v){
            if(!is_array($_POST[$k]) && !is_numeric($_POST[$k]) && !empty($_POST[$k])){
                $_POST[$k] = addslashes(htmlspecialchars($v));
            }
        }
        extract($_POST);

        if(!empty($id)){
            $check = $this->conn->query("SELECT id FROM `dept_tbl` where `name` = '{$name}' and `id` != '{$id}' ");
            $sql = "UPDATE `dept_tbl` set `name` = '{$name}' where `id` = '{$id}'";
        }else{
            
            $check = $this->conn->query("SELECT id FROM `dept_tbl` where `name` = '{$name}' ");
            $sql = "INSERT `dept_tbl` set `name` = '{$name}'";
        }
        if($check->num_rows > 0){
            return ['status' => 'error', 'msg' => 'Department Name Already Exists!'];
        }else{
            $qry = $this->conn->query($sql);
            if($qry){
                if(empty($id)){
                    $_SESSION['flashdata'] = [ 'type' => 'success', 'msg' => "New Department has been added successfully!" ];
                }else{
                    $_SESSION['flashdata'] = [ 'type' => 'success', 'msg' => "Department Data has been updated successfully!" ];
                }
                return [ 'status' => 'success'];
            }else{
                if(empty($id)){
                    return ['status' => 'error', 'msg' => 'An error occurred while saving the New Department!'];
                }else{
                    return ['status' => 'error', 'msg' => 'An error occurred while updating the Department Data!'];
                }
            }    
        }
    }

    public function list_dept(){
        $sql = "SELECT * FROM `dept_tbl` order by `name` ASC";
        $qry = $this->conn->query($sql);
        return $qry->fetch_all(MYSQLI_ASSOC);
    }
    public function get_dept($id=""){
        $sql = "SELECT * FROM `dept_tbl` where `id` = '{$id}'";
        $qry = $this->conn->query($sql);
        $result = $qry->fetch_assoc();
        return $result;
    }

// dept end

    function __destruct()
    {
        if($this->conn)
        $this->conn->close(); 
    }
}