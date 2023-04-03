<?php
session_start();
if (!isset($_SESSION['Admin-name'])) {
  header("location: login.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Users</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/favicon.png">

    <script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="js/search.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <link rel="stylesheet" type="text/css" href="css/Users.css">
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
    <script>
      $(window).on("load resize ", function() {
        var scrollWidth = $('.tbl-content').width() - $('.tbl-content table').width();
        $('.tbl-header').css({'padding-right':scrollWidth});
    }).resize();
    </script>
    
</head>
<body>
<?php include'header.php'; ?> 
<main>
<section>
  <h1 class="slideInDown animated"><b>Danh sách tất cả khách hàng</b></h1>

  <div class="table-responsive slideInDown animated" style="max-height: 400px;"> 

    <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for names..">
    
    <table class="table">
      <thead class="table-primary">
        <tr>
          <th><b>Tên</b></th>
          <th><b>Số phòng</b></th>
          <th><b>CMND/CCCD</b></th>
          <th><b>Số điện thoại</b></th>
          <th><b>Giới tính</b></th>
          <th><b>Số UID thẻ</b></th>
          <th><b>Ngày nhận phòng</b></th>
        </tr>
      </thead>
      <tbody class="table-secondary" id="myTable">
        <?php
          //Connect to database
          require'connectDB.php';

            $sql = "SELECT * FROM users WHERE add_card=1 ORDER BY room_number ASC";
            $result = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($result, $sql)) {
                echo '<p class="error">SQL Error</p>';
            }
            else{
                mysqli_stmt_execute($result);
                $resultl = mysqli_stmt_get_result($result);
              if (mysqli_num_rows($resultl) > 0){
                  while ($row = mysqli_fetch_assoc($resultl)){
          ?>
                      <TR>
                      <TD><?php echo $row['username'];?></TD>
                      <TD><?php echo $row['room_number'];?></TD>
                      <TD><?php echo $row['CMND_CCCD'];?></TD>
                      <TD><?php echo $row['SDT'];?></TD>
                      <TD><?php echo $row['gender'];?></TD>
                      <TD><?php echo $row['card_uid'];?></TD>
                      <TD><?php echo $row['user_date'];?></TD>                   
                      </TR>
        <?php
                }   
            }
          }
        ?>
      </tbody>
    </table>
  </div>
</section>
</main>
</body>
</html>