<?php  
//Connect to database
require 'connectDB.php';
date_default_timezone_set('Asia/Ho_Chi_Minh');
$d = date("d-m-Y");
$t = date("H:i:sa");

//Send data (card UID) to the door lock of specific room
//First time server will receive room number (isset($_POST['ID']))from door lock board
//Then server wil echo the result (card_uid) to door lock board
if (isset($_POST['ID'])){
    $q5 = $_POST['ID'];
    $sql5 = "SELECT * FROM users WHERE room_number= $q5;";
    $result5 = mysqli_query($conn, $sql5);
	$row5  = mysqli_fetch_assoc($result5);
    echo $row5['card_uid'];
    mysqli_free_result($result5);
    exit();
} 

//Receive data (card UID) from sender_board
//Sender_board is the board use to create card when new customer come to the hotel
if (isset($_GET['card_uid'])) {
       
    $card_uid = $_GET['card_uid'];
    //New Card has been added
    $sql = "SELECT * FROM users WHERE card_uid=?";
    $result = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($result, $sql)) {
        echo "SQL_Error_Select_card";
        exit();
    }
    else{
        mysqli_stmt_bind_param($result, "s", $card_uid);
        mysqli_stmt_execute($result);
        $resultl = mysqli_stmt_get_result($result);
        //The situation if the RFID card is available
        if ($row = mysqli_fetch_assoc($resultl)){
            $sql = "SELECT card_select FROM users WHERE card_select=1";
            $result = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($result, $sql)) {
                echo "SQL_Error_Select";
                exit();
            }
            else{
                mysqli_stmt_execute($result);
                $resultl = mysqli_stmt_get_result($result);                            
                if ($row = mysqli_fetch_assoc($resultl)) {
                    $sql="UPDATE users SET card_select=0";
                    $result = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($result, $sql)) {
                        echo "SQL_Error_insert";
                        exit();
                    }
                    else{
                        mysqli_stmt_execute($result);
                        $sql="UPDATE users SET card_select=1 WHERE card_uid=?";
                        $result = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($result, $sql)) {
                            echo "SQL_Error_insert_An_available_card";
                            exit();
                        }
                        else{
                            mysqli_stmt_bind_param($result, "s", $card_uid);
                            mysqli_stmt_execute($result);
                            echo "available";
                            exit();
                        }
                    }
                }
                else{
                    $sql="UPDATE users SET card_select=1 WHERE card_uid=?";
                    $result = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($result, $sql)) {
                        echo "SQL_Error_insert_An_available_card";
                        exit();
                    }
                    else{
                        mysqli_stmt_bind_param($result, "s", $card_uid);
                        mysqli_stmt_execute($result);
                        echo "available";
                        exit();
                    }
                }
            }
        }
        //The situation if the RFID card is new, then add card to user table
        else{
            $sql="UPDATE users SET card_select=0";
            $result = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($result, $sql)) {
                echo "SQL_Error_insert";
                exit();
            }
            else{
                mysqli_stmt_execute($result);
                //Insert value to database
                $sql = "INSERT INTO users (card_uid, card_select, user_date) VALUES (?, 1, CURDATE())";
                $result = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($result, $sql)) {
                    echo "SQL_Error_Select_add";
                    exit();
                }
                else{
                    mysqli_stmt_bind_param($result, "s", $card_uid,);
                    mysqli_stmt_execute($result);
                    echo "Card add succesful";
                    exit();
                }
            }
        }
    }                 
}
else {
    echo "Fail to receive UID";
    exit();
}
?>