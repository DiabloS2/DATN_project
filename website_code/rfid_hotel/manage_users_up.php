<div class="table-responsive-sm" style="max-height: 870px;"> 

  <table class="table">
    <thead class="table-primary">
      <tr>
        <th><b>số id thẻ</b></th>
        <th><b>tên</b></th>
        <th><b>cmnd/cccd</b></th>
        <th><b>số điện thoại</b></th>
        <th><b>giới tính</b></th>
        <th><b>số phòng</b></th>
        <th><b>ngày nhận phòng</b></th>
      </tr>
    </thead>

    <tbody class="table-secondary">
    <?php
      //Connect to database
      require'connectDB.php';

        $sql = "SELECT * FROM users ORDER BY id DESC";
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
                  <tr>
                  	<td><?php  
                        $card_uid = $row['card_uid'];
                    	?>
                    	<form>
                    		<button type="button" class="select_btn" id="<?php echo $card_uid;?>" title="Chọn thẻ này"><?php echo $card_uid;?></button>
                    	</form>
                    </td>
                    <td><?php echo $row['username'];?></td>
                    <td><?php echo $row['CMND_CCCD'];?></td>
                    <td><?php echo $row['SDT'];?></td>
                    <td><?php echo $row['gender'];?></td>
                    <td><?php
                    echo $row['room_number'];
                    if ($row['card_select'] == 1) {
                      echo "<span><i class='glyphicon glyphicon-search' title='Thẻ lặp lại'></i></span>";
                    }
                    ?>
                    </td> 
                    <td><?php echo $row['user_date'];?></td>               
                  </tr>
    <?php
            }   
        }
      }
    ?>
    </tbody>
  </table>
</div>