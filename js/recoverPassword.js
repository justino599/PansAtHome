$(document).ready(function () {
	$("#password-recover-form").submit(function (e) {
		e.preventDefault();

		if ($("#username").val() != "" || $("#email").val() == "") {
			// Get the email address and username
			var username = $("#username").val();
			var email = $("#email").val();

			// Check to see if they match
			var formData = new FormData();
			formData.append("username", username);
			formData.append("email", email);
			var xhr = new XMLHttpRequest();
			xhr.open("POST", "usernameEmailCheck.php", false);
			xhr.send(formData);

			if (xhr.responseText != "true") {
				$("#login-error").val(xhr.responseText);
			} else {
				$("#username").attr("disabled", "disabled");
				$("#email").attr("disabled", "disabled");
				$("input.hide").removeClass("hide");
			}
		} else {
            $.ajax({
                url: 'updateUser.php',
                type: "POST",
                data: {
                    what: "password",
                    to: $("#password").val(),
                    user: $("#username").val()
                },
                success: function (data) {
                    if (data == "success") {
                        window.location.href = "login.php";
                    } else {
                        $("#login-error").val(data);
                    }
                }
            });
		}
	});
});
