<?php
$dbUser = "USERNAME";
$dbpassword = "PASSOWRD";
$mysqli = mysqli_connect("SERVER", $dbUser, $dbpassword,"DBNAME");
if (mysqli_connect_errno($mysqli)) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
                die;
}

require_once("calls.php");

// Returns data as json
function retJson($data) {
  header('content-type: application/json');
  print json_encode($data);
  exit;
}

// get request method into $path variable
$method = strtolower($_SERVER['REQUEST_METHOD']);
if (isset($_SERVER['PATH_INFO'])) {
	$path  = $_SERVER['PATH_INFO'];
} else {
	 $path = "";
}

//path comes in as /a/b/c - split it apart and make sure it passes basic checks

$pathParts = explode("/",$path);
if (count($pathParts) < 2) {
  $ret = array('status'=>'FAIL','msg'=>'Invalid URL');
  retJson($ret);
}
if ($pathParts[1] !== "v1") {
  $ret = array('status'=>'FAIL','msg'=>'Invalid URL or version');
  retJson($ret);
}

//get json data if any
$jsonData =array();
try {
  $rawData = file_get_contents("php://input");
  $jsonData = json_decode($rawData,true);
  if ($rawData !== "" && $jsonData==NULL) {
    $ret=array("status"=>"FAIL","msg"=>"invalid json");
    retJson($ret);
  }
} catch (Exception $e) {
};


// Look for url /v1/user. Post:
// Json in: user
// Json out: Status: Ok/Fail, array values
if ($method==="post" && count($pathParts) ==  3 && $pathParts[2] === "user") {
	if (!isset($jsonData['user']) || $jsonData['user'] ==="") {
		$ret = array('status'=>'FAIL','msg'=>'Missing user json');
		retJson($ret);
	}
	$user = $jsonData['user'];
	$data = getLikes($mysqli, $user);
	if (!getLikes($mysqli, $user)){
		$ret = array('status'=>'Error','msg'=>'No liked posts found for: ' . $user);
		retJson($ret);
	} else {
		$ret = array('status'=>'OK','posts' => $data);
		retJson($ret);
	}
}

// Look for url /v1/topLikes. Get:
// No Json In, Json Out: Status, message, items[] (pk and item)
if ($method==="get" && count($pathParts) ==  3 && $pathParts[2] === "topLikes") {
	
  	$ret = array('status'=>'OK','items' => getTopLikes($mysqli));
  	retJson($ret);
}

else {
  $ret = array('status'=>'FAIL','msg'=>'Invalid URL');
  retJson($ret);
}


?>
