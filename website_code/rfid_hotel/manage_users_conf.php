<?php  
//Connect to database
require'connectDB.php';

//Add customer
if (isset($_POST['Add'])) {
     
    $user_id = $_POST['user_id'];    //ID
    $Uname = $_POST['name'];         //Name of customer
    $Number = $_POST['number'];      //Hotel room number
    $CMNDCCCD = $_POST['cmnd_cccd']; //Identity card
    $SoDT = $_POST['sdt'];           //Telephone number
    $Gender = $_POST['gender'];      //Gender
    
    //Check if there any selected customer
    $sql = "SELECT add_card FROM users WHERE id=?";
    $result = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($result, $sql)) {
      echo "SQL_Error";
      exit();
    }
    else{
        mysqli_stmt_bind_param($result, "i", $user_id);
        mysqli_stmt_execute($result);
        $resultl = mysqli_stmt_get_result($result);
        if ($row = mysqli_fetch_assoc($resultl)) {

            if ($row['add_card'] == 0) {

                if (!empty($Uname) && !empty($Number) && !empty($CMNDCCCD) && !empty($SoDT)) {
                    //Check if there any customer had already the Room Number
                    $sql = "SELECT room_number FROM users WHERE room_number=? AND id NOT like ?";
                    $result = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($result, $sql)) {
                        echo "SQL_Error";
                        exit();
                    }
                    else{
                        mysqli_stmt_bind_param($result, "ii", $Number, $user_id);
                        mysqli_stmt_execute($result);
                        $resultl = mysqli_stmt_get_result($result);
                        if (!$row = mysqli_fetch_assoc($resultl)) {
                            $sql="UPDATE users SET username=?, room_number=?, gender=?, SDT=?, CMND_CCCD=?, user_date=NOW(), add_card=1 WHERE id=?";
                            $result = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($result, $sql)) {
                                echo "SQL_Error";
                                exit();
                            }
                            else{
                                mysqli_stmt_bind_param($result, "sisssi", $Uname, $Number, $Gender, $SoDT, $CMNDCCCD, $user_id );
                                mysqli_stmt_execute($result);
                                echo 1;
                                exit();
                            }
                        }
                        else {
                            echo "Phòng đã cho thuê!";
                            exit();
                        }
                    }
                }
                else{
                    echo "Không để trống";
                    exit();
                }
            }
            else{
                echo "Khách hàng đã có sẵn";
                exit();
            }    
        }
        else {
            echo "Chưa chọn thẻ khách hàng !";
            exit();
        }
    }
}

//Update an exist customer
if (isset($_POST['Update'])) {

    $user_id = $_POST['user_id'];
    $Uname = $_POST['name'];
    $Number = $_POST['number'];
    $CMNDCCCD = $_POST['cmnd_cccd'];
    $SoDT = $_POST['sdt'];
    $Gender = $_POST['gender'];
    
    //Check if there any selected customer
    $sql = "SELECT add_card FROM users WHERE id=?";
    $result = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($result, $sql)) {
      echo "SQL_Error";
      exit();
    }
    else{
        mysqli_stmt_bind_param($result, "i", $user_id);
        mysqli_stmt_execute($result);
        $resultl = mysqli_stmt_get_result($result);
        if ($row = mysqli_fetch_assoc($resultl)) {

            if ($row['add_card'] == 0) {
                echo "Đầu tiên bạn cần thêm khách hàng!";
                exit();
            }            
            else{
                if (empty($Uname) && empty($Number) && empty($CMNDCCCD) && empty($SoDT)) {
                    echo "không để trống";
                    exit();
                }
                else{
                    //Check if there any user had already the Serial Number
                    $sql = "SELECT room_number FROM users WHERE room_number=? AND id NOT like ?";
                    $result = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($result, $sql)) {
                        echo "SQL_Error";
                        exit();
                    }
                    else{
                        mysqli_stmt_bind_param($result, "ii", $Number, $user_id);
                        mysqli_stmt_execute($result);
                        $resultl = mysqli_stmt_get_result($result);
                        if (!$row = mysqli_fetch_assoc($resultl)) {                                    
                            if (!empty($Uname) && !empty($CMNDCCCD && !empty($SoDT))) {
                                $sql="UPDATE users SET username=?, room_number=?, gender=?, SDT=?, CMND_CCCD=? WHERE id=?";
                                $result = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($result, $sql)) {
                                    echo "SQL_Error_select_Card";
                                    exit();
                                }
                                else{
                                    mysqli_stmt_bind_param($result, "sisssi", $Uname, $Number, $Gender, $SoDT, $CMNDCCCD, $user_id );
                                    mysqli_stmt_execute($result);
                                    echo 1;
                                    exit();
                                }
                            }
                        }
                        else {
                            echo "Phòng đã cho thuê!";
                            exit();
                        }
                    }
                }
            }    
        }
        else {
            echo "Chưa chọn thẻ khách hàng để cập nhật!";
            exit();
        }
    }
}

//Select customer and show the info of that customer
if (isset($_GET['select'])) {

    $card_uid = $_GET['card_uid'];

    $sql = "SELECT * FROM users WHERE card_uid=?";
    $result = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($result, $sql)) {
        echo "SQL_Error_Select";
        exit();
    }
    else{
        mysqli_stmt_bind_param($result, "s", $card_uid);
        mysqli_stmt_execute($result);
        $resultl = mysqli_stmt_get_result($result);
        // echo "User Fingerprint selected";
        // exit();
        header('Content-Type: application/json');
        $data = array();
        if ($row = mysqli_fetch_assoc($resultl)) {
            foreach ($resultl as $row) {
                $data[] = $row;
            }
        }
        $resultl->close();
        $conn->close();
        print json_encode($data);
    } 
}

//Delete customer
if (isset($_POST['delete'])) {

    $user_id = $_POST['user_id'];

    if (empty($user_id)) {
        echo "Chưa chọn thẻ khách hàng để xóa";
        exit();
    } else {
        $sql = "DELETE FROM users WHERE id=?";
        $result = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($result, $sql)) {
            echo "SQL_Error_delete";
            exit();
        }
        else{
            mysqli_stmt_bind_param($result, "i", $user_id);
            mysqli_stmt_execute($result);
            echo 1;
            exit();
        }
    }
}
?>