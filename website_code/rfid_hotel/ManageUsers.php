<?php
session_start();
if (!isset($_SESSION['Admin-name'])) {
  header("location: login.php");
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Quản lý khách hàng</title>
  	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<link rel="icon" type="image/png" href="images/favicon.png">
	<link rel="stylesheet" type="text/css" href="css/manageusers.css">

	<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.3.1.js" 
	        integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" 
			crossorigin="anonymous">
	</script>
    <script type="text/javascript" src="js/bootbox.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.js"></script>
	<script src="js/manage_users.js"></script>
	<script type="text/javascript" src="js/search.js"></script>
	<script>
	  	$(window).on("load resize ", function() {
		    var scrollWidth = $('.tbl-content').width() - $('.tbl-content table').width();
		    $('.tbl-header').css({'padding-right':scrollWidth});
		}).resize();
	</script>
	<script>
	  $(document).ready(function(){
	  	  $.ajax({
	        url: "manage_users_up.php"
	        }).done(function(data) {
	        $('#manage_users').html(data);
	      });
	    setInterval(function(){
	      $.ajax({
	        url: "manage_users_up.php"
	        }).done(function(data) {
	        $('#manage_users').html(data);
	      });
	    },5000);
	  });
	</script>
</head>
<body>
<?php include'header.php';?>
<main>
	<div class="form-style-5 slideInDown animated">
		<form enctype="multipart/form-data">
			<div class="alert_user"></div>
			<fieldset>
				<legend><span class="number"></span> Thông tin khách hàng</legend>
				<input type="hidden" name="user_id" id="user_id">
				<input type="text" name="name" id="name" placeholder="Tên khách hàng..." autocomplete="off">
				<input type="number" name="number" id="number" placeholder="Số phòng...">
				<input type="number" name="cmnd_cccd" id="cmnd_cccd" placeholder="CMND/CCCD...">
				<input type="number" name="sdt" id="sdt" placeholder="Số điện thoại...">
			</fieldset>
			<fieldset>
			<label>
				<p>Giới tính:</p>
				<input type="radio" name="gender" class="gender" value="Nam" checked="checked"> Nam 
	          	<input type="radio" name="gender" class="gender" value="Nữ"> Nữ
				<input type="radio" name="gender" class="gender" value="Khác"> Khác
	      	</label >
			</fieldset>
			<button type="button" name="user_add" class="user_add">Thêm khách mới</button>
			<button type="button" name="user_upd" class="user_upd">Sửa thông tin khách</button>
			<button type="button" name="user_rmo" class="user_rmo">Xóa thông tin khách</button>
		</form>
	</div>

	<!--User table-->
	<div class="section">		
		<div class="slideInRight animated">
			<div id="manage_users"></div>
		</div>
	</div>
</main>
</body>
</html>
<!-- endd -->