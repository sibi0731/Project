
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

$(document).ready(function() {
    $(document).on('keyup', '#confirmpassword', function () {
        let password = $("#password").val();
        let confirmPassword = $(this).val();
        if (password !== confirmPassword) {
            $("#checkpassword").html("Password does not match!").css("color", "red");
        } else {
            $("#checkpassword").html("Password match!").css("color", "green");
        }
    });

    $('#regform').submit(function(event) {
        event.preventDefault(); 

        const email = $("#email").val(); 
        if (!validateEmail(email)) {
            $("#emailerror").text("Invalid email address");
            return; 
        } else {
            $("#emailError").text(""); 
        }
           
        var formData = $(this).serialize(); 

        $.ajax({
            type: 'POST',
            url: 'php/register.php', 
            data: formData,
            success: function(response) {
                $('#passwordmessage').html(response); 
                $('#regform')[0].reset(); 
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText); 
            }
        });
    });
});
