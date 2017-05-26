<?php
	include "php/routes.php";
	session_start();
	$search = $_GET['s'];

	echo '<table>
	        <tr>
	            <th>System</th>
	            <th>Signature ID</th>
	            <th>Signature Type</th>
	            <th>Signature Name</th>
	            <th>Reported By</th>
	            <th>Reported Time</th>
	            <th></th>
	        </tr>';
	$conn = connect();
	$param = '%'.$search.'%';
    $prepared = $conn->prepare("SELECT * FROM signatures 
    	WHERE region_name = ? AND (system LIKE ?
    	OR sig_id LIKE ?
    	OR sig_type LIKE ?
    	OR sig_name LIKE ?
    	OR reported LIKE ?)
    	ORDER BY report_time DESC"); 
    $prepared->bind_param('ssssss', $_SESSION['CharacterRegionName'], $param, $param, $param, $param, $param);
    $prepared->execute();
    $result = get_result($prepared);
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
                <td></td>
            </tr>
        ';
        
    }      
    $conn->close();
    echo ' </table>';

?>