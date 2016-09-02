<?php

	$city_name = ucwords($_GET['city_name']);
	
	include('database.php');

	$url_head = "http://makeadiff.in/apps/profile/create_card.php?user_id=";
	
	// Create connection
	$conn = new mysqli($servername, $username, $password,$dbname);

	if ($conn->connect_error) {
	    die("Connection failed: " .$conn->connect_error);
	} 
	else{
		//echo "Connected successfully";
	}

	$query = "Select User.id as user_id,User.name as user_name,City.name as city_name from User inner join City on City.id = User.city_id where User.status = 1 and User.user_type = 'volunteer' and City.name = '".$city_name."'";
	$result = $conn->query($query);
	//var_dump($result);

	mkdir($city_name,0777);
	$files = array();

	if ($result->num_rows > 0){
  	    while($row = $result->fetch_assoc()) {
	        //echo "User ID: " . $row["user_id"]. " | Name: " . $row["user_name"]. " | City Name: ".$row["city_name"]. " ". "<br>";
	        $url = $url_head.$row["user_id"];
	        //echo $url;

	        $img = './'.$city_name.'/'.str_replace(' ', '_', ucwords($row["user_name"])).'.png';
			file_put_contents($img, file_get_contents($url));
	    }
	} 
	else {
	    echo "0 results";
	}

	$zipname = $city_name.'-MAD-CRED.zip';
    $zip = new ZipArchive;
    $zip->open($zipname, ZipArchive::CREATE);
    foreach (glob($city_name."/*.png") as $file) { /* Add appropriate path to read content of zip */
        $zip->addFile($file);
    }
    $zip->close();

    header('Content-Type: application/zip');
    header("Content-Disposition: attachment; filename='adcs.zip'");
    header('Content-Length: ' . filesize($zipname));
	header("Location: $zipname");

	$conn->close();
	
?>