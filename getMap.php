<?php
	include "php/routes.php";
	session_start();	
	if (isset($_SESSION['CharacterID'])) {
		$charid = $_SESSION['CharacterID'];
		$access = $_SESSION['AccessToken'];
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
	    if ($result2 === FALSE) {
	    	echo "NONE";
	    } else {
	    	$content2 = json_decode($result2, true);

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

			    if ($region_name != $_SESSION['CharacterRegionName']) {
			    	$_SESSION["CharacterRegionName"] = $region_name;
					$_SESSION["CharacterRegionID"] = $region_id;	
					echo '<canvas id="canvas" width="1000" height="800"></canvas>';
					$r = str_replace(" ","_",$region_name);
					$url = file_get_contents("maps/".$r.".svg.json");				
	                $content = json_decode($url, true);
	                $systems = $content['map']['systems'];
	                foreach($systems as $system) {                     
	                    echo '<div class="system" id="'.$system['name'].'" style="position: absolute; left: '.($system['x']+36).'px; top: '.($system['y']+5).'px; width: 16px; height: 16px; cursor: pointer; z-index:23; background-color: #FFF;">
	                        <div class="system-name"><h5>'.$system['name'].'</h5></div>
	                    </div>';
	                }
	                $connections = $content['map']['connections'];
	                foreach($connections as $connection) { 
	                    echo '<script> drawConnection('.$connection['x1'].','.$connection['y1'].','.$connection['x2'].','.$connection['y2'].'); </script>';
	                }  
	                echo '</canvas>';
			    }
			}
		}	
	}
		
?>