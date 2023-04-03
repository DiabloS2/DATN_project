<?php 

if (isset($_POST['login'])) {

	require 'connectDB.php';

	$Usermail = $_POST['email']; 
	$Userpass = $_POST['pwd']; 

	if (empty($Usermail) || empty($Userpass) ) {
		header("location: login.php?error=emptyfields");
  		exit();
	}
	else if (!filter_var($Usermail,FILTER_VALIDATE_EMAIL)) {
          header("location: login.php?error=invalidEmail");
          exit();
    }
	else{
		$sql = "SELECT * FROM admin WHERE admin_email=?";
		$result = mysqli_stmt_init($conn);
		if (!mysqli_stmt_prepare($result, $sql)) {
			header("location: login.php?error=sqlerror");
  			exit();
		}
		else{
			mysqli_stmt_bind_param($result, "s", $Usermail);
			mysqli_stmt_execute($result);
			$resultl = mysqli_stmt_get_result($result);

			if ($row = mysqli_fetch_assoc($resultl)) {
				$pwdCheck = password_verify($Userpass, password_hash($row['admin_pwd'],PASSWORD_DEFAULT));
				if ($pwdCheck == false) {
					header("location: login.php?error=wrongpassword");
  					exit();
				}
				else if ($pwdCheck == true) {
	                session_start();
					$_SESSION['Admin-name'] = $row['admin_name']; //KHAI BAO BIEN Admin-name va Admin-email
					$_SESSION['Admin-email'] = $row['admin_email'];
					header("location: index.php?login=success");
					exit();
				}
			}
			else{
				header("location: login.php?error=nouser");
  				exit();
			}
		}
	}
mysqli_stmt_close($result);    
mysqli_close($conn);
}
else{
  header("location: login.php");
  exit();
}

?>