<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 'On');
	include "php/routes.php";
	session_start();
	
	
	if (isset($_SESSION['CharacterID']) && isset($_GET['system'])) {
		$conn = connect();
		$main_system_id = "".$_GET['system']."";
		$prepared = $conn->prepare("SELECT * FROM neighbours WHERE system_id = ?"); 
        $prepared->bind_param('s', $main_system_id);    
        $prepared->execute();
        $result = get_result($prepared);
        if ($prepared->num_rows == 0) {        	
            // SYSTEM NOT YET IN DATABASE;
            $url = file_get_contents("https://esi.tech.ccp.is/dev/universe/systems/".$_GET['system']."/?datasource=tranquility&language=en-us");
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
							
			echo '
			
            		            
	            <div class="col-xs-12" style="background-color: #333;">
	                <h2>Current System: <span class="text-primary">';
	                
	                    if (isset($_SESSION["CharacterSystemName"])) {
	                        echo $_SESSION["CharacterSystemName"] ;
	                        echo '<h5 style="color:white;">'.$main_system_name.' <span class="security-status"> '.round($content['security_status'],1).'</span> &lsaquo; '.$const_name.' &lsaquo; '.$region_name.'</h5>';
	                    } else {
	                        echo "Not Found";
	                    }
	                    

	                echo '</span></h2>
	            </div>
	            <div class="col-xs-12" style="height: 200px;">
	                <h6>Add Signatures</h6>
	                <form id="add-sig" action="signature" enctype="multipart/form-data" method="post">
	                    <textarea id="sigdata" name="sigdata" placeholder="Add signature results" style="width:100%; color:#333333; font-size:12px" rows="4"></textarea>
	                    <button type="submit" name="Submit" id="Submit" class="btn btn-default">Submit</button>
	                </form>
	            </div>
	            <div class="col-xs-12 signature-list">
	                <table>
	                    <tr>
	                        <th>System</th>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Signature Name</th>
                            <th>Explorer</th>
                            <th>Time</th>
                            <th></th>
	                    </tr>';
	                    
	                    	$conn2 = connect();
	                        $prepared2 = $conn2->prepare("SELECT * FROM signatures WHERE system_id = ?"); 
	                        $prepared2->bind_param('s', $main_system_id);    
	                        $prepared2->execute();
	                        $result2 = get_result($prepared2);
	                        while ($row2 = array_shift($result2)) {
	                            $system = $row2['system'];
	                            $sigID = $row2['sig_id'];
	                            $sigType = $row2['sig_type'];
	                            $sigName = $row2['sig_name'];
	                            $reported = $row2['reported'];
	                            $reportedID = $row2['reported_id'];
	                            $reportTime = $row2['report_time'];
	                            echo '
	                                <tr>
	                                    <td>'.$system.'</td>
	                                    <td>'.$sigID.'</td>
	                                    <td>'.$sigType.'</td>
	                                    <td>'.$sigName.'</td>
	                                    <td><a href="https://zkillboard.com/character/'.$reportedID.'/">'.$reported.'</a></td>
	                                    <td>'.$reportTime.'</td>
	                                    <td>
                                            <form id="sig-action-form" action="index.php/delete-signature" method="post" style="margin: 0;">
                                              <input type="hidden" name="sig_id" value="'.$sigID.'">
                                              <button type="submit" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Delete Signature" name="del_sig" value="Delete Sig" style="padding-left:6px;">
                                                <span><i class="fa fa-times" aria-hidden="true"></i></span>
                                              </button>
                                          	</form>
                                        </td>
	                                </tr>
	                            ';
	                            
	                        }   
	                        $conn2->close();       
	                        
	                 
	        echo '  </table>
	            </div>
	            <div class="col-xs-12 intel">
	                <h4>Neighbouring Systems</h4>
	                <hr style="padding: 0; margin: 0;">';                            
	                    $url = file_get_contents("https://esi.tech.ccp.is/dev/universe/systems/".$_GET['system']."/?datasource=tranquility&language=en-us");
	                    $content = json_decode($url, true);
	                    $stargates = $content['stargates'];
	                    foreach ($stargates as $stargate) {
	                        $url2 = file_get_contents("https://esi.tech.ccp.is/dev/universe/stargates/".$stargate."/?datasource=tranquility");
	                        $content2 = json_decode($url2, true);

	                        $destination = $content2['destination']['system_id'];

	                        $url3 = file_get_contents("https://esi.tech.ccp.is/dev/universe/systems/".$destination."/?datasource=tranquility&language=en-us");
	                        $content3 = json_decode($url3, true);
	                        $system_name = $content3['name'];
                            $system_id = $content3['system_id'];                            
                            $sec_status = round($content3['security_status'],1);
                            $const_id = $content3['constellation_id'];

                            $url2 = file_get_contents("https://esi.tech.ccp.is/latest/universe/constellations/".$const_id."/?datasource=tranquility&language=en-us");
                            $content2 = json_decode($url2, true);      
                            
                            $const_name = $content2['name'];
                            $region_id = $content2['region_id'];
                            $url3_1 = file_get_contents("https://esi.tech.ccp.is/latest/universe/regions/".$region_id."/?datasource=tranquility&language=en-us");
                        	$content3_1 = json_decode($url3_1, true);   
                        	$region_name = $content3_1['name'];

                            $url4 = file_get_contents("https://esi.tech.ccp.is/latest/universe/system_kills/?datasource=tranquility");
                            $content4 = json_decode($url4, true);
                            $foundKills = false;
                            foreach($content4 as $systemKills) { 
                                if ($systemKills['system_id'] == $system_id) {
                                    $foundKills = true;		                                    
                                    $kills = (int)$systemKills['ship_kills']; 
                                    $npc_kills = (int)$systemKills['npc_kills']; 
                                    $pod_kills = $systemKills['pod_kills']; 
                                    $ship_kills = $kills - $npc_kills;
                                }                                
                            }  

                            if ($foundKills == false) {
                                $kills = 0; 
                                $npc_kills = 0; 
                                $pod_kills = 0; 
                                $ship_kills = 0;
                            }

                            $foundJumps = false;
                            $url5 = file_get_contents("https://esi.tech.ccp.is/latest/universe/system_jumps/?datasource=tranquility");
                            $content5 = json_decode($url5, true);
                            foreach($content5 as $systemJumps) { 
                                if ($systemJumps['system_id'] == $system_id) {
                                    $foundJumps = true;
                                    $jumps = $systemJumps['ship_jumps'];                                     
                                }                                
                            } 

                            if ($foundJumps == false) {
                                $jumps = 0;
                            } 

                            // INSERT NEIGHBOUR SYSTEMS TO DATABASE
                            $conn3 = connect();
                            $prepared3 = $conn3->prepare("SELECT * FROM neighbours WHERE system_id = ? AND neighbour = ?"); 
                            $prepared3->bind_param('ss', $main_system_id, $system_name);    
                            $prepared3->execute();
                            $result3 = get_result($prepared3);
                            if ($prepared3->num_rows == 0) {
                                $prepared3_1 = $conn3->prepare("INSERT INTO `neighbours` (`id`, `system`, `system_id`, `neighbour`, `neighbour_id`, `sec_status`, `const`, `const_id`, `region`, `region_id`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
								$entryVal = NULL;		
								$prepared3_1->bind_param('ssssssssss', $entryVal, $main_system_name, $main_system_id, $system_name, $system_id, $sec_status, $const_name, $const_id, $region_name, $region_id);
								$prepared3_1->execute();
                            }
                            
                            $conn3->close();  

	                        echo '
	                            
	                            <div class="col-xs-12 intel-system">
	                                <h5>'.$system_name.' <span class="security-status"> '.$sec_status.'</span> &lsaquo; '.$const_name.' &lsaquo; '.$region_name.'</h5>
	                                <div class="col-xs-6">
	                                    <table>';	
	                                    $conn4 = connect();			                                    
	                                    $prepared4 = $conn4->prepare("SELECT * FROM signatures WHERE system = ?"); 
	                                    $prepared4->bind_param('s', $content3['name']);    
	                                    $prepared4->execute();
	                                    $result4 = get_result($prepared4);
	                                    if ($prepared4->num_rows == 0) {
	                                        echo "No Signatures Found";
	                                    }
	                                    while ($row4 = array_shift($result4)) {
	                                        $system = $row4['system'];
	                                        $sigID = $row4['sig_id'];
	                                        $sigType = $row4['sig_type'];
	                                        $sigName = $row4['sig_name'];
	                                        $reported = $row4['reported'];
	                                        $reportedID = $row4['reported_id'];
	                                        $reportTime = $row4['report_time'];
	                                        echo '
	                                            <tr>
	                                                <td>'.$sigID.'</td>
	                                                <td>'.$sigType.'</td>
	                                                <td>'.$sigName.'</td>
	                                            </tr>
	                                        ';
	                                        
	                                    }  
	                                    $conn4->close();      
	                                    
	                        echo '      </table>
	                                </div>
	                                <div class="col-xs-6">
                                        <h5>Intel (1h)</h5>
                                        <table>
                                            <tr>
                                                <td>'.$jumps.'</td>
                                                <td>Jumps</td>
                                            </tr>
                                            <tr>
                                                <td>'.$ship_kills.'</td>
                                                <td>Ship Kills</td>
                                            </tr>
                                            <tr>
                                                <td>'.$pod_kills.'</td>
                                                <td>Pod Kills</td>
                                            </tr>
                                            <tr>
                                                <td>'.$npc_kills.'</td>
                                                <td>Rat Kills</td>
                                            </tr>
                                        </table>
                                    </div>                          
	                            </div>
	                        ';
	                        
	                    }
	        echo '    
	            </div>		        
	            <script type="text/javascript">	      
	            	$(".system").css({"background-color":"#fff"});
		            $("#';echo $_SESSION["CharacterSystemName"]; echo '").css({"background-color":"#337ab7"});
		            if (queue[queue.length-1] != "'.$_SESSION["CharacterSystemName"].'") {
		            	queue.push("'; echo $_SESSION["CharacterSystemName"]; echo'");
		            }
                    
		        </script>
			';
        } else {        	
        	$conn0 = connect();
        	$prepared0 = $conn->prepare("SELECT * FROM neighbours WHERE neighbour_id = ?"); 
	        $prepared0->bind_param('s', $main_system_id);    
	        $prepared0->execute();
	        $result0 = get_result($prepared0);
	        while ($row0 = array_shift($result0)) {
	        	$sec_status = $row0['sec_status'];
	        	$main_system_name = $row0['neighbour'];
	        	$const_name = $row0['const'];
	        	$region_name = $row0['region'];
	        }
	        $conn0->close();   
        	echo '          
	            <div class="col-xs-12" style="background-color: #333;">
	                <h2>Current System: <span class="text-primary">';				                
	                    if (isset($_SESSION["CharacterSystemName"])) {
	                        echo $_SESSION["CharacterSystemName"] ;
	                        echo '<h5 style="color:white;">'.$main_system_name.' <span class="security-status"> '.$sec_status.'</span> &lsaquo; '.$const_name.' &lsaquo; '.$region_name.'</h5>';
	                    } else {
	                        echo "Not Found";
	                    }
	                    

	                echo '</span></h2>
	            </div>
	            <div class="col-xs-12" style="height: 200px;">
	                <h6>Add Signatures</h6>
	                <form id="add-sig" action="signature" enctype="multipart/form-data" method="post">
	                    <textarea id="sigdata" name="sigdata" placeholder="Add signature results" style="width:100%; color:#333333; font-size:12px" rows="4"></textarea>
	                    <button type="submit" name="Submit" id="Submit" class="btn btn-default">Submit</button>
	                </form>
	            </div>
	            <div class="col-xs-12 signature-list">
	                <table>
	                    <tr>
	                        <th>System</th>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Signature Name</th>
                            <th>Explorer</th>
                            <th>Time</th>
                            <th></th>
	                    </tr>';
	                    
	                    	$conn2 = connect();
	                        $prepared2 = $conn2->prepare("SELECT * FROM signatures WHERE system_id = ?"); 
	                        $prepared2->bind_param('s', $main_system_id);    
	                        $prepared2->execute();
	                        $result2 = get_result($prepared2);
	                        while ($row2 = array_shift($result2)) {
	                            $system = $row2['system'];
	                            $sigID = $row2['sig_id'];
	                            $sigType = $row2['sig_type'];
	                            $sigName = $row2['sig_name'];
	                            $reported = $row2['reported'];
	                            $reportedID = $row2['reported_id'];
	                            $reportTime = $row2['report_time'];
	                            echo '
	                                <tr>
	                                    <td>'.$system.'</td>
	                                    <td>'.$sigID.'</td>
	                                    <td>'.$sigType.'</td>
	                                    <td>'.$sigName.'</td>
	                                    <td><a href="https://zkillboard.com/character/'.$reportedID.'/">'.$reported.'</a></td>
	                                    <td>'.$reportTime.'</td>
	                                    <td>
                                            <form id="sig-action-form" action="index.php/delete-signature" method="post" style="margin: 0;">
                                              <input type="hidden" name="sig_id" value="'.$sigID.'">
                                              <button type="submit" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Delete Signature" name="del_sig" value="Delete Sig" style="padding-left:6px;">
                                                <span><i class="fa fa-times" aria-hidden="true"></i></span>
                                              </button>
                                          	</form>
                                        </td>
	                                </tr>
	                            ';
	                            
	                        }   
	                        $conn2->close();       
	                        
	                 
	        echo '  </table>
	            </div>';
         echo ' <div class="col-xs-12 intel">
                	<h4>Neighbouring Systems</h4>
                	<hr style="padding: 0; margin: 0;">';                  	
            while ($row = array_shift($result)) {
            	$system_name = $row['system'];
            	$system_id = $row['system_id'];
            	$neighbour_name = $row['neighbour'];
            	$neighbour_id = $row['neighbour_id'];
            	$sec_status = $row['sec_status'];
            	$const_name = $row['const'];
            	$const_id = $row['const_id'];
            	$region_name = $row['region'];
            	$region_id = $row['region_id'];

            	$url4 = file_get_contents("https://esi.tech.ccp.is/latest/universe/system_kills/?datasource=tranquility");
                $content4 = json_decode($url4, true);
                $foundKills = false;
                foreach($content4 as $systemKills) { 
                    if ($systemKills['system_id'] == $system_id) {
                        $foundKills = true;		                                    
                        $kills = (int)$systemKills['ship_kills']; 
                        $npc_kills = (int)$systemKills['npc_kills']; 
                        $pod_kills = $systemKills['pod_kills']; 
                        $ship_kills = $kills - $npc_kills;
                    }                                
                }  

                if ($foundKills == false) {
                    $kills = 0; 
                    $npc_kills = 0; 
                    $pod_kills = 0; 
                    $ship_kills = 0;
                }

                $foundJumps = false;
                $url5 = file_get_contents("https://esi.tech.ccp.is/latest/universe/system_jumps/?datasource=tranquility");
                $content5 = json_decode($url5, true);
                foreach($content5 as $systemJumps) { 
                    if ($systemJumps['system_id'] == $neighbour_id) {
                        $foundJumps = true;
                        $jumps = $systemJumps['ship_jumps'];                                     
                    }                                
                } 

                if ($foundJumps == false) {
                    $jumps = 0;
                } 

                echo '
	                            
                    <div class="col-xs-12 intel-system">
                        <h5>'.$neighbour_name.' <span class="security-status"> '.$sec_status.'</span> &lsaquo; '.$const_name.' &lsaquo; '.$region_name.'</h5>
                        <div class="col-xs-6">
                            <table>';	
                            $conn4 = connect();			                                    
                            $prepared4 = $conn4->prepare("SELECT * FROM signatures WHERE system = ?"); 
                            $prepared4->bind_param('s', $neighbour_name);    
                            $prepared4->execute();
                            $result4 = get_result($prepared4);
                            if ($prepared4->num_rows == 0) {
                                echo "No Signatures Found";
                            }
                            while ($row4 = array_shift($result4)) {
                                $system = $row4['system'];
                                $sigID = $row4['sig_id'];
                                $sigType = $row4['sig_type'];
                                $sigName = $row4['sig_name'];
                                $reported = $row4['reported'];
                                $reportedID = $row4['reported_id'];
                                $reportTime = $row4['report_time'];
                                echo '
                                    <tr>
                                        <td>'.$sigID.'</td>
                                        <td>'.$sigType.'</td>
                                        <td>'.$sigName.'</td>
                                    </tr>
                                ';
                                
                            }  
                            $conn4->close();      
                            
                echo '      </table>
                        </div>
                        <div class="col-xs-6">
                            <h5>Intel (1h)</h5>
                            <table>
                                <tr>
                                    <td>'.$jumps.'</td>
                                    <td>Jumps</td>
                                </tr>
                                <tr>
                                    <td>'.$ship_kills.'</td>
                                    <td>Ship Kills</td>
                                </tr>
                                <tr>
                                    <td>'.$pod_kills.'</td>
                                    <td>Pod Kills</td>
                                </tr>
                                <tr>
                                    <td>'.$npc_kills.'</td>
                                    <td>Rat Kills</td>
                                </tr>
                            </table>
                        </div>                          
                    </div>
                ';
            }

    		echo '    
	            </div>		        
	            <script type="text/javascript">	      
	            	$(".system").css({"background-color":"#fff"});
		            $("#';echo $_SESSION["CharacterSystemName"]; echo '").css({"background-color":"#337ab7"});

		            if (queue[queue.length-1] != "'.$_SESSION["CharacterSystemName"].'") {
		            	queue.push("'; echo $_SESSION["CharacterSystemName"]; echo'");
		            }                    
		        </script>
			';
        } 

        $conn->close();               
	}
	

	
?>