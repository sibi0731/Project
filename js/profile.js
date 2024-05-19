$(document).ready(function(){
    var redisval=localStorage.getItem("redis");
    var email=localStorage.getItem("email");
    if(redisval){
        $.ajax({
            type: 'GET',
            url: 'redis.php',
            data: {"email":email},
            success: function(response){         
                $('#myprofile').html(response);
                $('#profileform')[0].reset();
                localStorage.setItem("redis",true);      
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText); 
            }
        });
    }else{        
        var email=localStorage.getItem("email");
        if (email) {
            $.ajax({
                type: 'GET',
                url: 'mongo.php',
                data: {"email": email},
                success: function(response) {
                    var response = JSON.parse(response);
                    if (response.error) {
                        console.error(response.error);
                        $('#profileform').show();
                    } else {
                        $('#myprofile').html(
                            '<p>email: ' + response.email + '</p>' +
                            '<p>name: ' + response.name + '</p>' 
                        );
                        $('#profileform')[0].reset();
                        localStorage.setItem("redis", true); 
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }       
    });
}
    }
  $('#profileform').submit(function(event){
      event.preventDefault();
      var formData = $(this).serialize();
      formData += '&id=';    
      $.ajax({
          type: 'POST',
          url: 'php/profile.php',
          data: formData,
          success: function(response){         
              $('#myprofile').html(response);
              $('#profileform')[0].reset();
              localStorage.setItem("redis",true);
          },
          error: function(xhr, status, error) {
              console.error(xhr.responseText); 
          }
      });
  });
});
document.getElementById('logoutbtn').addEventListener('click', logout);
function logout() {
    localStorage.clear();
    window.location.href = 'login.html';
}
