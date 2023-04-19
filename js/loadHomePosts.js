function loadPosts() {
	let params = new URLSearchParams(location.search);
	var search = params.get("search");
	$(".posts .post").remove();
	$.ajax({
		url: "getPosts.php",
		type: "GET",
		data: "filter=" + $("#filter").val() + (search != null ? "&search=" + search : ""),
		success: function (data) {
			if (data.length == 0) {
				$("#actually-the-posts").append('<div class="post no-post"><p>There are no posts here!</p></div>');
			} else {
				$("#actually-the-posts").append(data);
				setVoteListeners();
				setShareListeners();
			}
		},
		error: function (error) {
			$("#actually-the-posts").append('<div class="post no-post"><p>There are no posts here!</p></div>');
		},
	});
}

$(document).ready(function () {
	loadPosts();
});

function setShareListeners() {
	$(".share").click(function () {
        var postId = $(this).parent().parent().parent().attr("id");
        const currentUrl = window.location.href;
        const url = currentUrl.substring(0, currentUrl.lastIndexOf("/") + 1) + "post.php?id=" + postId;
		const postTitle = $(this).parent().parent().parent().find(".h2").text();

        // Works for Chrome, but not Firefox
		if (navigator.share) {
			try {
				navigator.share({
					title: postTitle,
					url: url,
				});
			} catch (error) {
				console.error("Error sharing:", error);
			}
		} else {
			$(this).find("p").html("Link Copied!");
			navigator.clipboard.writeText(url);
		}
	});
}

async function share(post) {

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
