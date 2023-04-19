function validateRegister(data) {
    var dataArray = data.split("&");
    var result = {};
    for (var i = 0; i < dataArray.length; i++) {
        var item = dataArray[i].split("=");
        result[item[0]] = decodeURIComponent(item[1]);
    }

    if (result['username'].length < 3) {
        return "Username must be at least 3 characters long";
    }
    if (result['username'].length > 30) {
        return "Username must be at most 30 characters long";
    }

    if (result['password'].length < 8) {
        return "Password must be at least 8 characters long";
    }
    if (result['password'].length > 30) {
        return "Password must be at most 30 characters long";
    }
    if (result['password'] != result['passwordconfirm']) {
        return "Passwords do not match";
    }
    // Password must have at least one uppercase letter, one lowercase letter, one number, and one special character
    if (!result['password'].match(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\da-zA-Z]).{8,30}$/)) {
        return "Password must have at least: <ul style='text-align: left'><li>one uppercase letter</li><li>one lowercase letter</li><li>one number</li><li>one special character</li></ul>";
    }
}

function validateLogin(data) {
    var dataArray = data.split("&");
    var result = {};
    for (var i = 0; i < dataArray.length; i++) {
        var item = dataArray[i].split("=");
        result[item[0]] = decodeURIComponent(item[1]);
    }
    
    // Just do basic validation here, if the user is not found in the database, the attemptLogin.php script will handle that
    if (result['username'].length < 3 || result['username'].length > 30 || result['password'].length < 8 || result['password'].length > 30) {
        return "Invalid username or password";
    }
}

$(document).ready(function () {
    $("#login").submit(function (e) {
        e.preventDefault();

        var data = $("#login").serialize();

        var validate = validateLogin(data);
        if (validate != null) {
            $("#login-error").html(validate);
            return false;
        }

        $.ajax({
            url: "attemptLogin.php",
            method: "post",
            data: data,
        }).done(function (response) {
            if (response == "success") {
                window.location.href = $("#redirect").val();
            } else {
                $("#login-error").html(response);
            }
        });
        return false;
    });

    $("#register").submit(function (e) {
        e.preventDefault();

        var data = $("#register").serialize();

        var validate = validateRegister(data);
        if (validate != null) {
            $("#login-error").html(validate);
            return false;
        }

        $.ajax({
            url: "attemptRegister.php",
            method: "post",
            data: data,
        }).done(function (response) {
            if (response == "success") {

                // Update database with pfp if it was provided
                if ($("#pfp").val() != "") {
                    var pfpFile = $("#pfp").prop('files')[0];
                    var formData = new FormData();
                    formData.append("what", "pfp");
                    formData.append("to", pfpFile);
                    formData.append("username", $("#username").val());

                    var xhr = new XMLHttpRequest();

                    xhr.open('POST', 'updateUser.php', false);
                    xhr.send(formData);
                }

                window.location.href = $("#redirect").val();
            } else {
                $("#login-error").html(response);
            }
        });

        return false;
    });
});
