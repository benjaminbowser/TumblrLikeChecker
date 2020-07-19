<?php
function getLikes($mysqli, $user) {
	$stmt = $mysqli->prepare("SELECT * FROM likes WHERE blog_name =? OR source_title =?");
	$stmt->bind_param("ss", $user, $user);
	$stmt->execute();
	$result = $stmt->get_result();
	$masterData = array();
		while ($row = $result->fetch_assoc()){
			$items = array("id_string"=>$row['id_string'], "type"=>$row['type'],"blog_name"=>$row['blog_name'], "source_title"=>$row['source_title'], "post_url"=>$row['post_url'], "date"=>$row['date'], 
"timestamp"=>$row['timestamp'], "state"=>$row['state'], "format"=>$row['format'], "reblog_key"=>$row['reblog_key'], "tags"=>$row['tags'], "short_url"=>$row['short_url'], "summary"=>$row['summary'], "liked_timestamp"=>$row['liked_timestamp']);
			$masterData[] = $items;
			$items = array();
		}
	return $masterData;

}

function getTopLikes($mysqli) {
	$sql = "SELECT blog_name, COUNT(blog_name) AS value_occurrence FROM likes GROUP BY blog_name ORDER BY value_occurrence DESC LIMIT 25";
	$result = $mysqli->query($sql);
	if ($result->num_rows > 0){
		while ($row = $result->fetch_assoc()){
			$items = array("blog_name"=>$row['blog_name'], "value"=>$row['value_occurrence']);
			$masterData[] = $items;
			$items = array();
		}
	}
	return $masterData;
}
?>
