<?php
	// ============================================================================
    // Handles character location. This script is called every 5 seconds to check
	// if user has entered a new system. If so, return the system id.
    // ============================================================================

	include "php/routes.php";
	session_start();
	$url = file_get_contents("https://crest-tq.eveonline.com/");
    $content = json_decode($url, true);

    // Check if eve is online
    $serviceStatus =  $content['serviceStatus'];
    $serverCount =  $content['userCount_str'];
    if ($serviceStatus != 'online') {
      echo "logout";
    } else {    	
    	if (isset($_SESSION['CharacterID'])) {
    		// Get character location
			$charid = $_SESSION['CharacterID'];
			$access = $_SESSION['AccessToken'];
			$url2 = 'https://crest-tq.eveonline.com/characters/'.$charid.'/location/';
			
			$options2 = array(
			    'http' => array(
			        'header'  => "Authorization: Bearer ".$access."\r\n",
			        'method'  => 'GET'
			    )
			);
			$context2  = stream_context_create($options2);
			$result2 = file_get_contents($url2, false, $context2);		
		    if ($result2 === FALSE) {
		    	$_SESSION["CharacterSystemName"] = "";
				$_SESSION["CharacterSystemID"] = "";
		    } else {
		    	$content2 = json_decode($result2, true);	    	
				if ($content2 === FALSE || empty($content2) || is_null($content2)) {
					$_SESSION["CharacterSystemName"] = "";
					$_SESSION["CharacterSystemID"] = "";			
				} else {
					// Set and save location
					$main_system_id = $content2['solarSystem']['id'];
					$main_system_name = $content2['solarSystem']['name'];
					$system_href = $content2['solarSystem']['href'];
					if ($main_system_name != $_SESSION["CharacterSystemName"]) {
						$_SESSION["CharacterSystemName"] = $main_system_name;
						$_SESSION["CharacterSystemID"] = $main_system_id;
						echo $main_system_id;
					} else {

					}
				}
			}
		}
    }
	
?>