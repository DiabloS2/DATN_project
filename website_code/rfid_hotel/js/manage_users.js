$(document).ready(function(){
  //Add new customer
  $(document).on('click', '.user_add', function(){
   
    var user_id = $('#user_id').val();
    var name = $('#name').val();
    var number = $('#number').val();
    var sdt = $('#sdt').val();
    var cmnd_cccd = $('#cmnd_cccd').val();  
    var gender = $(".gender:checked").val();
    
    $.ajax({
      url: 'manage_users_conf.php',
      type: 'POST',
      data: {
        'Add': 1,
        'user_id': user_id,
        'name': name,
        'number': number,
        'cmnd_cccd': cmnd_cccd,
        'sdt': sdt,
        'gender': gender,
      },
      success: function(response){

        if (response == 1) {
          $('#user_id').val('');
          $('#name').val('');
          $('#number').val('');
          $('#cmnd_cccd').val('');
          $('#sdt').val('');
          $('.alert_user').fadeIn(500);
          $('.alert_user').html('<p class="alert alert-success">Khách hàng đã được thêm thành công!</p>');
        }
        else{
          $('.alert_user').fadeIn(500);
          $('.alert_user').html('<p class="alert alert-danger">'+ response + '</p>');
        }

        setTimeout(function () {
            $('.alert').fadeOut(500);
        }, 5000);
        
        $.ajax({
          url: "manage_users_up.php"
          }).done(function(data) {
          $('#manage_users').html(data);
        });
      }
    });
  });

  //Update infomation for exist customer
  $(document).on('click', '.user_upd', function(){
    
    var user_id = $('#user_id').val();
    var name = $('#name').val();
    var number = $('#number').val();
    var cmnd_cccd = $('#cmnd_cccd').val();
    var sdt = $('#sdt').val();   
    var gender = $(".gender:checked").val();
   
    $.ajax({
      url: 'manage_users_conf.php',
      type: 'POST',
      data: {
        'Update': 1,
        'user_id': user_id,
        'name': name,
        'number': number,
        'cmnd_cccd': cmnd_cccd,
        'sdt': sdt,
        'gender': gender,
      },
      success: function(response){

        if (response == 1) {
          $('#user_id').val('');
          $('#name').val('');
          $('#number').val('');
          $('#cmnd_cccd').val('');
          $('#sdt').val('');
          $('.alert_user').fadeIn(500);
          $('.alert_user').html('<p class="alert alert-success">Khách hàng đã được cập nhật!</p>');
        }
        else{
          $('.alert_user').fadeIn(500);
          $('.alert_user').html('<p class="alert alert-danger">'+ response + '</p>');
        }
        
        setTimeout(function () {
            $('.alert').fadeOut(500);
        }, 5000);
        
        $.ajax({
          url: "manage_users_up.php"
          }).done(function(data) {
          $('#manage_users').html(data);
        });
      }
    });   
  });

  //Delete exist customer
  $(document).on('click', '.user_rmo', function(){

    var user_id = $('#user_id').val();

    bootbox.confirm("Bạn có muốn khóa Khách hàng này?", function(result) {
      if(result){
        $.ajax({
          url: 'manage_users_conf.php',
          type: 'POST',
          data: {
            'delete': 1,
            'user_id': user_id,
          },
          success: function(response){

            if (response == 1) {
              $('#user_id').val('');
              $('#name').val('');
              $('#number').val('');
              $('#cmnd_cccd').val('');
              $('#sdt').val('');
              $('.alert_user').fadeIn(500);
              $('.alert_user').html('<p class="alert alert-success">Đã xóa khách hàng!</p>');
            }
            else{
              $('.alert_user').fadeIn(500);
              $('.alert_user').html('<p class="alert alert-danger">'+ response + '</p>');
            }
            
            setTimeout(function () {
                $('.alert').fadeOut(500);
            }, 5000);
            
            $.ajax({
              url: "manage_users_up.php"
              }).done(function(data) {
              $('#manage_users').html(data);
            });
          }
        });
      }
    });
  });

  //Select exist customer to update information
  $(document).on('click', '.select_btn', function(){
    var el = this;
    var card_uid = $(this).attr("id");
    $.ajax({
      url: 'manage_users_conf.php',
      type: 'GET',
      data: {
      'select': 1,
      'card_uid': card_uid,
      },
      success: function(response){

        $(el).closest('tr').css('background','#70c276');

        $('.alert_user').fadeIn(500);
        $('.alert_user').html('<p class="alert alert-success">Đã chọn khách hàng!</p>');
        
        setTimeout(function () {
            $('.alert').fadeOut(500);
        }, 5000);

        $.ajax({
          url: "manage_users_up.php"
          }).done(function(data) {
          $('#manage_users').html(data);
        });

        console.log(response);

        var user_id = {
          User_id : []
        };
        var user_name = {
          User_name : []
        };
        var user_on = {
          User_on : []
        };
        var user_CMND_CCCD = {
          User_CMND_CCCD : []
        };
        var user_SDT = {
          User_SDT : []
        };
        var user_gender = {
          User_gender : []
        };

        var len = response.length;

        for (var i = 0; i < len; i++) {
            user_id.User_id.push(response[i].id);
            user_name.User_name.push(response[i].username);
            user_on.User_on.push(response[i].room_number);
            user_CMND_CCCD.User_CMND_CCCD.push(response[i].CMND_CCCD);
            user_SDT.User_SDT.push(response[i].SDT);
            user_gender.User_gender.push(response[i].gender);
        }
        $('#user_id').val(user_id.User_id);
        $('#name').val(user_name.User_name);
        $('#number').val(user_on.User_on);
        $('#cmnd_cccd').val(user_CMND_CCCD.User_CMND_CCCD);
        $('#sdt').val(user_SDT.User_SDT);

        if (user_gender.User_gender == 'Nam'){
            $('.form-style-5').find(':radio[name=gender][value="Nam"]').prop('checked', true);
        }
        else if (user_gender.User_gender == 'Nữ'){
            $('.form-style-5').find(':radio[name=gender][value="Nữ"]').prop('checked', true);
        } else if (user_gender.User_gender == 'Khác'){
            $('.form-style-5').find(':radio[name=gender][value="Khác"]').prop('checked', true); 
        }

      },
      error : function(data) {
        console.log(data);
      }
    });
  });
});