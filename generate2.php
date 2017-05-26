<?php

	include "php/connect.php";
	ini_set('max_execution_time', 500);
	$allSystems = array();

	
	
    $x = 100;
    $z = 100;
    echo '<canvas id="myCanvas" width="1000px" height="1000px" style="border:1px solid #d3d3d3;"></canvas>';	                        
    $conn = connect();
    $prepared2 = $conn->prepare("SELECT * FROM gates WHERE system_start = 'VYJ-DA'");	
    $prepared2->execute();
	$result2 = get_result($prepared2);
	echo '
		
			<div id="VYJ-DA" style="position: absolute;left: '.$x.'px;top: '.$z.'px;width: 12px;height: 12px;color: red;cursor: pointer;z-index:23;background-color: #FF0000;/* opacity: 0; */border-radius: 50%;" onclick="" onmouseover="" onmouseleave="">VYJ-DA</div>
		

		';
	array_push($allSystems, 'VYJ-DA');
	// $x += 50;	
	$dir = 1;
	$COUNT = 3;
	while($row2 = array_shift($result2)){
		$systemEnd = $row2['system_end'];
		// $x += 50;	
		if ($COUNT >= 4) {
			$dir = 4;
		}
		if ($COUNT == 3) {
			$dir = 3;
		}
		if ($COUNT == 2) {
			$dir = 2;
		}
		if ($COUNT == 1) {
			$dir = 1;
		}	

		if ($dir == 1) {
			$newx = $x + 100;
			$newz = $z;
		} else if ($dir == 2) {
			$newz = $z + 100;
			$newx = $x;
		} else if ($dir == 3) {
			$newx = $x - 50;
			$newz = $z;
			echo "gethere";
		} else if ($dir == 4) {
			$newz = $z - 50;
			$newx = $x;
		}
		
		$hm = next_system($newx, $newz, $systemEnd, $allSystems, $dir);	
		$COUNT -= 1;	
	}

    


//       $conn = connect();
//       // $prepared2 = $conn->prepare("INSERT INTO `systems` (`id`, `system_id`, `name`, `x_co`, `z_co`) VALUES (?, ?, ?, ?, ?)");
//       $prepared2 = $conn->prepare("UPDATE `systems` SET x_co = ?, z_co = ? WHERE name = ?");
	// $entryID = NULL;					
	// $prepared2->bind_param('sss', $newPositionXScreen, $newPositionZScreen, $name);
	// $prepared2->execute();
	// $conn->close();

    

	


	function next_system($x, $z, $name, $allSystems, $dir) {	
		echo "<br> $name -> $dir <br>";
		$orig = $dir;
		$conn = connect();
		$prepared2 = $conn->prepare("SELECT COUNT(*) AS count FROM gates WHERE system_start = '$name'");	
	    $prepared2->execute();
		$result2 = get_result($prepared2);
		$COUNT = 0;
		while($row2 = array_shift($result2)){
			$COUNT = $row2['count'];			
		}

		$prepared2 = $conn->prepare("SELECT * FROM gates WHERE system_start = '$name'");	
	    $prepared2->execute();
		$result2 = get_result($prepared2);
		$COUNT -= 1;
		
		

		echo '
		
			<div id="'.$name.'" style="position: absolute;left: '.$x.'px;top: '.$z.'px;width: 12px;height: 12px;color: red;cursor: pointer;z-index:23;background-color: #FF0000;/* opacity: 0; */border-radius: 50%;" onclick="" onmouseover="" onmouseleave="">'.$name.'</div>
		

		';

		array_push($allSystems, $name);
		// 1 -> 3
		// 2 -> 4
		while($row2 = array_shift($result2)){
			$systemEnd = $row2['system_end'];
			// echo "$COUNT";
			if (!in_array($systemEnd, $allSystems)) {	

				if ($COUNT >= 4) {
					if ($orig == 4) {
						$dir = 2;
					} else {
						$dir = 4;
					}
					
				}
				if ($COUNT == 3) {
					if ($orig == 3) {
						$dir = 4;
					} else {
						$dir = 3;
					}
				}
				if ($COUNT == 2) {					
					if ($orig == 2) {
						$dir = 1;
					} else {
						$dir = 2;
					}
				}
				if ($COUNT == 1) {
					if ($orig == 1) {
						$dir = 1;
					} else {
						$dir = $orig;
					}
				}	

				if ($dir == 1) {
					$newx = $x + 150;
					$newz = $z;
				} else if ($dir == 2) {
					$newz = $z + 150;
					$newx = $x;
				} else if ($dir == 3) {
					$newx = $x - 100;
					$newz = $z;					
				} else if ($dir == 4) {
					$newz = $z - 100;
					$newx = $x;
				}
				$hmm = next_system($newx, $newz, $systemEnd, $allSystems, $dir);	
				$COUNT -= 1;			
			}						
		}

		return '';
	}

?>