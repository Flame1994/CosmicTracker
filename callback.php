<?php	
	require_once("./php/config.php");
	// ============================================================================
	// Handles the callback from the EVE SSO
	// ============================================================================

	include "php/connect.php";	
	$url = 'https://login.eveonline.com/oauth/token';
	if (isset($_GET['code'])) {
		$code = $_GET['code'];
		$data = array('grant_type' => 'authorization_code', 'code' => $code);
		
		// Request Auth
        $key = base64_encode($GLOBALS["config"]["app"]["client_id"]
                             .':'
                             .$GLOBALS["config"]["app"]["secret_key"]);
		$options = array(
		    'http' => array(
		        'header'  => "Authorization: Basic ".$key."\r\nContent-type: application/x-www-form-urlencoded\r\n",
		        'method'  => 'POST',
		        'content' => http_build_query($data)
		    )
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		if ($result === FALSE) {
		} else {
			$content = json_decode($result, true);
			$access = $content['access_token'];
			$refresh = $content['refresh_token'];
			
			// Verify Auth
			$url = 'https://login.eveonline.com/oauth/verify';
			$options = array(
			    'http' => array(
			        'header'  => "Authorization: Bearer ".$access."\r\n",
			        'method'  => 'GET'
			    )
			);
			$context  = stream_context_create($options);
			$result = file_get_contents($url, false, $context);
			if ($result === FALSE) {				
			} else {

				$url = file_get_contents("https://crest-tq.eveonline.com/");
	            $content = json_decode($url, true);

                $serviceStatus =  $content['serviceStatus'];
                $serverCount =  $content['userCount_str'];

                // Check if eve is online
                if ($serviceStatus == 'online') {
                	session_write_close();
					session_start(); 								
					$content = json_decode($result, true);
					$name = $content['CharacterName'];
					$charid = $content['CharacterID'];
					$hash = $content['CharacterOwnerHash'];

					// Get location
					$url2 = 'https://crest-tq.eveonline.com/characters/'.$charid.'/location/';
					$options2 = array(
					    'http' => array(
					        'header'  => "Authorization: Bearer ".$access."\r\n",
					        'method'  => 'GET'
					    )
					);
					$context2  = stream_context_create($options2);
					$result2 = file_get_contents($url2, false, $context2);
					$content2 = json_decode($result2, true);

					// Get info
					$url = file_get_contents("https://esi.tech.ccp.is/latest/characters/".$charid."/?datasource=tranquility");
			        $content = json_decode($url, true);      
			        $corp_id = $content['corporation_id'];
			        $alliance_id = $content['alliance_id'];
			        
			        $url = file_get_contents("https://esi.tech.ccp.is/latest/corporations/".$corp_id."/?datasource=tranquility");
			        $content = json_decode($url, true);      
			        $corp_name = $content['corporation_name'];

			        $url = file_get_contents("https://esi.tech.ccp.is/latest/alliances/".$alliance_id."/?datasource=tranquility");
			        $content = json_decode($url, true);      
			        $alliance_name = $content['alliance_name'];
			        $alliance_ticker = $content['ticker'];
					if ($content2 === FALSE || empty($content2) || is_null($content2)) {						
					} else {
						$system_id = $content2['solarSystem']['id'];
						$system_name = $content2['solarSystem']['name'];
						$system_href = $content2['solarSystem']['href'];

						$url = file_get_contents("https://esi.tech.ccp.is/dev/universe/systems/".$system_id."/?datasource=tranquility&language=en-us");
				        $content = json_decode($url, true);      
				        $const_id = $content['constellation_id'];

				        $url2 = file_get_contents("https://esi.tech.ccp.is/latest/universe/constellations/".$const_id."/?datasource=tranquility&language=en-us");
				        $content2 = json_decode($url2, true);      
				        
				        $const_name = $content2['name'];
				        $region_id = $content2['region_id'];
				        $url3 = file_get_contents("https://esi.tech.ccp.is/latest/universe/regions/".$region_id."/?datasource=tranquility&language=en-us");
				        $content3 = json_decode($url3, true);   
				        $region_name = $content3['name'];				        

				        // Check if user is in database
				        $conn = connect();
				        $prepared = $conn->prepare("SELECT * FROM users WHERE user = ?"); 
		                $prepared->bind_param('s', $name);    
		                $prepared->execute();
		                $result = get_result($prepared);
						if ($prepared->num_rows == 0) {	
							if ($alliance_name == '' || is_null($alliance_name) || empty($alliance_name)) {
								$alliance_name = '';
								$alliance_id = '';
							}
							// Add user if not
							$prepared2 = $conn->prepare("INSERT INTO `users` (`id`, `user`, `user_id`, `corp`, `corp_id`, `alliance`, `alliance_id`, `relic_sites`, `data_sites`, `gas_sites`, `combat_sites`, `wormholes`, `total_scanned`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"); 
							$entryVal = NULL;		
							$nullVal = 0;
							$prepared2->bind_param('sssssssdddddd', $entryVal, $name, $charid, $corp_name, $corp_id, $alliance_name, $alliance_id, $nullVal, $nullVal, $nullVal, $nullVal, $nullVal, $nullVal);
							$prepared2->execute();							
						} else {
							// Update user if exists
							$prepared2 = $conn->prepare("UPDATE `users` SET corp = ?, corp_id = ?, alliance = ?, alliance_id = ? WHERE user = ?"); 
						    $prepared2->bind_param('sssss', $corp_name, $corp_id, $alliance_name, $alliance_id, $name);    
						    $prepared2->execute();
						}
						$conn->close();

						// Set session variables
						$_SESSION["CharacterSystemID"] = $system_id;
						$_SESSION["CharacterSystemName"] = $system_name;
						$_SESSION["CharacterRegionID"] = $region_id;
						$_SESSION["CharacterRegionName"] = $region_name;
					}	

					// Set session variables
					$_SESSION["AccessToken"] = $access;
					$_SESSION["RefreshToken"] = $refresh;	
					$_SESSION["CharacterOwnerHash"] = $hash;
					$_SESSION["CharacterID"] = $charid;
					$_SESSION["CharacterName"] = $name;
					$_SESSION["CharacterCorp"] = $corp_name;
					$_SESSION["CharacterCorpID"] = $corp_id;
					$_SESSION["CharacterAlliance"] = $alliance_name;
					$_SESSION["CharacterAllianceID"] = $alliance_id;
                } else {    	
                }
			}
		}
		print_r($_SESSION);
		header('Location: '. $GLOBALS["config"]["app"]["root_dir"] .'/home');
	}
	

?>
