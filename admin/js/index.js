$(document).ready(function() {

    // Clear errors while typing
    $("#txtemail").on("input", function() {
        $("#emailError").text("");
    });
    $("#txtpassword").on("input", function() {
        $("#passwordError").text("");
    });

    // Email validation on blur
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

    // Password validation on blur
    $("#txtpassword").on("blur", function() {
        let email = $("#txtemail").val().trim();
        let password = $(this).val().trim();
        $("#passwordError").text("");

        if (email === "" || password === "") return;

        // Only check password if email is valid
        if ($("#emailError").text() !== "") return;

        $.ajax({
            url: "ajaxadmin/checkusername.php",
            type: "POST",
            dataType: "json",
            data: { email: email, password: password },
            success: function(response) {
                if (response.status === "wrong_password") {
                    $("#passwordError").text("Wrong Password");
                } else {
                    $("#passwordError").text(""); // correct password
                }
            },
            error: function(xhr, status, error) {
                console.log("AJAX error:", status, error);
                console.log(xhr.responseText);
            }
        });
    });

    // Optional: prevent form submission if errors exist
    $("#adminLoginForm").on("submit", function(e) {
        e.preventDefault();
        if ($("#emailError").text() !== "" || $("#passwordError").text() !== "") return;

        let email = $("#txtemail").val().trim();
        let password = $("#txtpassword").val().trim();

        $.ajax({
            url: "ajaxadmin/checkusername.php",
            type: "POST",
            dataType: "json",
            data: { email: email, password: password },
            success: function(response) {
                if (response.status === "success") {
                    window.location.href = "dashboard.php"; // redirect
                } else if (response.status === "wrong_password") {
                    $("#passwordError").text("Wrong Password");
                } else if (response.status === "invalid_email") {
                    $("#emailError").text("Invalid Email, Check your email");
                }
            }
        });
    });

});
