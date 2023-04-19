const transpose = (arr) => {
	const result = {};
	arr.forEach((obj) => {
		for (const [key, value] of Object.entries(obj)) {
			if (!result[key]) {
				result[key] = [];
			}
			result[key].push(value);
		}
	});
	return result;
};

function dailyActivity(option, timePeriod) {
	$.ajax({
		url: "statfetcher.php",
		type: "POST",
		data: {
			query: option,
			modifier: timePeriod,
		},
		success: function (data) {
			
			data = JSON.parse(data);
			if (data.success == false) {
				$("#daily-activity-error").html("Error: " + data.response);
				return;
			} else {
				$("#daily-activity-error").html("");
			}
			var transposedData = transpose(data.response);

			var xValues = transposedData["date"];
			var yValues = transposedData["count"];
			
			// If there is no data, display a message
			if (xValues == undefined) {
				$("#daily-activity-error").html("No data in this time period");
				return;
			}

			// Destroy the old chart
			let chartStatus = Chart.getChart("daily-activity-canvas");
			if (chartStatus != undefined) {
				chartStatus.destroy();
			}

			new Chart("daily-activity-canvas", {
				type: "line",
				data: {
					labels: xValues,
					datasets: [
						{
							data: yValues,
							borderColor: "#f24600",
							fill: false,
						},
					],
				},
				options: {
					plugins: {
						legend: {
							display: false,
						},
					},
					y: {
						title: {
							display: true,
							text: "Count",
						},
						min: 0,
						ticks: {
							stepSize: 1,
						},
					},
				},
			});
		},
		error: function (data) {
			$("#daily-activity-error").html("Error fetching data");
		},
	});
}

function postActivity(option, timePeriod) {
	$.ajax({
		url: "statfetcher.php",
		type: "POST",
		data: {
			query: option,
			modifier: timePeriod,
		},
		success: function (data) {
			data = JSON.parse(data);
			var transposedData = transpose(data.response);
			if (data.success == false) {
				$("#post-activity-error").html("Error: " + data.response);
				return;
			} else {
				$("#post-activity-error").html("");
			}
			
			var count = transposedData["count"];
			
			if (count == undefined) {
				$("#post-activity-error").html("No posts in this time period");
				return;
			}
			
			// Destroy the old chart
			let chartStatus = Chart.getChart("post-activity-canvas");
			if (chartStatus != undefined) {
				chartStatus.destroy();
			}

			// Use the list of count and create 10 buckets for a histogram
			var buckets = [];
			var max = Math.max(...count);
			var min = Math.min(...count);
			var numBuckets = Math.min(10, max - min + 1);
			var bucketSize = Math.ceil((max - min + 1) / numBuckets);
			// Initialize the buckets
			for (var i = 0; i < numBuckets; i++) {
				buckets.push(0);
			}
			// Fill the buckets
			for (var i = 0; i < count.length; i++) {
				var index = Math.floor((count[i] - min) / bucketSize);
				// Edge case for the max value
				if (index == numBuckets) {
					index--;
				}
				buckets[index]++;
			}

			var yValues = buckets;
			var xValues = [];
			// Create the x-axis labels
			for (var i = 0; i < numBuckets; i++) {
				var start = min + i * bucketSize;
				var end = start + bucketSize - 1;
				xValues.push(start + " to " + end);
			}

			new Chart("post-activity-canvas", {
				type: "bar",
				data: {
					labels: xValues,
					datasets: [
						{
							data: yValues,
							backgroundColor: "#f24600",
						},
					],
				},
				options: {
					plugins: {
						legend: {
							display: false,
						},
					},
					y: {
						min: 0,
						ticks: {
							stepSize: 1,
						},
					},
				},
			});
		},
		error: function (data) {
			$("#post-activity-error").html("Error fetching data");
		},
	});
}

function interactions(timePeriod) {
	$.ajax({
		url: "statfetcher.php",
		type: "POST",
		data: {
			query: 4,
			modifier: timePeriod,
		},
		success: function (data) {
			data = JSON.parse(data);
			if (data.success == false) {
				$("#interactions-error").html("Error: " + data.response);
				return;
			} else {
				$("#interactions-error").html("");
			}
			var transposedData = transpose(data.response);

			var xValues = transposedData["date"];
			var yValues = transposedData["count"];

			// If there is no data, display a message
			if (xValues == undefined) {
				$("#interactions-error").html("No interactions in this time period");
				return;
			}

			// Destroy the old chart
			let chartStatus = Chart.getChart("interactions-canvas");
			if (chartStatus != undefined) {
				chartStatus.destroy();
			}

			new Chart("interactions-canvas", {
				type: "line",
				data: {
					labels: xValues,
					datasets: [
						{
							data: yValues,
							borderColor: "#f24600",
							fill: false,
						},
					],
				},
				options: {
					plugins: {
						legend: {
							display: false,
						},
					},
					y: {
						title: {
							display: true,
							text: "Count",
						},
						min: 0,
						ticks: {
							stepSize: 1,
						},
					},
				},
			});
		},
		error: function (data) {
			$("#interactions-error").html("Error fetching data");
		},
	});
}

function activeUsers(numDays) {
	$.ajax({
		url: "statfetcher.php",
		type: "POST",
		data: {
			query: 5,
			modifier: numDays,
		},
		success: function (data) {
			// Destroy the old chart
			let chartStatus = Chart.getChart("active-users-canvas");
			if (chartStatus != undefined) {
				chartStatus.destroy();
			}

			data = JSON.parse(data);
			if (data.success == false) {
				$("#active-users-error").html("Error: " + data.response);
				return;
			} else {
				$("#active-users-error").html("");
			}

			var transposedData = transpose(data.response);
			var users = transposedData["username"];
			if (users == undefined) {
				$("#active-users-list").html("No active users in this time period");
				return;
			}

			// Clear the list
			$("#active-users-list").html("");

			// Show the total number of active users
			$("#active-users-list").append(
				`<li class="list-group-item" style="font-weight: bold; text-decoration: underline;">Total: ${users.length}</li>`
			);
			for (var i = 0; i < users.length; i++) {
				// Add each user to the list
				$("#active-users-list").append(
					`<li class="list-group-item">${users[i]}</li>`
				);
			}
		},
		error: function (data) {
			$("#active-users-error").html("Error fetching data");
		},
	});
}

function postType(timePeriod) {
	$.ajax({
		url: "statfetcher.php",
		type: "POST",
		data: {
			query: 6,
			modifier: timePeriod,
		},
		success: function (data) {
			data = JSON.parse(data);
			if (data.success == false) {
				$("#post-type-error").html("Error: " + data.response);
				return;
			} else {
				$("#post-type-error").html("");
			}
			var transposedData = transpose(data.response);

			if (transposedData["textRatio"][0] == null) {
				$("#post-type-error").html("No posts in this time period");
				return;
			}

			var textRatio = transposedData["textRatio"];

			var xValues = ["Text", "Image"];
			var yValues = [textRatio * 100, (1 - textRatio) * 100];

			// Destroy the old chart
			let chartStatus = Chart.getChart("post-type-canvas");
			if (chartStatus != undefined) {
				chartStatus.destroy();
			}

			new Chart("post-type-canvas", {
				type: "pie",
				data: {
					labels: xValues,
					datasets: [
						{
							data: yValues,
							backgroundColor: ["#f24600", "#244cc3", "green"],
						},
					],
				},
				options: {
					plugins: {
						legend: {
							display: true,
						},
					},
				},
			});
		},
		error: function (data) {
			$("#post-type-error").html("Error fetching data");
		},
	});
}

function popularHours(timePeriod) {
	$.ajax({
		url: "statfetcher.php",
		type: "POST",
		data: {
			query: 7,
			modifier: timePeriod,
		},
		success: function (data) {
			data = JSON.parse(data);
			if (data.success == false) {
				$("#popular-hours-error").html("Error: " + data.response);
				return;
			} else {
				$("#popular-hours-error").html("");
			}
			var transposedData = transpose(data.response);

			var hour = transposedData["hour"] == undefined ? [] : transposedData["hour"];
			var count = transposedData["count"] == undefined ? [] : transposedData["count"];

			// cast all the values to integers
			hour = hour.map(Number);
			count = count.map(Number);
			
			var xValues = [];
			var yValues = [];

			// Add the hours to the xValues array filling in any missing hours and converting the x values to be formatted 24 hour time
			for (var i = 0; i < 24; i++) {
				if (hour.includes(i)) {
					xValues.push(i + ":00");
					yValues.push(count[hour.indexOf(i)]);
				} else {
					xValues.push(i + ":00");
					yValues.push(0);
				}
			}

			// Destroy the old chart
			let chartStatus = Chart.getChart("popular-hours-canvas");
			if (chartStatus != undefined) {
				chartStatus.destroy();
			}

			new Chart("popular-hours-canvas", {
				type: "bar",
				data: {
					labels: xValues,
					datasets: [
						{
							data: yValues,
							backgroundColor: "#f24600",
							fill: false,
						},
					],
				},
				options: {
					plugins: {
						legend: {
							display: false,
						},
					},
					y: {
						title: {
							display: true,
							text: "Count",
						},
						min: 0,
						ticks: {
							stepSize: 1,
						},
					},
				},
			});
		},
		error: function (data) {
			$("#popular-hours-error").html("Error fetching data");
		},
	});
}

function initGraphs() {
	dailyActivity(0, 7);
	postActivity(2, 7);
	interactions(7);
	activeUsers(7);
	postType(7);
	popularHours(7);
}

$(document).ready(function () {
	initGraphs();

	$("#daily-activity-select").change(function () {
		var selectedOption = $(this).val();
		var time = $("#daily-activity-time-select").val();
		dailyActivity(selectedOption, time);
	});

	$("#daily-activity-time-select").change(function () {
		var selectedOption = $("#daily-activity-select").val();
		var time = $(this).val();
		dailyActivity(selectedOption, time);
	});
	
	$("#post-activity-select").change(function () {
		var selectedOption = $(this).val();
		var time = $("#post-activity-time-select").val();
		postActivity(selectedOption, time);
	});

	$("#post-activity-time-select").change(function () {
		var selectedOption = $("#post-activity-select").val();
		var time = $(this).val();
		postActivity(selectedOption, time);
	});

	$("#interactions-select").change(function () {
		var selectedOption = $(this).val();
		interactions(selectedOption);
	});

	$("#active-users-select").change(function () {
		var selectedOption = $(this).val();
		activeUsers(selectedOption);
	});

	$("#post-type-select").change(function () {
		var selectedOption = $(this).val();
		postType(selectedOption);
	});

	$("#popular-hours-select").change(function () {
		var selectedOption = $(this).val();
		popularHours(selectedOption);
	});
});
