$(document).ready(function () {
	let params = new URLSearchParams(location.search);
	var search = params.get("search");
	$.ajax({
		url: "getPosts.php",
		type: "GET",
		data: "user=" + $("#username").val() + (search != null ? "&search=" + search : ""),
		success: function (data) {
			if (data.length == 0) {
				$(".posts").append('<div class="post no-post"><p>It appears that there are no posts here!</p></div>');
			} else {
				$(".posts").append(data);
                setVoteListeners();
				setCommentShareListeners();
			}
		},
		error: function (error) {
			$(".posts").append('<div class="post no-post"><p>It appears that there are no posts here!</p></div>');
		},
	});

	$.ajax({
		url: "getUserComments.php",
		type: "GET",
		data: "user=" + $("#username").val() + (search != null ? "&search=" + search : ""),
		success: function (data) {
			if (data.length == 0) {
				$(".posts").append('<div class="comment no-post" style="display: none;"><p>It appears that there are no comments here!</p></div>');
			} else {
				$(".posts").append(data);
                setCommentVoteListeners();
				setCommentShareListeners();
			}
		},
		error: function (error) {
            $(".posts").append('<div class="comment no-post" style="display: none;"><p>It appears that there are no comments here!</p></div>');
		},
	});
});

function setCommentShareListeners() {
	$(".share").click(function () {
		var postId = $(this).parent().parent().parent().parent().attr("postId");
		const currentUrl = window.location.href;
		const url = currentUrl.substring(0, currentUrl.lastIndexOf("/") + 1) + "post.php?id=" + postId;

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

function setVoteListeners() {
	$(".post .votes>img").click(function () {
		var $imgUp = $(this).parent().find(".upvote");
		var $imgdown = $(this).parent().find(".downvote");
		var post = $(this).parent().parent();
		var postID = post.attr("id");
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
