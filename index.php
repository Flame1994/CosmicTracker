<?php
	session_start();
	include "php/routes.php";

    function parse_path() {
	  	$path = array();
		if (isset($_SERVER['REQUEST_URI'])) {
		    $request_path = explode('?', $_SERVER['REQUEST_URI']);

		    $path['base'] = rtrim(dirname($_SERVER['SCRIPT_NAME']), '\/');
		    $path['call_utf8'] = substr(urldecode($request_path[0]), strlen($path['base']) + 1);
		    $path['call'] = utf8_decode($path['call_utf8']);
		    if ($path['call'] == basename($_SERVER['PHP_SELF'])) {
		    	$path['call'] = '';
		    }
		    $path['call_parts'] = explode('/', $path['call']);

		    $path['query_utf8'] = urldecode($request_path[1]);
		    $path['query'] = utf8_decode(urldecode($request_path[1]));
		    $vars = explode('&', $path['query']);
		    foreach ($vars as $var) {
		    	$t = explode('=', $var);
		    	$path['query_vars'][$t[0]] = $t[1];
		    }
	 	}
		return $path;
	}	
	$path_info = parse_path();
	// echo '<pre>'.print_r($path_info, true).'</pre>';		
		
	if (isset($path_info['call_parts'][1]) && $path_info['call_parts'][1] == '') {
		include "404.php";
	} else {
		switch($path_info['call_parts'][0]) {
			case 'home': 
				include 'home.php';
		    	break;	 
		    case 'login':
		    	include 'login.php';   	
		    	break;
		    case '':
		    	include 'login.php';   	
		    	break;
		    case 'signature':
		    	$data = $_POST['sigdata'];
		    	// echo "$data";
		    	// QSQ-900	Cosmic Signature	Combat Site		43,9%	3,66 AU
		    	// QSQ-922	Cosmic Signature	Combat Site	Radiance	99,1%	3,68 AU

		    	$sigs = explode("\n", $data);    	
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
			    	$var = add_signature($system, $system_id, $const_name, $const_id, $region_name, $region_id, $sig_id, $sig_type, $sig_name, $reporter_name, $reporter_id, $corp_id, $alliance_id);
			    	if ($var == false) {
			    		$has = true;
			    	}
		    	}
		    	echo $has;
		    	echo "awe";
		    	if ($has == true) {
		    		echo "meh";
		    		header('Location: '.'/home?s=f');
		    	} else {
		    		echo "lel";
		    		header('Location: '.'/home?s=t');
		    	}
		    	break;
		    case 'delete-signature':
		    	$sig_id = $_POST['sig_id'];
		    	delete_signature($sig_id);
		    	include 'home.php';
		    	break;
		    case 'logout':
		    	logout();
		    	break;
			default:
		    	include '404.php';
		}
	}
	
?>