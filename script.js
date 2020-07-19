$(document).ready(function(){

$("#form-sub").submit(function(e){
		e.preventDefault();
		var obj = {};
		obj.user =  $("#user").val();
		if (obj.user == "") {
			alert("Blog name cannot be blank!");
		} else {	
			getLikes(obj);
		}
	});

});	

function getLikes(obj) {
	$.ajax({
		url: "rest.php/v1/user",
		type: "POST", 
		dataType: "JSON",
		contentType: "application/json",
		data: JSON.stringify(obj),
		success: function (text){
			var fields = "";
			var str = JSON.stringify(text);
			var val = JSON.parse(str);
			if (val.status == "OK") {
				fields = "<table class='table'><tr><th>Blog Name</th><th>Source Title</th><th>Post URL</th><th>Date/Time Liked</th><tr>";
				for (var i = 0; i < val.posts.length; i++){
					fields += "<tr><td>" +  val.posts[i].blog_name + "</td><td>" + val.posts[i].source_title + "</td><td>" + "<a href=" + val.posts[i].short_url + ">View Post</a>" + "</td><td>" + new Date(val.posts[i].liked_timestamp * 1000) + "</td><tr>";
				}
			fields += "</table>";
			$("#likes").html(fields);
			} else if (val.status == "Error"){
				alert(val.msg);
			}
		},
		
error: function( xhr ) {
	      alert( "Something went wrong" );
}
	});
}
