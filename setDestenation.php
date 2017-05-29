<?php	
	include "php/routes.php";
	session_start();
	if (isset($_GET['system'])) {
		$desto = $_GET['system'];

	// https://esi.tech.ccp.is/latest/ui/autopilot/waypoint/?add_to_beginning=false&clear_other_waypoints=false&datasource=tranquility&destination_id=$desto

	// 	$url = 'https://esi.tech.ccp.is/latest/ui/autopilot/waypoint/';
	// 	$data = array('add_to_beginning' => 'false', 'clear_other_waypoints' => 'false', 'datasource' => 'tranquility', 'destination_id' => $desto);
	// 	$key = base64_encode('86fe2014301a423e9f9a4df3c44f24b1:B54yYfQbuBtBYnqSG6tymVvapyK8ek1Alt5T56SG');
	// 	$options = array(
	// 	    'http' => array(
	// 	        'header'  => "Content-type: application/json\r\n",
	// 	        'method'  => 'POST',
	// 	        'content' => http_build_query($data)
	// 	    )
	// 	);	
		$myObj = new stdClass();
	    $url = 'https://crest-tq.eveonline.com/characters/'.$_SESSION['CharacterID'].'/ui/autopilot/waypoints/';
	    $myObj->clearOtherWaypoints = true;
	    $myObj->first = true;
	    $myObj->solarSystem->href = "https://crest-tq.eveonline.com/solarsystems/".$desto."/";
	    $myObj->solarSystem->id = (int)$desto;
	    $myJSON = json_encode($myObj);

	    $options = array(
	        'http' => array(
	            'header'  => "Authorization: Bearer ".$_SESSION['AccessToken']."\r\nContent-type: application/json",
	            'method'  => 'POST',
	            'content' => $myJSON
	        )
	    );
	    $context  = stream_context_create($options);
	    $result = file_get_contents($url, false, $context);


	 //    $url = file_get_contents("https://esi.tech.ccp.is/latest/route/".$_SESSION['CharacterSystemID']."/".$desto."/?datasource=tranquility&flag=shortest");
		// $content = json_decode($url, true);
		// $d = array();
		
		// foreach ($content as $obj) {

		// 	$url2 = file_get_contents("https://esi.tech.ccp.is/latest/universe/systems/".$obj."/?datasource=tranquility&language=en-us");
		// 	$content2 = json_decode($url2, true);
		// 	$name = $content2['name'];
		// 	$d[] = array('id' => $obj ,'name' => $name);		
		// }

	    // echo json_encode($d);



	    // -------------------------- JUST DESTO ------------------------
	    $url2 = file_get_contents("https://esi.tech.ccp.is/latest/universe/systems/".$desto."/?datasource=tranquility&language=en-us");
	    $content2 = json_decode($url2, true);
	    $name = $content2['name'];

	    echo $name;
	}
?>