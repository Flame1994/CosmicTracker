<?php	
	error_reporting(E_ALL);
	ini_set('display_errors', 'On');
	include "php/connect.php";	
	$url = 'https://login.eveonline.com/oauth/token';
	if (isset($_GET['code'])) {
		echo "Code is there";
		echo "<br>";
		$code = $_GET['code'];
		echo $code;
		echo "<br>";
		$data = array('grant_type' => 'authorization_code', 'code' => $code);

		// use key 'http' even if you send the request to https://...
		echo "<br>";
		$key = base64_encode('86fe2014301a423e9f9a4df3c44f24b1:B54yYfQbuBtBYnqSG6tymVvapyK8ek1Alt5T56SG');
		$options = array(
		    'http' => array(
		        'header'  => "Authorization: Basic ".$key."\r\nContent-type: application/x-www-form-urlencoded\r\n",
		        'method'  => 'POST',
		        'content' => http_build_query($data)
		    )
		);
		var_dump($options);
		echo "<br>";
		echo "<br>";
		$context  = stream_context_create($options);
		var_dump($context);
		echo "<br>";
		echo "<br>";
		$result = file_get_contents($url, false, $context);
		var_dump($result);
		if ($result === FALSE) {
			echo "Result is false";
		} else {
			$content = json_decode($result, true);
			echo "<br>";
			echo $content['access_token'];
			echo "<br>";
			echo $content['refresh_token'];
			$access = $content['access_token'];
			$refresh = $content['refresh_token'];
			
			// exit;
			$url = 'https://login.eveonline.com/oauth/verify';

			// use key 'http' even if you send the request to https://...

			// $key = 'yls9vXaxcquP0OnAsUMkGQETSngEN-1e9xcWHWjdCvE4H-OPhMv7TMDe2AefIsLy2rdAIQz-kKypS1u1Bu8Z2w2';
			$options = array(
			    'http' => array(
			        'header'  => "Authorization: Bearer ".$access."\r\n",
			        'method'  => 'GET'
			    )
			);
			$context  = stream_context_create($options);
			$result = file_get_contents($url, false, $context);
			
			if ($result === FALSE) {
				echo "Can't verify";
			} else {

				$url = file_get_contents("https://crest-tq.eveonline.com/");
	            $content = json_decode($url, true);

                $serviceStatus =  $content['serviceStatus'];  
                $serverCount =  $content['userCount_str'];   
                if ($serviceStatus == 'online') {
                	session_write_close();
					session_start(); 								
					$content = json_decode($result, true);
					echo "</br>";
					echo $content['CharacterID'];
					$name = $content['CharacterName'];
					$charid = $content['CharacterID'];
					$hash = $content['CharacterOwnerHash'];


					$url2 = 'https://crest-tq.eveonline.com/characters/'.$charid.'/location/';

					// use key 'http' even if you send the request to https://...

					// $key = 'yls9vXaxcquP0OnAsUMkGQETSngEN-1e9xcWHWjdCvE4H-OPhMv7TMDe2AefIsLy2rdAIQz-kKypS1u1Bu8Z2w2';
					$options2 = array(
					    'http' => array(
					        'header'  => "Authorization: Bearer ".$access."\r\n",
					        'method'  => 'GET'
					    )
					);
					$context2  = stream_context_create($options2);
					$result2 = file_get_contents($url2, false, $context2);
					$content2 = json_decode($result2, true);

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

							$prepared2 = $conn->prepare("INSERT INTO `users` (`id`, `user`, `user_id`, `corp`, `corp_id`, `alliance`, `alliance_id`, `relic_sites`, `data_sites`, `gas_sites`, `combat_sites`, `wormholes`, `total_scanned`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"); 
							$entryVal = NULL;		
							$nullVal = 0;
							$prepared2->bind_param('sssssssdddddd', $entryVal, $name, $charid, $corp_name, $corp_id, $alliance_name, $alliance_id, $nullVal, $nullVal, $nullVal, $nullVal, $nullVal, $nullVal);
							$prepared2->execute();							
						} else {
							$prepared2 = $conn->prepare("UPDATE `users` SET corp = ?, corp_id = ?, alliance = ?, alliance_id = ? WHERE user = ?"); 
						    $prepared2->bind_param('sssss', $corp_name, $corp_id, $alliance_name, $alliance_id, $name);    
						    $prepared2->execute();
						}
						$conn->close();
						$_SESSION["CharacterSystemID"] = $system_id;
						$_SESSION["CharacterSystemName"] = $system_name;
						$_SESSION["CharacterRegionID"] = $region_id;
						$_SESSION["CharacterRegionName"] = $region_name;
					}	
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
        			// header('Location: '.'/home?e=cio');        	
                }
			}
		}
		
		header('Location: '.'/home');
	}
	

?>