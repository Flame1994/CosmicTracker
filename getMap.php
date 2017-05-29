<?php
	include "php/routes.php";
	session_start();	
	if (isset($_SESSION['CharacterID']) && isset($_GET['system'])) {
		$conn = connect();
		$main_system_id = $_GET['system'];
		$prepared = $conn->prepare("SELECT * FROM neighbours WHERE neighbour_id = ?"); 
        $prepared->bind_param('s', $main_system_id);    
        $prepared->execute();
        $result = get_result($prepared);
        if ($prepared->num_rows == 0) {
        	$url = file_get_contents("https://esi.tech.ccp.is/dev/universe/systems/".$main_system_id."/?datasource=tranquility&language=en-us");
	        $content = json_decode($url, true);
			$const_id = $content['constellation_id'];
			$main_system_name = $content['name'];

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
	                echo '<div class="system current-system" id="'.$system['name'].'" style="position: absolute; left: '.($system['x']+36).'px; top: '.($system['y']+5).'px; width: 16px; height: 16px; cursor: pointer; z-index:23; background-color: #FFF;">
	                    <div class="system-name"><h5>'.$system['name'].'</h5></div>
	                </div>';
	            }
	            $connections = $content['map']['connections'];
	            foreach($connections as $connection) { 
	                echo '<script> drawConnection('.$connection['x1'].','.$connection['y1'].','.$connection['x2'].','.$connection['y2'].'); </script>';
	            }  
	            echo '</canvas>';
		    }

        } else {
        	while ($row = array_shift($result)) {
	        	$region_name = $row['region'];
	        	$region_id = $row['region_id'];
			}
			if ($region_name != $_SESSION['CharacterRegionName']) {
		    	$_SESSION["CharacterRegionName"] = $region_name;
				$_SESSION["CharacterRegionID"] = $region_id;	
				echo '<canvas id="canvas" width="1000" height="800"></canvas>';
				$r = str_replace(" ","_",$region_name);
				$url = file_get_contents("maps/".$r.".svg.json");				
	            $content = json_decode($url, true);
	            $systems = $content['map']['systems'];
	            foreach($systems as $system) {
	            	if ($system['id'] == $main_system_id) {
        		echo '	<div class="system current-system" id="'.$system['name'].'" onmouseover="showSystemInfo(\''.$system['name'].'\', \''.$system['id'].'\')" onmouseout="hideSystemInfo(\''.$system['name'].'\')" style="position: absolute; left: '.($system['x']+36).'px; top: '.($system['y']+5).'px; width: 16px; height: 16px; cursor: pointer; background-color: #337ab7;">
                			<div class="system-name"><h5>'.$system['name'].'</h5></div>
                			<div id="'.$system['name'].'-info" class="system-info-popup">
                				<div class="col-xs-12">
	                              <h5>'.$system['name'].'</h5>     
	                              <div id="'.$system['name'].'-sites"></div>                                         
	                              <button onclick="setDestenation('.$system['id'].')" class="btn btn-default btn-dest">Set Destination</button>
	                            </div>
                			</div>
            			</div>';
	            	} else {
        		echo '	<div class="system" id="'.$system['name'].'" onmouseover="showSystemInfo(\''.$system['name'].'\', \''.$system['id'].'\')" onmouseout="hideSystemInfo(\''.$system['name'].'\')" style="position: absolute; left: '.($system['x']+36).'px; top: '.($system['y']+5).'px; width: 16px; height: 16px; cursor: pointer; background-color: #FFF;">
                			<div class="system-name"><h5>'.$system['name'].'</h5></div>
                			<div id="'.$system['name'].'-info" class="system-info-popup">
                				<div class="col-xs-12">
                                  <h5>'.$system['name'].'</h5>         
                                  <div id="'.$system['name'].'-sites"></div>                                     
                                  <button onclick="setDestenation('.$system['id'].')" class="btn btn-default btn-dest">Set Destination</button>
                                </div>
                			</div>
            			</div>';	
	            	}                    
	                
	            }
	            $connections = $content['map']['connections'];
	            foreach($connections as $connection) { 
	                echo '<script> drawConnection('.$connection['x1'].','.$connection['y1'].','.$connection['x2'].','.$connection['y2'].'); </script>';
	            }  
	            echo '</canvas>';
		    }
        } 

        $conn->close();       
	} else {
		
	}
	
?>