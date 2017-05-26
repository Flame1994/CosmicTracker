<?php
	include "php/routes.php";
	session_start();
	$region = $_GET['region'];
	

	$conn = connect();
    $prepared = $conn->prepare("SELECT * FROM signatures WHERE region_name = ? AND sig_type = ?"); 
    $site = "Wormhole";
    $prepared->bind_param('ss', $region, $site);    
    $prepared->execute();
    $result = get_result($prepared);


    $return_arr = array();
	
    if ($prepared->num_rows == 0) {
        echo "NONE";
    }
    while ($row = array_shift($result)) {

        $row_array['system'] = $row['system'];	    
	    array_push($return_arr,$row_array);
    }      
    $conn->close();
	
	echo json_encode($return_arr);
?>