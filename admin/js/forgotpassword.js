$(document).ready(function(){
    $("#txtemail").on("input", function() {
        $("#emailError").text("");
    });
    $("#txtemail").on("blur", function() {
        let email = $(this).val().trim();
        $("#emailError").text("");

        if (email === "") return;

        $.ajax({
            url: "ajaxadmin/checkusername.php",
            type: "POST",
            dataType: "json",
            data: { email: email, password: "" },
            success: function(response) {
                if (response.status === "invalid_email") {
                    $("#emailError").text("Invalid Email, Check your email");
                } else {
                    $("#emailError").text(""); // email exists
                }
            },
            error: function(xhr, status, error) {
                console.log("AJAX error:", status, error);
                console.log(xhr.responseText);
            }
        });
    });
    
    $('#txtnewpass, #txtconformpass').on('blur', function(){
        let pass = $('#txtnewpass').val();
        let confirmPass = $('#txtconformpass').val();

        if(pass !== '' && confirmPass !== ''){
            if(pass !== confirmPass){
                $('#passworderror').text('⚠️ Passwords do not match.');
            } else {
                $('#passworderror').text('');
            }
        } else {
            $('#passworderror').text('');
        }
    });
})