$(document).ready(function () {
	$("#form-thing").submit(function (e) {
		var type = $("#postType").val();
		var title = $("#post-title").val();
		if (type == "text") {
			var text = $("#text-content").val();
		} else {
			var text = "";
		}
		if (title.length > 255) {
			e.preventDefault();
			var titleerror = document.getElementById("title-error");
			titleerror.innerHTML = "Title too long. Max character is 255";
		} else if (text != "" && text.length > 4095) {
			e.preventDefault();
			var texterror = document.getElementById("text-error");
			texterror.innerHTML = "Text too long. Max character is 4095";
		} else {
			var formData = new FormData();
			formData.append("data", title);
			var xhr = new XMLHttpRequest();
			xhr.open("POST", "censor.php", false);
			xhr.send(formData);
            $("#post-title").val(xhr.responseText);

            if (type == "text") {
                var formData = new FormData();
                formData.append("data", text);
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "censor.php", false);
                xhr.send(formData);
                $("#text-content").val(xhr.responseText);
            }
		}
	});
});
