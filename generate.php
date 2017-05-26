<?php

	include "php/connect.php";
	ini_set('max_execution_time', 500);
	$x_co = array();
	$z_co = array();

	$width = $_GET['w'] - 300;	
	$height = $_GET['h'] - 200;

	$CenterXScreen = $width/2;
	$CenterZScreen = $height/2+200;


	$CenterX = -8.678786471368562*pow(10,16);
	$CenterZ = -4.172325974847579*pow(10,17);

	$ratioX = $CenterXScreen/$CenterX;
	$ratioZ = $CenterZScreen/$CenterZ;

	$maxX = -5.16780703998651*pow(10,16);
	$minX = -1.2189765902750613*pow(10,17);
	$xDiff = $maxX - $minX;

	$url = file_get_contents("https://crest-tq.eveonline.com/regions/10000039/");
    $region = json_decode($url, true);
    echo '<canvas id="myCanvas" width="1000px" height="1000px" style="border:1px solid #d3d3d3;"></canvas>';
	foreach($region['constellations'] as $constellations) { //foreach element in $arr
        $href = $constellations['href']; //etc
        // echo "$href <br>";
        $url2 = file_get_contents($href);
        $const = json_decode($url2, true);
        $positionX = $const['position']['x'];
        $positionZ = $const['position']['z'];
        // echo "X: $positionX - WORLD<br>";
        // echo "Z: $positionZ - WORLD<br>";
        $positionXScreen = $positionX*$ratioX;
        $positionXScreen = $width-$positionXScreen;

        $offsetX = $positionXScreen-$CenterXScreen;
        $newOffsetX = $offsetX*2;
        $newPositionXScreen = $CenterXScreen+$newOffsetX;

        $positionZScreen = $positionZ*$ratioZ;

        $offsetZ = $positionZScreen-$CenterZScreen;
        $newOffsetZ = $offsetZ*4;
        $newPositionZScreen = $CenterZScreen+$newOffsetZ;

        $name = $const['name'];
    //     echo '
        	
				// <div id="'.$name.'" style="position: absolute;left: '.$newPositionXScreen.'px;top: '.$newPositionZScreen.'px;width: 12px;height: 12px;color: red;cursor: pointer;z-index:23;background-color: #FF0000;/* opacity: 0; */border-radius: 50%;" onclick="" onmouseover="" onmouseleave="">'.$name.'</div>
			

    //     ';
        // echo "X: $positionXScreen - SCREEN<br>";
        // echo "Z: $positionZScreen - SCREEN<br>";
        // echo "Z: $positionZ - SCREEN<br>";
        foreach($const['systems'] as $system) {
            $Systemhref = $system['href'];
            echo "$Systemhref <br>";
            $url3 = file_get_contents($Systemhref);
            $syst = json_decode($url3, true);
            $systPositionX = $syst['position']['x'];
            $systPositionZ = $syst['position']['z'];
            $name = $syst['name'];
            $ID = $syst['id'];
            // echo "X: $systPositionX <br>";
            // echo "Z: $systPositionZ <br>";
            $positionXScreen = $systPositionX*$ratioX;
            $positionXScreen = $width-$positionXScreen;

            $offsetX = $positionXScreen-$CenterXScreen;
	        $newOffsetX = $offsetX*1.8;
	        $newPositionXScreen = $CenterXScreen+$newOffsetX;

        	$positionZScreen = $systPositionZ*$ratioZ;

        	$offsetZ = $positionZScreen-$CenterZScreen;
	        $newOffsetZ = $offsetZ*6;
	        $newPositionZScreen = $CenterZScreen+$newOffsetZ;

	        $new_co = check_co($newPositionXScreen,$newPositionZScreen, $x_co, $z_co, $name);
	        $newPositionXScreen = $new_co[0];
	        $newPositionZScreen = $new_co[1];

	        array_push($x_co, $newPositionXScreen);
	        array_push($z_co, $newPositionZScreen);

	        $conn = connect();
	        // $prepared2 = $conn->prepare("INSERT INTO `systems` (`id`, `system_id`, `name`, `x_co`, `z_co`) VALUES (?, ?, ?, ?, ?)");
	        $prepared2 = $conn->prepare("UPDATE `systems` SET x_co = ?, z_co = ? WHERE name = ?");
			$entryID = NULL;					
			$prepared2->bind_param('sss', $newPositionXScreen, $newPositionZScreen, $name);
			$prepared2->execute();
			$conn->close();

            echo '
        	
				<div id="'.$name.'" style="position: absolute;left: '.$newPositionXScreen.'px;top: '.$newPositionZScreen.'px;width: 12px;height: 12px;color: red;cursor: pointer;z-index:23;background-color: #FF0000;/* opacity: 0; */border-radius: 50%;" onclick="" onmouseover="" onmouseleave="">'.$name.'</div>
			

        	';

    //     	foreach($syst['stargates'] as $stargate) {
    //     		$stargatehref = $stargate['href'];
    //     		$url4 = file_get_contents($stargatehref);
    //         	$gate = json_decode($url4, true);

    //         	$destName = $gate['destination']['system']['name'];
    //         	$destID = $gate['destination']['system']['id'];
    //         	$gateID = $stargate['id'];

    //         	echo "$name -> $destName";


    //         	$conn = connect();
    //         	$prepared2 = $conn->prepare("INSERT INTO `gates` (`id`, `gate_id`, `system_start`, `system_end`) VALUES (?, ?, ?, ?)");
				// $entryID = NULL;					
				// $prepared2->bind_param('ssss', $entryID, $gateID, $name, $destName);
				// $prepared2->execute();
				// $conn->close();

    //     	}

        }

    }   


    function check_co($x, $z, $x_co, $z_co, $name) {
    	$right = false;
    	$top = false;    
    	$newX = $x;
    	$newZ = $z;	
    	$new_co = array();
    	$changed = false;
    	for ($i=0; $i < count($x_co); $i++) { 
    		$diffX = $x_co[$i] - $x;
    		$diffZ = $z_co[$i] - $z;

    		if ($diffX < 0) {
    			$diffX = -1*$diffX;
    			$right = false;
    		} else {
    			$right = true;
    		}

    		if ($diffZ < 0) {
    			$diffZ = -1*$diffZ;
    			$top = false;
    		} else {
    			$top = true;
    		}

    		if ($diffX < 60 && $diffZ < 60) {
    			if ($right == true) {
    				$newX = $x + 60;
    			} else {
    				$newX = $x - 70;
    				if ($newX < 0) {
    					$newX = 60;
    					$newZ = $newZ + 60;
    				}
    			}  

    			if ($top == true) {
    				$newZ = $z + 70;
    			} else {
    				$newZ = $z - 60;
    				if ($newZ < 0) {
    					$newZ = 60;
    					$newx = $newX + 60;
    				}
    			}  
    			$changed = true;
    		}
    	}
    	array_push($new_co, $newX);
    	array_push($new_co, $newZ);
    	if ($changed) {
    		$new_co = check_co($newX, $newZ, $x_co, $z_co, $name);
    	}

    	return $new_co;
    } 

?>