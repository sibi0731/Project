$(document).ready(function() {
    var login=localStorage.getItem("login");
    if(login){
        var id=localStorage.getItem("id");
        window.location.href = 'profile.html?id=' +id; 
    }
    $('#loginform').submit(function(event) {
        event.preventDefault(); 
        var formData = $(this).serialize();
        $.ajax({
            type: 'POST',
            url: 'php/login.php', 
            data: formData,
            dataType: 'json', 
            success: function(response) {
                if (response.success === true) {
                    localStorage.setItem("login",true);
                    localStorage.setItem("id",response.id);
                    localStorage.setItem("email",response.email);
                    window.location.href = 'profile.html?id=' +response.id; 
                } else {                
                    $('#myform').html("<div class='alert alert-danger'>Invalid email or password.</div>");
                }
            },
            error: function(xhr, status, error) {
                console.log("AJAX request failed:", error); 
            }
        });
    });
});
