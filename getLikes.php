<?php
require __DIR__ . '/vendor/autoload.php';

$dbUser = "USERNAME";
$dbpassword = "PASSWORD";
$mysqli = mysqli_connect("SERVER", $dbUser, $dbpassword,"DB");
if (mysqli_connect_errno($mysqli)) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
                die;
}

use GuzzleHttp\Client;

function add($type, $blog_name, $source_title, $id_string, $post_url, $date, $timestamp, $state, $format, $reblog_key, 
$tags, $short_url, $summary, $liked_timestamp) {
	global $mysqli;

$stmt = $mysqli->prepare("insert into likes (type, blog_name, source_title, id_string, post_url, date, timestamp, state, 
format, reblog_key, tags, short_url, summary, liked_timestamp) values 
(?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
if (!$stmt) {
	echo("Error adding" . $mysqli->error);
	return "error";
}
$stmt->bind_param("ssssssssssssss",$type, $blog_name, $source_title, $id_string, $post_url, $date, $timestamp, $state, 
$format, $reblog_key, $tags, $short_url, $summary, $liked_timestamp);
if (!$stmt) {
	echo("Error adding" . $mysqli->error);
	return "error";
}

$stmt->execute();
if (!$stmt) {
	echo("Error adding" . $mysqli->error);
	return "error";
}

return "Added";
}

function getValue($k) {
	global $mysqli;
	$stmt = $mysqli->prepare("select password from users where 
user=?");
	if (!$stmt) {
		error_log("Error on getValue " . $mysqli->error);
		return null;
	}

	$stmt->bind_param("s",$k);
	$stmt->execute();
	$stmt->bind_result($user);

	$results = array();

	while ($stmt->fetch()) {
		$rec = ['user'=>$user];
		array_push($results, $rec);
	}
	return $results;
}


function apiCall($uri) {
	$client = new Client(['base_uri' => $uri, 'timeout' => 0,]);
	//global $client;
	try {
		$response = $client->get("");
	} catch (Exception $e) {
		print_r($e);
		echo "API Call Error";

	}
	$body = (string) $response->getBody();
	$jbody = json_decode($body);
	if (!$jbody) {
		echo "Not json reply";
	}
	return $jbody;
}

function looper() {
	$uri = "https://api.tumblr.com/v2/blog/taylorswift.tumblr.com/likes?api_key=YOURTUMBLRAPIKEY&limit=50";
	$call = apiCall($uri);
	$array_length = sizeof($call->response->liked_posts);
	$before = $call->response->_links->next->query_params->before;
	echo $array_length;
	
	for($i = 0; $i < $array_length; $i++) {
		if (isset($call->response->liked_posts[$i]->source_title)) {
			$sourceTitle = $call->response->liked_posts[$i]->source_title;
		} else {
			$sourceTitle = "";
		}
		$data = 
add($call->response->liked_posts[$i]->type,$call->response->liked_posts[$i]->blog_name,$sourceTitle,$call->response->liked_posts[$i]->id_string,$call->response->liked_posts[$i]->post_url,$call->response->liked_posts[$i]->date,$call->response->liked_posts[$i]->timestamp,$call->response->liked_posts[$i]->state,$call->response->liked_posts[$i]->format,$call->response->liked_posts[$i]->reblog_key,implode(",",$call->response->liked_posts[$i]->tags),$call->response->liked_posts[$i]->short_url,$call->response->liked_posts[$i]->summary,$call->response->liked_posts[$i]->liked_timestamp);
	}
	$count = 0;
	while($array_length != 0) {
		sleep(0.5);
		$count++;
		echo "Runs: " . $count . "\n";
		$uri = "https://api.tumblr.com/v2/blog/taylorswift.tumblr.com/likes?api_key=YOURTUMBLRAPIKEY&limit=50" . "&before=". $before;
		$call = apiCall($uri);
		$array_length = sizeof($call->response->liked_posts);
		$before = $call->response->_links->next->query_params->before;
		
		for($i = 0; $i < $array_length; $i++) {
			if (isset($call->response->liked_posts[$i]->source_title)) {
                        $sourceTitle = $call->response->liked_posts[$i]->source_title;
                } else {
                        $sourceTitle = "";
                }
			$data = add($call->response->liked_posts[$i]->type,$call->response->liked_posts[$i]->blog_name,$sourceTitle,$call->response->liked_posts[$i]->id_string,$call->response->liked_posts[$i]->post_url,$call->response->liked_posts[$i]->date,$call->response->liked_posts[$i]->timestamp,$call->response->liked_posts[$i]->state,$call->response->liked_posts[$i]->format,$call->response->liked_posts[$i]->reblog_key,implode(",",$call->response->liked_posts[$i]->tags),$call->response->liked_posts[$i]->short_url,$call->response->liked_posts[$i]->summary,$call->response->liked_posts[$i]->liked_timestamp);
		}
		echo "Before is: " . $before . "\n";

	}
	echo "Status: " . $call->meta->status . "\n";
	echo "Message: " .$call->meta->msg;	
}
looper();
?>
