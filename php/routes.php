<?php
	include "php/connect.php";
	/**
	 * Navigates the browser to the home page.
	*/
	function home_page() {
		// header('Location: '.'/EveBounty/home');
		header('Location: '.'/home');
	}

	function add_signature($system, $system_id, $const_name, $const_id, $region_name, $region_id, $sig_id, $sig_type, $sig_name, $reporter_name, $reporter_id, $corp_id, $alliance_id) {	
		$conn = connect();

		$prepared = $conn->prepare("SELECT * FROM signatures WHERE sig_id = ? AND system_id = ? AND alliance_id = ?"); 
		$prepared->bind_param('ss', $sig_id, $system_id, $alliance_id);    
		$prepared->execute();
		$result = get_result($prepared);
		if ($prepared->num_rows == 0 || $sig_type == '') {
		  	$date = date('Y-m-d H:i:s', time() - 60*60*2 - 103); 				
			$prepared = $conn->prepare("INSERT INTO `signatures` (`id`, `system`, `system_id`, `const_name`, `const_id`, `region_name`, `region_id`, `sig_id`, `sig_type`, `sig_name`, `reported`, `reported_id`, `corp_id`, `alliance_id`, `report_time`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"); 
			$entryVal = NULL;		
			$prepared->bind_param('sssssssssssssss', $entryVal, $system, $system_id, $const_name, $const_id, $region_name, $region_id, $sig_id, $sig_type, $sig_name, $reporter_name, $reporter_id, $corp_id, $alliance_id, $date);
			$prepared->execute();
			

			$prepared = $conn->prepare("SELECT * FROM users WHERE user = ?"); 
			$prepared->bind_param('s', $reporter_name);
			$prepared->execute();
			$result = get_result($prepared);
			while ($row = array_shift($result)) {
			  $relic = (int)$row['relic_sites'];
			  $data = (int)$row['data_sites'];
			  $combat = (int)$row['combat_sites'];
			  $gas = (int)$row['gas_sites'];
			  $wormholes = (int)$row['wormholes'];
			  $total = (int)$row['total_scanned'];  
			}
			if ($sig_type == 'Relic Site') {
				$relic++;
				$prepared2 = $conn->prepare("UPDATE `users` SET relic_sites = ? WHERE user = ?"); 
			    $prepared2->bind_param('ds', $relic, $reporter_name);    
			    $prepared2->execute();
			} else if ($sig_type == 'Data Site') {
				$data++;
				$prepared2 = $conn->prepare("UPDATE `users` SET data_sites = ? WHERE user = ?"); 
			    $prepared2->bind_param('ds', $data, $reporter_name);    
			    $prepared2->execute();
			} else if ($sig_type == 'Combat Site') {
				$combat++;
				$prepared2 = $conn->prepare("UPDATE `users` SET combat_sites = ? WHERE user = ?"); 
			    $prepared2->bind_param('ds', $combat, $reporter_name);    
			    $prepared2->execute();
			} else if ($sig_type == 'Gas Site') {
				$gas++;
				$prepared2 = $conn->prepare("UPDATE `users` SET gas_sites = ? WHERE user = ?"); 
			    $prepared2->bind_param('ds', $gas, $reporter_name);    
			    $prepared2->execute();
			} else if ($sig_type == 'Wormhole') {
				$wormholes++;
				$prepared2 = $conn->prepare("UPDATE `users` SET wormholes = ? WHERE user = ?"); 
			    $prepared2->bind_param('ds', $wormholes, $reporter_name);    
			    $prepared2->execute();
			}
			$total++;
			$prepared2 = $conn->prepare("UPDATE `users` SET total_scanned = ? WHERE user = ?"); 
		    $prepared2->bind_param('ds', $total, $reporter_name);    
		    $prepared2->execute();
		    $conn->close();
			
			return true;
		} else {
			return false;
		}
	}

	function delete_signature($sig_id) {
		$conn = connect();
		$prepared = $conn->prepare("DELETE FROM signatures WHERE sig_id = ?"); 
	    $prepared->bind_param('s', $sig_id);    
	    $prepared->execute();
	    $conn->close();
	}

	function logout() {
		session_start();

		// Unset all of the session variables.
		$_SESSION = array();

		// If it's desired to kill the session, also delete the session cookie.
		// Note: This will destroy the session, and not just the session data!
		if (ini_get("session.use_cookies")) {
		    $params = session_get_cookie_params();
		    setcookie(session_name(), '', time() - 42000,
		        $params["path"], $params["domain"],
		        $params["secure"], $params["httponly"]
		    );
		}
		session_unset();
    	session_destroy();

		header('Location: '.'/login');
	}


?>