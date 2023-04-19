function changeEmail() {
	$("#email-dialog").dialog();

	$("#email-dialog form").submit(function (e) {
		e.preventDefault();

		var newEmail = $("#email-dialog input[name='email']").val();
		var confirm = $("#email-dialog input[name='emailConfirm']").val();

		if (newEmail != confirm) {
			$("#email-error").html("Emails do not match");
			return;
		}

		$("#email-dialog").dialog("close");

		$(this).find("input").val("");

		$.ajax({
			url: "updateUser.php",
			type: "POST",
			data: "what=email&to=" + newEmail + "&username=" + $("#username").val(),
			success: function (data) {
				if (data == "success") {
					$("#email").html(newEmail);
				} else {
					alert("Email change failed");
				}
			},
		});
	});
}

function changePassword() {
	$("#password-dialog").dialog();

	$("#password-dialog form").submit(function (e) {
		e.preventDefault();

		var oldPassword = $("#password-dialog input[name='oldPassword']").val();

		$.ajax({
			url: "verifyPassword.php",
			type: "POST",
			data: "password=" + oldPassword,
			success: function (data) {
				if (data == "success") {
					var newPassword = $("#password-dialog input[name='newPassword']").val();
					var confirm = $("#password-dialog input[name='passwordConfirm']").val();

					if (newPassword != confirm) {
						$("#password-error").html("Passwords do not match");
						return;
					}

					$("#password-dialog").dialog("close");

					$(this).find("input").val("");

					$.ajax({
						url: "updateUser.php",
						type: "POST",
						data: "what=password&to=" + newPassword + "&username=" + $("#username").val(),
						success: function (data) {
							if (data == "success") {
								alert("Password changed");
							} else {
								alert("Password change failed");
							}
						},
					});
				} else {
					$("#password-error").html("Incorrect password");
					return;
				}
			},
		});
	});
}

function changePfp() {
	$("#pfp-dialog").dialog();

	$("#pfp-dialog form").submit(function (e) {
		e.preventDefault();

		$("#pfp-dialog").dialog("close");

		if ($("#pfp").val() != "") {
			var pfpFile = $("#pfp").prop("files")[0];
			var formData = new FormData();
			formData.append("what", "pfp");
			formData.append("to", pfpFile);
			formData.append("username", $("#username").val());

			var xhr = new XMLHttpRequest();

			xhr.open("POST", "updateUser.php", false);
			xhr.send(formData);

			if (xhr.responseText == "success") {
				$("img.profile").attr("src", "userPfp.php?refresh=" + new Date().getTime() + "&user=" + $("#username").val());
				$("#profile img").attr("src", "userPfp.php?refresh=" + new Date().getTime() + "&user=" + $("#username").val());
			} else {
				alert("Pfp change failed");
			}
		}

		$(this).find("input").val("");
	});
}

function deleteAccount() {
	if (confirm("Are you sure you want to delete your account? This action cannot be undone.")) {
		window.location.href = "deleteAccount.php";
	}
}

$(document).ready(function () {
	$("#change-email").click(function () {
		changeEmail();
	});
	$("#change-password").click(function () {
		changePassword();
	});
	$("#change-pfp").click(function () {
		changePfp();
	});
});
