$(document).ready(function(){
    var email=localStorage.getItem("email");  
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
