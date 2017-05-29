<?php
	include "php/routes.php";
	session_start();
	if (isset($_GET['system'])) {
		  $system_id = $_GET['system'];
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


		  echo '
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
		  ';
	}

?>