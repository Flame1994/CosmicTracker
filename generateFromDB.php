<?php
	include "php/connect.php";
	$conn = connect();
    $prepared = $conn->prepare("SELECT * FROM systems");
	$prepared->execute();
	$result = get_result($prepared);
	echo '<canvas id="myCanvas" width="1920px" height="2000px"></canvas>
	<div class="column">					
			            <div class="portlet">
			                <div class="portlet-header"></div>			                
			            </div>		            
			        </div>
	';
	while($row = array_shift($result)){
		$systemID = $row['system_id'];
		$systemName = $row['name'];
		$systemXco = $row['x_co']+400;
		$systemZco = $row['z_co'];
		// <div id="'.$systemName.'" style="position: absolute;left: '.$systemXco.'px;top: '.$systemZco.'px;width: 12px;height: 12px;color: red;cursor: pointer;z-index:23;background-color: #FF0000;/* opacity: 0; */border-radius: 50%;" onclick="" onmouseover="" onmouseleave="">
					

		// 		</div>
		echo '
        		<div class="system" style="position: absolute;left: '.($systemXco-30).'px;top: '.($systemZco-20).'px;">
        			<div class="system-name">
        				'.$systemName.'
        			</div>
        			<div class="column ui-sortable"></div>
					
				</div>
				
			

        	';
	}		


	$prepared = $conn->prepare("SELECT * FROM gates");
	$prepared->execute();
	$result = get_result($prepared);
	echo '<script>
		var c = document.getElementById("myCanvas");
				var ctx = c.getContext("2d");
	';
	while($row = array_shift($result)){
		$gateID = $row['gate_id'];
		$systemStart = $row['system_start'];
		$systemEnd = $row['system_end'];

		$prepared2 = $conn->prepare("SELECT * FROM systems WHERE name = ?"); 
	    $prepared2->bind_param('s', $systemStart);    
	    $prepared2->execute();
		$result2 = get_result($prepared2);
		$systemStartXco = 0;
		$systemStartZco = 0;
		while($row2 = array_shift($result2)){
			$systemStartXco = $row2['x_co']+400;
			$systemStartZco = $row2['z_co'];
		}

		$systemStartXco -= 4;
		$systemStartZco -= 4;

		$prepared3 = $conn->prepare("SELECT * FROM systems WHERE name = ?"); 
	    $prepared3->bind_param('s', $systemEnd);    
	    $prepared3->execute();
		$result3 = get_result($prepared3);
		$systemEndXco = 0;
		$systemEndZco = 0;
		while($row3 = array_shift($result3)){			
			$systemEndXco = $row3['x_co']+400;
			$systemEndZco = $row3['z_co'];
		}

		$systemEndXco -= 4;
		$systemEndZco -= 4;
		if ($systemStartXco > 0 && $systemEndXco > 0) {
echo '
			

				
				ctx.beginPath();
				ctx.lineWidth=5;
				ctx.strokeStyle = \'#000\';
				ctx.moveTo('.$systemStartXco.', '.$systemStartZco.');
				ctx.lineTo('.$systemEndXco.', '.$systemEndZco.');
				ctx.stroke();
				ctx.lineWidth=3;
				ctx.strokeStyle = \'#4C4C4C\';
				ctx.moveTo('.$systemStartXco.', '.$systemStartZco.');
				ctx.lineTo('.$systemEndXco.', '.$systemEndZco.');
				ctx.stroke();

			
		';
		}

		
		

	}

	echo "</script>";
	
	echo '<script type="text/javascript">
        $( ".column" ).sortable({            
            connectWith: ".column",
            handle: ".portlet-header",
            cancel: ".portlet-toggle",
            start: function (event, ui) {
                ui.item.addClass(\'tilt\');
                tilt_direction(ui.item);
            },
            stop: function (event, ui) {
                ui.item.removeClass("tilt");
                $("html").unbind(\'mousemove\', ui.item.data("move_handler"));
                ui.item.removeData("move_handler");
            }
        });

        function tilt_direction(item) {
            var left_pos = item.position().left,
                move_handler = function (e) {
                    if (e.pageX >= left_pos) {
                        item.addClass("right");
                        item.removeClass("left");
                    } else {
                        item.addClass("left");
                        item.removeClass("right");
                    }
                    left_pos = e.pageX;
                };
            $("html").bind("mousemove", move_handler);
            item.data("move_handler", move_handler);
        }  

        $( ".portlet" )
            .addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
            .find( ".portlet-header" )
            .addClass( "ui-widget-header ui-corner-all" )
            .prepend( "<span class=\'ui-icon ui-icon-minusthick portlet-toggle\'></span>");

        $( ".portlet-toggle" ).click(function() {
            var icon = $( this );
            icon.toggleClass( "ui-icon-minusthick ui-icon-plusthick" );
            icon.closest( ".portlet" ).find( ".portlet-content" ).toggle();
        });

    </script>
    
';

	$conn->close();



?>