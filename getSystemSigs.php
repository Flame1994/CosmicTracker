<?php
	include "php/routes.php";
	session_start();
	echo '
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
                $count = 0;
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
                $conn->close();
  echo '</table>
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
	                      $(".signature-list").html("");
	                      $(".signature-list").html(data);                                                      
	                        
	                    }
	                });     
	              }
	            }
	          });

	        });

	      });
	    </script>
  ';

?>