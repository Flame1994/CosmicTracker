<?php
	include "php/routes.php";
	session_start();

	$sig_id = $_POST['sig_id'];
	$var = delete_signature($sig_id);

	echo "true";
	// FUN-628	Cosmic Signature	Data Site		0,0%	14,08 AU
?>