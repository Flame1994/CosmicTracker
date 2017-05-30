<?php
	// ============================================================================
    // Refreshes the token
    // ============================================================================
	session_start();
	if (isset($_SESSION["AccessToken"])) {
		$url = 'https://login.eveonline.com/oauth/token';
		$data = array('grant_type' => 'refresh_token', 'refresh_token' => $_SESSION["RefreshToken"]);

		$key = base64_encode('86fe2014301a423e9f9a4df3c44f24b1:B54yYfQbuBtBYnqSG6tymVvapyK8ek1Alt5T56SG');
		$options = array(
		    'http' => array(
		        'header'  => "Authorization: Basic ".$key."\r\nContent-type: application/x-www-form-urlencoded\r\n",
		        'method'  => 'POST',
		        'content' => http_build_query($data)
		    )
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		$content = json_decode($result, true);
		$access = $content['access_token'];
		$refresh = $content['refresh_token'];
		$_SESSION["AccessToken"] = $access;
		$_SESSION["RefreshToken"] = $refresh;
	}
?>