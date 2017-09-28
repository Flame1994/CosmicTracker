<?php
	include "php/routes.php";
	session_start();

	$data = $_POST['sigdata'];
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
		// header('Location: '.'/home?s=f');
		echo "false";
	} else {
		// header('Location: '.'/home?s=t');
		echo "true";
	}

?>