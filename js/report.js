$(document).ready(function () {
    $("#report").submit(function (e) {
        e.preventDefault();
		var reason = $("#report textarea").val();
		if (reason.length > 255) {
			$("#report-error").text("The reason is too long. Please shorten it to 255 characters or less. Currently, it is " + reason.length + " characters long.");
		} else {
			$.ajax({
				type: "POST",
				url: "submitReport.php",
				data: $("#report").serialize(),
				success: function (data) {
                    data = data.split(",");
					if (data[0] == "success") {
						window.location.href = "post.php?id=" + data[1];
					} else {
						$("#report-error").text(data);
					}
				},
			});
		}
	});
});
