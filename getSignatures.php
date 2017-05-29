<?php
	include "php/routes.php";
	session_start();
	if (isset($_SESSION['CharacterRegionName'])) {	
		echo '
		<h4 id="sig-region">Signatures scanned in <span class="text-primary">'.$_SESSION['CharacterRegionName'].'</span></h4>
              <form action="" autocomplete="on" style="margin-bottom: 50px;">
                <input id="search" name="search" type="text" placeholder="What are you looking for?"><input id="search_submit" value="Rechercher" type="submit"><i class="fa fa-search search-bar" aria-hidden="true" style="font-size: 30px;"></i>
              </form>
              <div class="col-xs-12 signature-list all-signature-list">
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
		          $prepared = $conn->prepare("SELECT * FROM signatures WHERE region_name = ? AND (corp_id = ? OR alliance_id = ?) ORDER BY report_time DESC"); 
		          $prepared->bind_param('sss', $_SESSION['CharacterRegionName'], $_SESSION["CharacterCorpID"], $_SESSION['CharacterAllianceID']);    
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
		                      <td></td>
		                  </tr>
		              ';
		              
		          }      
		          $conn->close();
		  echo '</table>
		  </div>	';
	} else {
		echo "NONE";
	}
?>