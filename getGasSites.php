<?php
	include "php/routes.php";
	session_start();
    if (isset($_SESSION['CharacterRegionName'])) {
    	$region = $_SESSION['CharacterRegionName'];
    	

    	$conn = connect();
        $prepared = $conn->prepare("SELECT * FROM signatures WHERE region_name = ? AND sig_type = ?"); 
        $site = "Gas Site";
        $prepared->bind_param('ss', $region, $site);    
        $prepared->execute();
        $result = get_result($prepared);


        $return_arr = array();
    	
        if ($prepared->num_rows == 0) {
            echo "NONE";
        } else {
            while ($row = array_shift($result)) {
                $row_array['system'] = $row['system'];      
                array_push($return_arr,$row_array);
            }      
            $conn->close();
            
            echo json_encode($return_arr);    
        }
    }
    
?>