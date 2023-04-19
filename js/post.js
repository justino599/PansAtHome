function updateComments(str) {
	var currentComments = $("#comments").html();
	if (str != currentComments) {
		$("#comments").html(str);
		setCommentVoteListeners();
		setCommentShareListeners();
	}
}

function loadComments() {
	let params = new URLSearchParams(location.search);
	var search = params.get("search");
	$.ajax({
		url: "getComments.php",
		type: "GET",
		data: "postId=" + $("#postId").val() + (search != null ? "&search=" + search : ""),
		success: function (data) {
			if (data.length == 0) {
				updateComments('<div class="post no-post"><p>Be the first to comment!</p></div>');
			} else {
				updateComments(data);
			}
		},
		error: function (error) {
			updateComments('<div class="post no-post"><p>Be the first to comment!</p></div>');
		},
	});
}

$(document).ready(function () {
	loadComments();
	setVoteListeners();
	setPostShareListener();
	setInterval(loadComments, 8000);
});

function setPostShareListener() {
	$(".post .share").click(function () {
		const currentUrl = window.location.href;
		const url = currentUrl.substring(0, currentUrl.lastIndexOf("/") + 1) + "post.php?id=" + $(".post").attr("id");

		// Works for Chrome, but not Firefox
		// if (navigator.share) {
		// 	try {
		// 		navigator.share({
		// 			title: postTitle,
		// 			url: url,
		// 		});
		// 	} catch (error) {
		// 		console.error("Error sharing:", error);
		// 	}
		// } else {
		// 	console.log("Web Share API is not supported in this browser.");
		// }
		navigator.clipboard.writeText(url);
		$(this).find("p").html("Link Copied!");
	});
}

function setCommentShareListeners() {
	$(".comment .share").click(function () {
        var postId = $(".post").attr("id");
        const currentUrl = window.location.href;
        const url = currentUrl.substring(0, currentUrl.lastIndexOf("/") + 1) + "post.php?id=" + $(".post").attr("id");

        // Works for Chrome, but not Firefox
		// if (navigator.share) {
		// 	try {
		// 		navigator.share({
		// 			title: postTitle,
		// 			url: url,
		// 		});
		// 	} catch (error) {
		// 		console.error("Error sharing:", error);
		// 	}
		// } else {
		// 	console.log("Web Share API is not supported in this browser.");
		// }
        navigator.clipboard.writeText(url);
        $(this).find("p").html("Link Copied!");
	});
}

function setCommentVoteListeners() {
	$(".comment .votes>img").click(function () {
		var $imgUp = $(this).parent().find(".upvote");
		var $imgdown = $(this).parent().find(".downvote");
		var commentID = $(this).closest(".comment").attr("id");
		var vote = $(this).attr("class");
		$.ajax({
			url: "commentVote.php",
			type: "POST",
			data: "commentID=" + commentID + "&vote=" + vote,
			success: function (data) {
				var karmaChange = data.split(",");
				if (karmaChange.length == 3) {
					if (karmaChange[0] == "orange") {
						$imgUp.attr("src", "resources/ChefKnifeUp.svg" + "?_=" + new Date().getTime());
						$imgUp.next("div").text(karmaChange[1]);
					}
					if (karmaChange[0] == "white") {
						$imgUp.attr("src", "resources/ChefKnife.svg");
						$imgUp.next("div").text(karmaChange[1]);
					}
					if (karmaChange[2] == "white") {
						$imgdown.attr("src", "resources/ChefKnife.svg");
						$imgdown.prev("div").text(karmaChange[1]);
					}
					if (karmaChange[2] == "blue") {
						$imgdown.attr("src", "resources/ChefKnifeDown.svg");
						$imgdown.prev("div").text(karmaChange[1]);
					}
				} else {
					alert(data);
				}
			},
			error: function (error) {
				alert("There was an error with your vote. Please try again later.");
			},
		});
	});
}

function setVoteListeners() {
	$(".post .votes>img").click(function () {
		var $imgUp = $(this).parent().find(".upvote");
		var $imgdown = $(this).parent().find(".downvote");
		var post = new URLSearchParams(location.search);
		var postID = post.get("id");
		var vote = $(this).attr("class");
		$.ajax({
			url: "vote.php",
			type: "POST",
			data: "postID=" + postID + "&vote=" + vote,
			success: function (data) {
				var karmaChange = data.split(",");
				if (karmaChange.length == 3) {
					if (karmaChange[0] == "orange") {
						$imgUp.attr("src", "resources/ChefKnifeUp.svg" + "?_=" + new Date().getTime());
						$imgUp.next("div").text(karmaChange[1]);
					}
					if (karmaChange[0] == "white") {
						$imgUp.attr("src", "resources/ChefKnife.svg");
						$imgUp.next("div").text(karmaChange[1]);
					}
					if (karmaChange[2] == "white") {
						$imgdown.attr("src", "resources/ChefKnife.svg");
						$imgdown.prev("div").text(karmaChange[1]);
					}
					if (karmaChange[2] == "blue") {
						$imgdown.attr("src", "resources/ChefKnifeDown.svg");
						$imgdown.prev("div").text(karmaChange[1]);
					}
				} else {
					alert(data);
				}
			},
			error: function (error) {
				alert("There was an error with your vote. Please try again later.");
			},
		});
	});
}
