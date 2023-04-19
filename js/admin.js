$(document).ready(function () {
	$("#ban-user").submit(function (e) {
		e.preventDefault();

		var user = $(this).find('input[name="user"]').val();
		var reason = $(this).find('textarea[name="reason"]').val();
		if (!confirm("Are you sure you want to ban " + user + "?")) return false;

		$.ajax({
			type: "POST",
			url: "updateUser.php",
			data: "what=banned&to=1&username=" + user,
			success: function (data) {
				if (data == "success") {
					$.ajax({
						type: "POST",
						url: "updateUser.php",
						data: "what=banReason&to=" + reason + "&username=" + user,
						success: function (data) {
							if (data == "success") {
								$("#bans-col .entry-box").find("input").val("");
								$("#bans-col .entry-box").find("textarea").val("");
                                $("#bans-col #none").remove();
								$("#bans-col .col-content").append('<div class="ban" user=' + user + '><div class="left"><h2><a href="user.php?user=' + user + '">u/' + user + "</a></h2><p>" + reason + '</p></div><img class="ban-icon" src="resources/unban.svg" title="Unban" alt="Unban" onclick="javascript:unban(this)"></div>');
							} else {
								alert(data);
							}
						},
					});
				} else {
					alert(data);
				}
			},
		});

		return false;
	});

    $("#ban-word").submit(function (e) {
        e.preventDefault();

        var word = $(this).find('input[name="word"]').val();
        if (!confirm("Are you sure you want to ban the word \"" + word + "\"?")) return false;

        $.ajax({
            type: "POST",
            url: "updateBannedWords.php",
            data: "action=add&word=" + word,
            success: function (data) {
                if (data == "success") {
                    $("#filter-col #none").remove();
                    $("#filter-col .entry-box").find("input").val("");
                    $("#filter-col .col-content").append('<div class="filter-word" word="' + word + '"><p>' + word + '</p><img class="ban-icon" src="resources/x.svg" title="Remove word" alt="Remove word" onclick="javascript:unbanWord(this)"></div>');
                } else {
                    alert(data);
                }
            },
        });
    });
});

function removePost(clicked) {
    var reportedId = $(clicked).parent().parent().attr("reportedId");
    console.log('id:' + reportedId)
    var reportId = $(clicked).parent().parent().attr("reportId");
    var type = $(clicked).parent().parent().attr("reportType").toLowerCase();
    if (!confirm("Are you sure you want to remove this " + type + "?")) return false;
    $.ajax({
        type: "POST",
        url: "removeContent.php",
        data: "table=" + type + "&id=" + reportedId,
        success: function (data) {
            if (data == "success") {
                $.ajax({
                    type: "POST",
                    url: "removeContent.php",
                    data: "table=report&id=" + reportId,
                    success: function (data) {
                        if (data == "success") {
                            $(clicked).parent().parent().remove();
                            if ($("#reports-col .col-content").children().length == 0) {
                                $("#reports-col .col-content").append("<div class='filter-word' id='none'><p>You cleared 'em all!</p></div>");
                            }
                        } else {
                            alert(data);
                        }
                    },
                });
            } else {
                alert(data);
            }
        },
    });
}

function ignoreReport(clicked) {
    var reportId = $(clicked).parent().parent().attr("reportId");
    if (!confirm("Are you sure you want to ignore this report?")) return false;
    $.ajax({
        type: "POST",
        url: "removeContent.php",
        data: "table=report&id=" + reportId,
        success: function (data) {
            if (data == "success") {
                $(clicked).parent().parent().remove();
                if ($("#reports-col .col-content").children().length == 0) {
                    $("#reports-col .col-content").append("<div class='filter-word' id='none'><p>You cleared 'em all!</p></div>");
                }
            } else {
                alert(data);
            }
        },
    });
}

function unbanWord(clicked) {
    var word = $(clicked).parent().attr("word");
    if (!confirm("Are you sure you want to unban the word \"" + word + "\"?")) return false;
    $.ajax({
        type: "POST",
        url: "updateBannedWords.php",
        data: "action=remove&word=" + word,
        success: function (data) {
            if (data == "success") {
                $(clicked).parent().remove();
                if ($("#filter-col .col-content").children().length <= 1) {
                    $("#filter-col .col-content").append("<div class='filter-word' id='none'><p>No banned words</p></div>");
                }
            } else {
                alert(data);
            }
        },
    });
}

function unban(clicked) {
    var user = $(clicked).parent().attr("user");
	if (!confirm("Are you sure you want to unban this user?")) return false;
	$.ajax({
		type: "POST",
		url: "updateUser.php",
		data: "what=banned&to=0&username=" + user,
		success: function (data) {
			if (data == "success") {
				$.ajax({
					type: "POST",
					url: "updateUser.php",
					data: "what=banReason&to=&username=" + user,
					success: function (data) {
						if (data == "success") {
							$("#bans-col .col-content")
								.find('div.ban[user="' + user + '"]')
								.remove();
                            if ($("#bans-col .col-content").children().length <= 1) {
                                $("#bans-col .col-content").append("<div class='filter-word' id='none'><p>No banned users</p></div>");
                            }
						} else {
							alert(data);
						}
					},
				});
			} else {
				alert(data);
			}
		},
	});
}
