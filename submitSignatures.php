<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 'On');
	include "php/routes.php";
	session_start();
	if (isset($_POST['text'])) {
		$sigs = explode("\n", $text);    	
    	$system = $_SESSION["CharacterSystemName"];
    	$system_id = $_SESSION["CharacterSystemID"];


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

        $reporter_name = $_SESSION["CharacterName"];
	   	$reporter_id = $_SESSION["CharacterID"];
	   	$corp_id = $_SESSION["CharacterCorpID"];
	   	$alliance_id = $_SESSION["CharacterAllianceID"];
	   	$has = false;
    	for ($i=0; $i < sizeof($sigs); $i++) { 
    		$info = explode("\t",$sigs[$i]);
	    	$sig_id = $info[0];	    	
	    	$sig_type = $info[2];	    
	    	$sig_name = $info[3];
	    	if ($sig_type == '' || !isset($sig_type) || $sig_type == null) {
	    		$has = true;
	    	} else {
	    		$var = add_signature($system, $system_id, $const_name, $const_id, $region_name, $region_id, $sig_id, $sig_type, $sig_name, $reporter_name, $reporter_id, $corp_id, $alliance_id);
		    	if ($var == false) {
		    		$has = true;
		    	}
	    	}			    	
    	} 	
    	if ($has == true) {
    		echo '
              <div class="col-xs-12">
                <div class="col-md-2"></div>
                <div class="col-xs-12 col-md-8 notification-container notification-failed">
                  <div class="notification-left"><i class="fa fa-times" aria-hidden="true"></i></div>
                  <div class="notification-right"><div class="notification-note">A signature failed to add.</div></div>                      
                </div>  
              </div>
            ';
    		if (isset($_SESSION['CharacterSystemName']) && $_SESSION['CharacterSystemName'] != '' && !is_null($_SESSION['CharacterSystemName'])) {
                echo '
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
                      
                          $conn = connect();
                          $prepared = $conn->prepare("SELECT * FROM signatures WHERE system = ? AND (corp_id = ? OR alliance_id = ?)"); 
                          $prepared->bind_param('sss', $_SESSION["CharacterSystemName"], $_SESSION["CharacterCorpID"], $_SESSION['CharacterAllianceID']);    
                          $prepared->execute();
                          $result = get_result($prepared);
                          while ($row = array_shift($result)) {
                              $system = $row['system'];
                              $sigID = $row['sig_id'];
                              $sigType = $row['sig_type'];
                              $sigName = $row['sig_name'];
                              $reported = $row['reported'];
                              $reportedID = $row['reported_id'];
                              $reportTime = $row['report_time'];
                              echo '
                                  <tr>
                                      <td>'.$system.'</td>
                                      <td>'.$sigID.'</td>
                                      <td>'.$sigType.'</td>
                                      <td>'.$sigName.'</td>
                                      <td><a href="https://zkillboard.com/character/'.$reportedID.'/">'.$reported.'</a></td>
                                      <td>'.$reportTime.'</td>
                                      <td>
                                        <form id="sig-action-form" action="delete-signature" method="post" style="margin: 0;">
                                            <input type="hidden" name="sig_id" value="'.$sigID.'">
                                            <button type="submit" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Delete Signature" name="del_sig" value="Delete Sig" style="padding-left:6px;">
                                              <span><i class="fa fa-times" aria-hidden="true"></i></span>
                                            </button>
                                        </form>
                                      </td>
                                  </tr>
                              ';
                              
                          }      
                          $conn->close();
              
    		echo '	</table>
    			</div>';
    		}
    	} else {
    		echo '
              <div class="col-xs-12">
                <div class="col-md-2"></div>
                <div class="col-xs-12 col-md-8 notification-container notification-success">
                  <div class="notification-left"><i class="fa fa-times" aria-hidden="true"></i></div>
                  <div class="notification-right"><div class="notification-note">All Signatures added.</div></div>                      
                </div>  
              </div>
            ';
    		if (isset($_SESSION['CharacterSystemName']) && $_SESSION['CharacterSystemName'] != '' && !is_null($_SESSION['CharacterSystemName'])) {
                echo '
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
                      
                          $conn = connect();
                          $prepared = $conn->prepare("SELECT * FROM signatures WHERE system = ? AND (corp_id = ? OR alliance_id = ?)"); 
                          $prepared->bind_param('sss', $_SESSION["CharacterSystemName"], $_SESSION["CharacterCorpID"], $_SESSION['CharacterAllianceID']);    
                          $prepared->execute();
                          $result = get_result($prepared);
                          while ($row = array_shift($result)) {
                              $system = $row['system'];
                              $sigID = $row['sig_id'];
                              $sigType = $row['sig_type'];
                              $sigName = $row['sig_name'];
                              $reported = $row['reported'];
                              $reportedID = $row['reported_id'];
                              $reportTime = $row['report_time'];
                              echo '
                                  <tr>
                                      <td>'.$system.'</td>
                                      <td>'.$sigID.'</td>
                                      <td>'.$sigType.'</td>
                                      <td>'.$sigName.'</td>
                                      <td><a href="https://zkillboard.com/character/'.$reportedID.'/">'.$reported.'</a></td>
                                      <td>'.$reportTime.'</td>
                                      <td>
                                        <form id="sig-action-form" action="delete-signature" method="post" style="margin: 0;">
                                            <input type="hidden" name="sig_id" value="'.$sigID.'">
                                            <button type="submit" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Delete Signature" name="del_sig" value="Delete Sig" style="padding-left:6px;">
                                              <span><i class="fa fa-times" aria-hidden="true"></i></span>
                                            </button>
                                        </form>
                                      </td>
                                  </tr>
                              ';
                              
                          }      
                          $conn->close();
              
    		echo '	</table>
    			</div>';
	    	}
		}
	}

?>