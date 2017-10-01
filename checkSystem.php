<?php
	// ============================================================================
	// Updates the website if a player enters a new solar system
	// ============================================================================

	include "php/routes.php";
	session_start();
	// Check if required parameters are set
	if (isset($_SESSION['CharacterID']) && isset($_GET['system'])) {

		// update and store neighbouring systems on each solar system a player visits
		$conn = connect();
		$main_system_id = "".$_GET['system']."";
		$prepared = $conn->prepare("SELECT * FROM neighbours WHERE system_id = ?"); 
        $prepared->bind_param('s', $main_system_id);    
        $prepared->execute();
        $result = get_result($prepared);
        if ($prepared->num_rows == 0) {
            // System not yet in database

            // Get information about system
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
	                <script>
                            $(function () {

                              $(\'#add-sig\').on(\'submit\', function (e) {

                                e.preventDefault();          
                                $.ajax({
                                  type: \'POST\',
                                  url: \'/sigadd.php\',
                                  data: $(\'#add-sig\').serialize(),
                                  success: function (data) {
                                    if (data == "false") {
                                      $(\'#sig-report\').html("<div class=\"col-md-2\"></div>\
                                                <div class=\"col-xs-12 col-md-8 notification-container notification-failed\">\
                                                  <div class=\"notification-left\"><i class=\"fa fa-times\" aria-hidden=\"true\"></i></div>\
                                                  <div class=\"notification-right\"><div class=\"notification-note\">A signature failed to add.</div></div>\
                                                </div>");
                                    } else if (data == "true") {
                                      $(\'#sig-report\').html("<div class=\"col-md-2\"></div>\
                                                <div class=\"col-xs-12 col-md-8 notification-container notification-success\">\
                                                  <div class=\"notification-left\"><i class=\"fa fa-times\" aria-hidden=\"true\"></i></div>\
                                                  <div class=\"notification-right\"><div class=\"notification-note\">All Signatures added.</div></div> \
                                                </div>");

                                      $.ajax({
                                          url:\'/getSystemSigs.php\',
                                          type:\'GET\',                    
                                          beforeSend:function () {

                                          },
                                          success:function (data) {                                            
                                            $(".sys-info-list").html("");
                                            $(".sys-info-list").html(data);                                                      
                                              
                                          }
                                      });     
                                    }
                                  }
                                });

                              });

                            });
                          </script>
	            </div>
	            <div id="sig-report" class="col-xs-12">

                </div>
	            <div class="col-xs-12 signature-list sys-info-list">
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
	                    	// Display signatures of system
	                    	$conn2 = connect();
	                        $prepared2 = $conn2->prepare("SELECT * FROM signatures WHERE system_id = ?"); 
	                        $prepared2->bind_param('s', $main_system_id);    
	                        $prepared2->execute();
	                        $result2 = get_result($prepared2);
	                        $count = 0;
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
                                            <form id="delete-sig-'.$count.'" class="delete-sig" enctype="multipart/form-data" method="post">
				                                  <input type="hidden" name="sig_id" value="'.$sigID.'">
				                                  <button id="Submit" type="submit" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Delete Signature" name="Submit" value="Delete Sig" style="padding-left:6px;">
				                                    <span><i class="fa fa-times" aria-hidden="true"></i></span>
				                                  </button>                                  
				                              </form>          
                                        </td>
	                                </tr>
	                            ';
	                            $count = $count + 1;
	                        }   
	                        $conn2->close();
	        echo '  </table>
	        		<script>
				      $(function () {

				        $(\'.delete-sig\').on(\'submit\', function (e) {	          
				          e.preventDefault();	         
				          $.ajax({
				            type: \'POST\',
				            url: \'/sigdelete.php\',
				            data: $(\'#\'+this.id).serialize(),
				            success: function (data) {
				              if (data == "false") {
				                
				              } else if (data == "true") {
				                $.ajax({
				                    url:\'/getSystemSigs.php\',
				                    type:\'GET\',                    
				                    beforeSend:function () {

				                    },
				                    success:function (data) {                                            
				                      $(".sys-info-list").html("");
				                      $(".sys-info-list").html(data);                                                      
				                        
				                    }
				                });     
				              }
				            }
				          });

				        });

				      });
				    </script>
	            </div>
	            <div class="col-xs-12 intel">
	                <h4>Neighbouring Systems</h4>
	                <hr style="padding: 0; margin: 0;">';
	                	// Get systems intel
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

                            // Insert neighbour systems to database
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
	                                <div id="'.$system_id.'-intel" class="col-xs-6">
			                        	<button onclick="getIntel('.$system_id.')" class="btn btn-default btn-intel">Get System Intel</button>                                          
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
        	// System is in database

        	// Get system info
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
	                  <script>
                            $(function () {

                              $(\'#add-sig\').on(\'submit\', function (e) {

                                e.preventDefault();          
                                $.ajax({
                                  type: \'POST\',
                                  url: \'/sigadd.php\',
                                  data: $(\'#add-sig\').serialize(),
                                  success: function (data) {
                                    if (data == "false") {
                                      $(\'#sig-report\').html("<div class=\"col-md-2\"></div>\
                                                <div class=\"col-xs-12 col-md-8 notification-container notification-failed\">\
                                                  <div class=\"notification-left\"><i class=\"fa fa-times\" aria-hidden=\"true\"></i></div>\
                                                  <div class=\"notification-right\"><div class=\"notification-note\">A signature failed to add.</div></div>\
                                                </div>");
                                    } else if (data == "true") {
                                      $(\'#sig-report\').html("<div class=\"col-md-2\"></div>\
                                                <div class=\"col-xs-12 col-md-8 notification-container notification-success\">\
                                                  <div class=\"notification-left\"><i class=\"fa fa-times\" aria-hidden=\"true\"></i></div>\
                                                  <div class=\"notification-right\"><div class=\"notification-note\">All Signatures added.</div></div> \
                                                </div>");

                                      $.ajax({
                                          url:\'/getSystemSigs.php\',
                                          type:\'GET\',                    
                                          beforeSend:function () {

                                          },
                                          success:function (data) {                                            
                                            $(".sys-info-list").html("");
                                            $(".sys-info-list").html(data);                                                      
                                              
                                          }
                                      });     
                                    }
                                  }
                                });

                              });

                            });
                          </script>
	            </div>
	            <div id="sig-report" class="col-xs-12">

                </div>
	            <div class="col-xs-12 signature-list sys-info-list">
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
	                    	// Get system signatures
	                    	$conn2 = connect();
	                        $prepared2 = $conn2->prepare("SELECT * FROM signatures WHERE system_id = ?"); 
	                        $prepared2->bind_param('s', $main_system_id);    
	                        $prepared2->execute();
	                        $result2 = get_result($prepared2);
	                        $count = 0;
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
                                            <form id="delete-sig-'.$count.'" class="delete-sig" enctype="multipart/form-data" method="post">
				                                  <input type="hidden" name="sig_id" value="'.$sigID.'">
				                                  <button id="Submit" type="submit" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Delete Signature" name="Submit" value="Delete Sig" style="padding-left:6px;">
				                                    <span><i class="fa fa-times" aria-hidden="true"></i></span>
				                                  </button>                                  
				                              </form>        
                                        </td>
	                                </tr>
	                            ';
	                            $count = $count +1;
	                        }   
	                        $conn2->close();       
	                        
	                 
	        echo '  </table>
	        		<script>
				      $(function () {

				        $(\'.delete-sig\').on(\'submit\', function (e) {	          
				          e.preventDefault();	         
				          $.ajax({
				            type: \'POST\',
				            url: \'/sigdelete.php\',
				            data: $(\'#\'+this.id).serialize(),
				            success: function (data) {
				              if (data == "false") {
				                
				              } else if (data == "true") {
				                $.ajax({
				                    url:\'/getSystemSigs.php\',
				                    type:\'GET\',                    
				                    beforeSend:function () {

				                    },
				                    success:function (data) {                                            
				                      $(".sys-info-list").html("");
				                      $(".sys-info-list").html(data);                                                      
				                        
				                    }
				                });     
				              }
				            }
				          });

				        });

				      });
				    </script>
	            </div>';
         echo ' <div class="col-xs-12 intel">
                	<h4>Neighbouring Systems</h4>
                	<hr style="padding: 0; margin: 0;">';    
            // Get system intel              	
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
                        <div id="'.$neighbour_id.'-intel" class="col-xs-6">
                        	<button onclick="getIntel('.$neighbour_id.')" class="btn btn-default btn-intel">Get System Intel</button>                                          
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