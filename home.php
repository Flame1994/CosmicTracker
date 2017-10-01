<?php    
    if (isset($_SESSION["AccessToken"])) {
        $url = file_get_contents("https://crest-tq.eveonline.com/");
        $content = json_decode($url, true);

        $serviceStatus =  $content['serviceStatus'];
        $serverCount =  $content['userCount_str'];        
        if ($serviceStatus != 'online') {
          header('Location: '.'/logout');
        }


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


        $url2 = 'https://crest-tq.eveonline.com/characters/'.$_SESSION["CharacterID"].'/location/';
        $options2 = array(
            'http' => array(
                'header'  => "Authorization: Bearer ".$_SESSION["AccessToken"]."\r\n",
                'method'  => 'GET'
            )
        );
        $context2  = stream_context_create($options2);        
        $result2 = file_get_contents($url2, false, $context2);
        if ($result2 === FALSE) {

        } else {
            $content2 = json_decode($result2, true);            
            if ($content2 === FALSE || empty($content2) || is_null($content2)) {
            } else {
                $system_id = $content2['solarSystem']['id'];
                $system_name = $content2['solarSystem']['name'];
                $system_href = $content2['solarSystem']['href'];
                $_SESSION["CharacterSystemID"] = $system_id;
                $_SESSION["CharacterSystemName"] = $system_name;        
            }
            
        }
    } else {
      header('Location: '.'/login');
    }
?>

<html lang="en-US" class=" js no-touch cssanimations csstransitions" style="">
	<head>      
      
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">  

        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
        <link href='https://fonts.googleapis.com/css?family=Holtwood+One+SC' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Kaushan+Script|Herr+Von+Muellerhoff' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Istok+Web|Roboto+Condensed:700' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/style.css">
        <link rel="icon" href="img/logo.ico" />
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <title>
            Home | Cosmic Tracker
        </title>
        <script type="text/javascript">
            var screenWidth = window.screen.width,
                screenHeight = window.screen.height;        
        </script>
        <script type="text/javascript">
            var queue = [];
            queue.push("<?php echo $_SESSION['CharacterSystemName'] ?>");
            var desto = "";
        </script>
        
        <script type="text/javascript">
            function drawConnection(x1, y1, x2, y2) {
                var c=document.getElementById("canvas");
                var ctx=c.getContext("2d");
                
                ctx.beginPath();
                
                // ctx.shadowBlur = 0;
                ctx.lineWidth = 4;
                ctx.strokeStyle = '#87CEFA';
                ctx.moveTo(x1,y1);
                ctx.lineTo(x2,y2);
                ctx.stroke();
                
                // ctx.shadowBlur = 0;
                ctx.lineWidth = 2;
                ctx.strokeStyle = '#000';
                ctx.moveTo(x1,y1);
                ctx.lineTo(x2,y2);
                ctx.stroke();
            }
            
        </script>
        <script type="text/javascript">
            "use strict";

            window.requestAnimFrame = (function() {
                return  window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.oRequestAnimationFrame || window.msRequestAnimationFrame ||
                    function( callback ) { window.setTimeout(callback, 1000 / 60 ); }
            })();

            (function() {
              var settings = {
                NUM_PARTICLES : 10,
                DISTANCE_T    : 0,
                RADIUS        : 1,
                OPACITY       : 1,
                SPEED_X       : 0.8,
                SPEED_Y       : 0.6,
                AMPLITUDE     : 110
              };
              
              var COLOURS    = ["#FFFFFF", "#FFFFFF", "#FFFFFF"],
                  bounds     = {},
                  particles  = [],
                  random     = Math.random,
                  sqrt       = Math.sqrt,
                  PI         = Math.PI,
                  ctx, W, H, stats;
              
              function Particle() {
                this.x      = random() * bounds.right;
                this.y      = random() * bounds.bottom;
                this.xspeed = random() * settings.SPEED_X;
                this.yspeed = random() * settings.SPEED_Y;
                this.radius = settings.RADIUS;
                this.colour = COLOURS[ ~~(random() * COLOURS.length)];
              }
              
              var bindEvents = function() {
                window.addEventListener('resize', resize, false);
              };
              
              var resize = function() {
                W = window.innerWidth;
                H = window.innerHeight;
                ctx.canvas.width  = W;
                ctx.canvas.height = H;
                bounds.top      = 100;
                bounds.right    = W - 100;
                bounds.bottom   = H - 200;
                bounds.left     = 100;
              };
              
              var draw = function() {
                render();
                requestAnimFrame(draw);
              };
              
              var update = function (p) {
                p.x += p.xspeed;
                p.y += p.yspeed;
                
                if (p.x > bounds.right) {
                  p.x = bounds.right;
                  p.xspeed *= -1;
                }
                if (p.x < bounds.left) {
                  p.x = bounds.left;
                  p.xspeed *= -1;
                }
                if (p.y > bounds.bottom) {
                  p.y = bounds.bottom;
                  p.yspeed *= -1;
                }
                if (p.y < bounds.top) {
                  p.y = bounds.top;
                  p.yspeed *= -1;
                }
              };
              
              var render = function() {
                ctx.beginPath();
                ctx.globalCompositeOperation = "source-over";
                ctx.rect(0, 0 , W, H);
                ctx.fillStyle = "rgba(0,0,0,.3)";

                ctx.fill();
                ctx.closePath();
                
                ctx.globalCompositeOperation = "lighter";
                    
                for (var i = 0; i < particles.length; i += 1) {
                  var p = particles[i];
                  
                  ctx.beginPath();
                  ctx.globalAlpha = settings.OPACITY;
                  ctx.fillStyle = p.colour;
                  ctx.arc(p.x, p.y, p.radius, PI * 2, false);
                  ctx.fill();
                  ctx.closePath();
                  
                  for (var j = 0; j < particles.length; j += 1) {
                    var pp = particles[j],
                        yd = pp.y - p.y,
                        xd = pp.x - p.x,
                        d  = sqrt(xd * xd + yd * yd);
                    
                    if ( d < settings.DISTANCE_T ) {
                      ctx.beginPath();
                      ctx.globalAlpha = (settings.DISTANCE_T - d) / (settings.DISTANCE_T - 0);
                      ctx.lineWidth = 1;
                      ctx.moveTo(p.x, p.y);
                      
                      if ( settings.AMPLITUDE ) {
                        ctx.bezierCurveTo(
                          p.x,
                          p.y - random() * settings.AMPLITUDE,
                          pp.x,
                          pp.y + random() * settings.AMPLITUDE,
                          pp.x,
                          pp.y
                        );
                      } else {
                        ctx.lineTo(pp.x, pp.y);
                      }
                      
                      ctx.strokeStyle = p.colour;
                      ctx.lineCap = "round";
                      ctx.stroke();
                      ctx.closePath();
                      
                    }
                  }
                  
                  update(p);
                  
                }
              };
              
              var updateSpeed = function( x, y, attr ) {
                var speed = arguments[0] ? x : y;
                for (var i = 0; i < settings.NUM_PARTICLES; i += 1) {
                  var ns = random() * speed;
                  particles[i][attr] = particles[i][attr] > 0 ? ns : -ns;
                }
              };
              
              var updateRadius = function( value ) {
                for (var i = 0; i < settings.NUM_PARTICLES; i += 1) {
                  particles[i].radius = value;
                }
              };
              
              var init = function() {
                ctx = document.getElementsByTagName('canvas')[0].getContext('2d');
              
                bindEvents();
                resize();
                
                for (var i = 0; i < settings.NUM_PARTICLES; i += 1) {
                  particles.push( new Particle() );
                }
                
                draw();
              };
              
              window.onload = init;
              
              var GUI = new dat.GUI();
              
              GUI.add(settings, 'NUM_PARTICLES')
                .min(2)
                .max(200)
                .step(1)
                .name("# Particles")
                .onFinishChange(function( num ){
                  var l = particles.length;
                  if ( num < l ) {
                    var diff = (l - num);
                    particles.splice( 1, diff );
                  }
                
                  if ( num > l ) {
                    var diff = (num - l);
                    for (var i = 0; i < diff; i += 1) {
                      particles.push( new Particle() );
                    }
                  }
                });
              
              GUI.add(settings, 'DISTANCE_T').min(0)
                .max(200)
                .step(10)
                .name("Distance");
              
              GUI.add(settings, 'RADIUS')
                .min(0)
                .max(10)
                .step(1)
                .name("Radius")
                .onFinishChange(function( value ) {
                  updateRadius( value );
                });
              
              GUI.add(settings, 'SPEED_X')
                .min(0)
                .max(20)
                .name("X speed")
                .onFinishChange(function( value ) {
                  updateSpeed( value, false, "xspeed");
                });
              
              GUI.add(settings, 'SPEED_Y')
                .min(0)
                .max(20)
                .name("Y speed")
                .onFinishChange(function( value ) {
                  updateSpeed( false, value, "yspeed");
                });
              
              GUI.add(settings, 'AMPLITUDE')
                .min(0)
                .max(20)
                .step(1)
                .name("Amplitude");
              
              GUI.close();
              
            })();   
                    
                    
                    var canvas = document.getElementById("canvas"),
                ctx = canvas.getContext("2d");



            var background = new Image();
            background.src = "http://mytime-magazine.com/img/bg.jpg";

            background.onload = function(){
                ctx.drawImage(background,0,0);   
            }
        </script>
    </head>
    <body class="homepage" id="content-block">        
        <canvas id="backdrop" style="position: absolute;"></canvas>
        <div class="col-xs-12 col-lg-5 side-panel" id="system-info-container">
            <div class="col-xs-12 profile-info">
                <?php
                    if (isset($_SESSION['CharacterID'])) {
                        $portrait = "https://imageserver.eveonline.com/Character/".$_SESSION['CharacterID']."_64.jpg";
                        echo '<img id="profile-pic" src="'.$portrait.'">';
                        echo '
                                <div class="profile-char">
                                    <h4>'.strtoupper($_SESSION['CharacterName']).'</h4>
                                    <h5>'.$_SESSION["CharacterCorp"].'</h5>
                                    <h5>'.$_SESSION["CharacterAlliance"].'</h5>
                                </div>
                        ';
                        $corpPortrait = "https://imageserver.eveonline.com/Corporation/".$_SESSION['CharacterCorpID']."_64.png";
                        if ($_SESSION['CharacterAllianceID'] != '') {
                          $alliancePortrait = "https://imageserver.eveonline.com/Alliance/".$_SESSION['CharacterAllianceID']."_64.png";
                          echo '
                                <div class="profile-ca">
                                  <img id="profile-pic" src="'.$corpPortrait.'">
                                  <img id="profile-pic" src="'.$alliancePortrait.'">
                                </div>
                        ';
                        } else {                          
                          echo '
                                <div class="profile-ca">
                                  <img id="profile-pic" src="'.$corpPortrait.'">
                                </div>
                        ';
                        }
                        echo '
                                <div class="logout-button">
                                    
                                    <form action="logout" enctype="multipart/form-data" method="post">                                        
                                        <button type="submit" name="logout" class="btn btn-default btn-logout"><i class="fa fa-sign-out" aria-hidden="true"></i></button>
                                    </form>
                                </div>
                        ';
                    } else {
                      echo '<a  href="https://login.eveonline.com/oauth/authorize?response_type=code&redirect_uri=http://cosmic.ashfordindustries.xyz/callback.php&client_id=86fe2014301a423e9f9a4df3c44f24b1&scope=characterLocationRead"><img id="login-eve"><img id="login-eve" src="EVE_SSO_Login_Buttons_Large_Black.png"></a> ';  
                    }                     
                ?>           
            </div>
            <div class="col-xs-12 main-headers">
                <div id="system-info-button" class="col-xs-3 main-header">
                    <h4>System Info</h4>
                </div>
                <div id="all-sigs-button" class="col-xs-3 main-header">
                    <h4>All Signatures</h4>
                </div>
                <div id="players-button" class="col-xs-3 main-header">
                    <h4>Players</h4>
                </div>
                <div id="help-button" class="col-xs-3 main-header">
                    <h4>Help</h4>
                </div>
            </div> 
            <div id="system-info" class="col-xs-12">
              <div class="col-xs-12" style="background-color: #333;">
                    <h2>Current System: <span class="text-primary">
                        <?php
                        if (isset($_SESSION['CharacterSystemName']) && $_SESSION['CharacterSystemName'] != '' && !is_null($_SESSION['CharacterSystemName'])) {
                          $url = file_get_contents("https://esi.tech.ccp.is/dev/universe/systems/".$_SESSION["CharacterSystemID"]."/?datasource=tranquility&language=en-us");
                          $content = json_decode($url, true);      
                          $const_id = $content['constellation_id'];

                          $url2 = file_get_contents("https://esi.tech.ccp.is/latest/universe/constellations/".$const_id."/?datasource=tranquility&language=en-us");
                          $content2 = json_decode($url2, true);      
                          
                          $const_name = $content2['name'];
                          $region_id = $content2['region_id'];
                          $url3 = file_get_contents("https://esi.tech.ccp.is/latest/universe/regions/".$region_id."/?datasource=tranquility&language=en-us");
                          $content3 = json_decode($url3, true);   
                          $region_name = $content3['name'];
                          if (isset($_SESSION["CharacterSystemName"])) {
                              echo $_SESSION["CharacterSystemName"] ;
                              echo '<h5 style="color:white;">'.$content['name'].' <span class="security-status"> '.round($content['security_status'],1).'</span> &lsaquo; '.$const_name.' &lsaquo; '.$region_name.'</h5>';
                          } else {
                              echo "Not Found";
                          }
                        } else {
                          echo "Not online";
                        }                       
                        ?>

                    </span></h2>
                </div>
                <div class="col-xs-12" style="height: 160px;">
                    <?php
                      if (isset($_SESSION['CharacterSystemName']) && $_SESSION['CharacterSystemName'] != '' && !is_null($_SESSION['CharacterSystemName'])) {
                        echo '
                          <h6>Add Signatures</h6>
                          <form id="add-sig" action="signature" enctype="multipart/form-data" method="post">
                              <textarea id="sigdata" name="sigdata" placeholder="Add signature results" style="width:100%; color:#333333; font-size:12px" rows="4"></textarea>
                              <button type="submit" name="Submit" id="Submit" class="btn btn-default">Submit</button>
                          </form>
                          <script>
                            $(function () {

                              $(\'#add-sig\').on(\'submit\', function (e) {

                                e.preventDefault();          
                                $.ajax({
                                  type: \'POST\',
                                  url: \'/sigadd.php\',
                                  data: $(\'#add-sig\').serialize(),
                                  success: function (data) {
                                    if (data == "false") {
                                      $(\'#sig-report\').html("<div class=\"col-md-2\"></div>\
                                                <div class=\"col-xs-12 col-md-8 notification-container notification-failed\">\
                                                  <div class=\"notification-left\"><i class=\"fa fa-times\" aria-hidden=\"true\"></i></div>\
                                                  <div class=\"notification-right\"><div class=\"notification-note\">A signature failed to add.</div></div>\
                                                </div>");
                                      $(\'#sigdata\').val(\'\');
                                    } else if (data == "true") {
                                      $(\'#sig-report\').html("<div class=\"col-md-2\"></div>\
                                                <div class=\"col-xs-12 col-md-8 notification-container notification-success\">\
                                                  <div class=\"notification-left\"><i class=\"fa fa-times\" aria-hidden=\"true\"></i></div>\
                                                  <div class=\"notification-right\"><div class=\"notification-note\">All Signatures added.</div></div> \
                                                </div>");
                                        $(\'#sigdata\').val(\'\');
                                      $.ajax({
                                          url:\'/getSystemSigs.php\',
                                          type:\'GET\',                    
                                          beforeSend:function () {

                                          },
                                          success:function (data) {                                            
                                            $(".sys-info-list").html("");
                                            $(".sys-info-list").html(data);                                                      
                                              
                                          }
                                      });     
                                    }
                                  }
                                });

                              });

                            });
                          </script>
                        ';
                      }
                    ?>
                    
                </div>
                <div id="sig-report" class="col-xs-12">

                </div>
                <?php

                  if (isset($_GET['s'])) {
                    if ($_GET['s'] == 't') {
                      echo '
                        <div class="col-xs-12">
                          <div class="col-md-2"></div>
                          <div class="col-xs-12 col-md-8 notification-container notification-success">
                            <div class="notification-left"><i class="fa fa-times" aria-hidden="true"></i></div>
                            <div class="notification-right"><div class="notification-note">All Signatures added.</div></div>                      
                          </div>  
                        </div>
                      ';
                    } else {
                      echo '
                        <div class="col-xs-12">
                          <div class="col-md-2"></div>
                          <div class="col-xs-12 col-md-8 notification-container notification-failed">
                            <div class="notification-left"><i class="fa fa-times" aria-hidden="true"></i></div>
                            <div class="notification-right"><div class="notification-note">A signature failed to add.</div></div>                      
                          </div>  
                        </div>
                      ';
                    }
                  }

                ?>
                
                
                <div class="col-xs-12 signature-list sys-info-list">
                  <?php
                    if (isset($_SESSION['CharacterSystemName']) && $_SESSION['CharacterSystemName'] != '' && !is_null($_SESSION['CharacterSystemName'])) {
                      echo '
                        <table>
                            <tr>
                                <th>System</th>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Signature Name</th>
                                <th>Explorer</th>
                                <th>Time</th>
                                <th></th>
                            </tr>';
                            
                                $conn = connect();
                                $prepared = $conn->prepare("SELECT * FROM signatures WHERE system = ? AND (corp_id = ? OR alliance_id = ?)"); 
                                $prepared->bind_param('sss', $_SESSION["CharacterSystemName"], $_SESSION["CharacterCorpID"], $_SESSION['CharacterAllianceID']);    
                                $prepared->execute();
                                $result = get_result($prepared);
                                $count = 0;
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
                                            <td>
                                              <form id="delete-sig-'.$count.'" class="delete-sig" enctype="multipart/form-data" method="post">
                                                  <input type="hidden" name="sig_id" value="'.$sigID.'">
                                                  <button id="Submit" type="submit" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Delete Signature" name="Submit" value="Delete Sig" style="padding-left:6px;">
                                                    <span><i class="fa fa-times" aria-hidden="true"></i></span>
                                                  </button>                                  
                                              </form>                                            
                                            </td>
                                        </tr>
                                    ';
                                    $count = $count + 1;
                                }   

                                $conn->close();
                  echo '</table>
                        <script>
                          $(function () {

                            $(\'.delete-sig\').on(\'submit\', function (e) {
                              e.preventDefault();                              
                              $.ajax({
                                type: \'POST\',
                                url: \'/sigdelete.php\',
                                data: $(\'#\'+this.id).serialize(),
                                success: function (data) {
                                  if (data == "false") {
                                    
                                  } else if (data == "true") {
                                    $.ajax({
                                        url:\'/getSystemSigs.php\',
                                        type:\'GET\',                    
                                        beforeSend:function () {

                                        },
                                        success:function (data) {                                            
                                          $(".sys-info-list").html("");
                                          $(".sys-info-list").html(data);                                                      
                                            
                                        }
                                    });     
                                  }
                                }
                              });

                            });

                          });
                        </script>
                  ';
                      }
                    ?> 
                </div>
                <div class="col-xs-12 intel">
                    <?php
                    
                        
                        if (isset($_SESSION['CharacterSystemName']) && $_SESSION['CharacterSystemName'] != '' && !is_null($_SESSION['CharacterSystemName'])) {
                          echo '<h4>Neighbouring Systems</h4>
                          <hr style="padding: 0; margin: 0;">';
                          $url = file_get_contents("https://esi.tech.ccp.is/dev/universe/systems/".$_SESSION["CharacterSystemID"]."/?datasource=tranquility&language=en-us");
                          $content = json_decode($url, true);
                          $stargates = $content['stargates'];
                          foreach ($stargates as $stargate) {
                              $url2 = file_get_contents("https://esi.tech.ccp.is/dev/universe/stargates/".$stargate."/?datasource=tranquility");
                              $content2 = json_decode($url2, true);

                              $destination = $content2['destination']['system_id'];

                              $url3 = file_get_contents("https://esi.tech.ccp.is/dev/universe/systems/".$destination."/?datasource=tranquility&language=en-us");
                              $content3 = json_decode($url3, true);
                              $system_name = $content3['name'];
                              $system_id = $content3['system_id'];                            
                              $sec_status = round($content3['security_status'],1);
                              $const_id = $content3['constellation_id'];

                              $url2 = file_get_contents("https://esi.tech.ccp.is/latest/universe/constellations/".$const_id."/?datasource=tranquility&language=en-us");
                              $content2 = json_decode($url2, true);      
                              
                              $const_name = $content2['name'];
                              $region_id = $content2['region_id'];
                              $url3_1 = file_get_contents("https://esi.tech.ccp.is/latest/universe/regions/".$region_id."/?datasource=tranquility&language=en-us");
                              $content3_1 = json_decode($url3_1, true);   
                              $region_name = $content3_1['name'];

                              
                              
                              echo '
                                  
                                  <div class="col-xs-12 intel-system">
                                      <h5>'.$system_name.' <span class="security-status"> '.$sec_status.'</span> &lsaquo; '.$const_name.' &lsaquo; '.$region_name.'</h5>
                                      <div class="col-xs-12 col-lg-6">
                                          <table>';
                                          $conn = connect();
                                          $prepared = $conn->prepare("SELECT * FROM signatures WHERE system = ? AND (corp_id = ? OR alliance_id = ?)");  
                                          $prepared->bind_param('sss', $content3['name'], $_SESSION["CharacterCorpID"], $_SESSION['CharacterAllianceID']);    
                                          $prepared->execute();
                                          $result = get_result($prepared);
                                          if ($prepared->num_rows == 0) {
                                              echo "No Signatures Found";
                                          }
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
                                                      <td>'.$sigID.'</td>
                                                      <td>'.$sigType.'</td>
                                                      <td>'.$sigName.'</td>
                                                  </tr>
                                              ';
                                              
                                          }      
                                          $conn->close();

                              echo '      </table>
                                      </div>
                                      <div id="'.$system_id.'-intel" class="col-xs-12 col-lg-6">
                                          <button onclick="getIntel('.$system_id.')" class="btn btn-default btn-intel">Get System Intel</button>                                          
                                      </div>              
                                  </div>
                              ';
                              
                          }
                        }
                        
                        ?>
                </div>
            </div>    
            <div id="all-sigs" class="col-xs-12">
              <h4 id="sig-region">Signatures scanned in <span class="text-primary"><?php echo $_SESSION['CharacterRegionName']; ?></span></h4>
              <form action="" autocomplete="on" style="margin-bottom: 50px;">
                <input id="search" name="search" type="text" placeholder="What are you looking for?"><input id="search_submit" value="Rechercher" type="submit"><i class="fa fa-search search-bar" aria-hidden="true" style="font-size: 30px;"></i>
              </form>
              <div class="col-xs-12 signature-list all-signature-list">
                  <table>
                      <tr>
                          <th>System</th>
                          <th>ID</th>
                          <th>Type</th>
                          <th>Signature Name</th>
                          <th>Explorer</th>
                          <th>Time</th>
                          <th></th>
                      </tr>
                      <?php
                          $conn = connect();                          
                          $prepared = $conn->prepare("SELECT * FROM signatures WHERE region_name = ? AND (corp_id = ? OR alliance_id = ?) ORDER BY report_time DESC"); 
                          $prepared->bind_param('sss', $_SESSION['CharacterRegionName'], $_SESSION["CharacterCorpID"], $_SESSION['CharacterAllianceID']);    
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
                  ?> 
                  </table>
              </div>
            </div>  
            <div id="players" class="col-xs-12" style="background-color: #333; height: 100%; max-height: 100%; overflow: auto;">
              <div id="total-scanned" class="col-xs-12">                    
                  <table style="position: absolute; right: 0; text-align: left; border: none; width: 50%;">
                    <tr style="border: none;">
                      <?php
                          $conn = connect();

                          $prepared = $conn->prepare("SELECT SUM(relic_sites) as relicTotal, SUM(data_sites) as dataTotal, SUM(combat_sites) as combatTotal, SUM(gas_sites) as gasTotal, SUM(wormholes) as wormholeTotal FROM users WHERE alliance_id = ?");
                          $prepared->bind_param('s', $_SESSION['CharacterAllianceID']);   
                          $prepared->execute();
                          $result = get_result($prepared);
                          while ($row = array_shift($result)) {
                              $relicT = $row['relicTotal'];
                              $dataT = $row['dataTotal'];
                              $combatT = $row['combatTotal'];
                              $gasT = $row['gasTotal'];
                              $wormholeT = $row['wormholeTotal'];
                              echo '
                                  <td><img src="img/relic.png"> '.$relicT.'</td>                      
                                  <td><img src="img/data.png"> '.$dataT.'</td>                      
                                  <td><img src="img/combat.png"> '.$combatT.'</td>                      
                                  <td><img src="img/gas.gif"> '.$gasT.'</td>
                                  <td><img src="img/wormhole.png"> '.$wormholeT.'</td> 
                              ';
                              
                          }      
                          $conn->close();
                      ?>  
                                           
                    </tr>
                  </table>                               
              </div>
              <div class="col-xs-12">
                <h4>Top Explorers (All Time)</h4>
                  <table style="text-align: center;">
                        <tr>
                            <th>Rank</th>
                            <th></th>
                            <th>Name</th>
                            <th>Relic Sites</th>
                            <th>Data Sites</th>
                            <th>Combat Sites</th>
                            <th>Gas Sites</th>
                            <th>Wormholes</th>
                            <th>Total</th>
                        </tr>
                        <?php
                            $conn = connect();

                            $prepared = $conn->prepare("SELECT * FROM users WHERE alliance = ? ORDER BY total_scanned DESC LIMIT 10");
                            $prepared->bind_param('s', $_SESSION['CharacterAlliance']);
                            $prepared->execute();
                            $result = get_result($prepared);
                            $i = 1;
                            while ($row = array_shift($result)) {
                                $user = $row['user'];
                                $userID = $row['user_id'];
                                $corp = $row['corp'];
                                $corpID = $row['corp_id'];
                                $alliance = $row['alliance'];
                                $allianceID = $row['alliance_id'];
                                $relic = $row['relic_sites'];
                                $data = $row['data_sites'];
                                $gas = $row['gas_sites'];
                                $combat = $row['combat_sites'];
                                $wormhole = $row['wormholes'];
                                $total = $row['total_scanned'];
                                $portrait = "https://imageserver.eveonline.com/Character/".$userID."_64.jpg";
                                $corpPortrait = "https://imageserver.eveonline.com/Corporation/".$corpID."_64.png";
                                $alliancePortrait = "https://imageserver.eveonline.com/Alliance/".$allianceID."_64.png";
                                echo '
                                    <tr>
                                        <td>'.$i.'</td>
                                        <td><img id="profile-pic" src="'.$portrait.'"></td>
                                        <td><a href="https://zkillboard.com/character/'.$userID.'/">'.$user.'</a></td>
                                        <td>'.$relic.'</td>
                                        <td>'.$data.'</td>
                                        <td>'.$combat.'</td>
                                        <td>'.$gas.'</td>
                                        <td>'.$wormhole.'</td>
                                        <td>'.$total.'</td>
                                    </tr>
                                ';
                                $i = $i + 1;
                            }      
                            $conn->close();
                    ?> 
                    </table>                
              </div>              
            </div> 
            <div id="help" class="col-xs-12">              
              <h3 style="border-bottom: solid 1px white; text-align: left;">Website Guide</h3>
              <button class="accordion">Step 1 - Navigate to a system</button>
              <div class="panel">
                <p>Navigate to the system you want to explore.</p>
                <img src="img/step1.JPG">
              </div>
              <button class="accordion">Step 2 - Check for signatures</button>
              <div class="panel">
                <p>Open the probe window <span class="key">alt</span>+<span class="key">P</span> and check if there are any signatures. Check if these signatures are already submitted.</p>
                <img src="img/step2.JPG">
              </div>

              <button class="accordion">Step 3 - Scan signatures</button>
              <div class="panel">
                <p>Scan down any signatures you want.</p>
                <img src="img/step3.JPG">
              </div>
              <button class="accordion">Step 4 - Copy scanned signatures</button>
              <div class="panel">
                <p>Select the signatures you want to add.</p>
                <p>Select mutiple signatures by holding down the <span class="key">ctrl</span> key. Press <span class="key">ctrl</span>+<span class="key">A</span> to select all signatures. Press <span class="key">ctrl</span>+<span class="key">C</span> to copy.</p>
                <img src="img/step4.JPG">
              </div>
              <button class="accordion">Step 5 - Paste signatures to the website</button>
              <div class="panel">
                <p>Go to the website and paste with <span class="key">ctrl</span>+<span class="key">V</span> into the textarea. Click the submit button.</p>
                <img src="img/step5.JPG">
              </div>
              <div id="help-faq">
                <h3 style="border-bottom: solid 1px white; text-align: left;">FAQ</h3>
                <h4>What is this?</h4>
                <p>This site provides you with the ability to map the signatures you or any other alliance member has scanned accross New Eden. By tracking your current location, the website follows you through each system for a user friendly experience in submitting signatures that you have scanned.</p>
                <hr>
                <h4>How do you track my location?</h4>
                <p>By using SSO (Single Sign On) on this website, you give us permission to access your live location through the CREST API. The Eve ESI will be used in the future. Your location is never stored and will not be made available to other users.</p>
                <hr>
                <h4>Why should I use this?</h4>
                <p>This site gives you the opportunity to make ISK faster. When entering a system, the website will display which sites has been scanned. You can easily see which sites you can then ignore or scan.</p>
                <hr>
                <h4>Who can see the scanned sites?</h4>
                <p>Only alliance members will be able to see the sites you have scanned.</p>
                <hr>
                <h4>Why do some systems take so long to load?</h4>
                <p>All information regarding the system is pulled from the EVE API's if the system has never been 'seen' by our website. This process takes a while. Once the system has been seen, all information is pulled from our own database, which should be faster. Please be patient when exploring systems our website has never seen.</p>
                <hr>
                <h4>How can I support the developer?</h4>
                <p>If you do wish to support the developer and site, send ISK to Kallen Ashford.</p>
                <hr>
              </div> 
            </div>   

            

        </div>
        <div class="col-xs-12 col-lg-7 map">
            <div id="map-filters">
              <ul id="nav-bar">
                <li id="no-filter" class="level-one" onmousedown="clearRegionMap()" style="background-color: #337ab7;"><img src="img/reset.png">
                  <ul class="level-two">
                    <li>No Filter<li/>
                  </ul>
                </li> 
                <li id="relic-filter" class="level-one"><img src="img/relic.png">
                  <ul class="level-two">
                    <li>Relic Sites<li/>
                  </ul>
                </li> 
                <li id="data-filter" class="level-one"><img src="img/data.png">
                  <ul class="level-two">
                    <li>Data Sites<li/>
                  </ul>
                </li>
                <li id="gas-filter" class="level-one"><img src="img/gas.gif">
                  <ul class="level-two">
                    <li>Gas Sites<li/>
                  </ul>
                </li>   
                <li id="combat-filter" class="level-one"><img src="img/combat.png">
                  <ul class="level-two">
                    <li>Combat Sites<li/>
                  </ul>
                </li>                    
                <li id="wormhole-filter" class="level-one"><img src="img/wormhole.png">
                  <ul class="level-two">
                    <li>Wormholes<li/>
                  </ul> 
                </li>                                   
              </ul>
            </div>            
            <div class="col-xs-12" id="history-container">
                <h4 style="color: white; margin-top: 20px;">Jump History <span id="jump-filter">show</span></h4>
                <div class="col-xs-12" id="jump-history">
                    <span class="jump-history" onmouseover="showSystem('<?php echo $_SESSION['CharacterSystemName'] ?>')" onmouseout="hideSystem('<?php echo $_SESSION['CharacterSystemName'] ?>')" alt="<?php echo $_SESSION['CharacterSystemName'] ?>"><?php echo $_SESSION['CharacterSystemName'] ?></span>
                </div>
            </div>
            <div class="col-xs-12" id="canvas-container">
                <canvas id="canvas" width="1000" height="800"></canvas>
                    <?php 
                        if (isset($_SESSION['CharacterSystemName'])) {
                          $r = str_replace(" ","_",$_SESSION['CharacterRegionName']);                          
                          $url = file_get_contents("maps/".$r.".svg.json");
                          $content = json_decode($url, true);
                          $systems = $content['map']['systems'];
                          foreach($systems as $system) {                               
                            if ($system['name'] == $_SESSION['CharacterSystemName']) {
                            echo '  <div class="system current-system" id="'.$system['name'].'" onmouseover="showSystemInfo(\''.$system['name'].'\', \''.$system['id'].'\')" onmouseout="hideSystemInfo(\''.$system['name'].'\')" style="position: absolute; left: '.($system['x']+36).'px; top: '.($system['y']+5).'px; width: 16px; height: 16px; cursor: pointer; background-color: #337ab7;">
                                      <div class="system-name"><h5>'.$system['name'].'</h5></div>
                                      <div id="'.$system['name'].'-info" class="system-info-popup">
                                        <div class="col-xs-12">
                                          <h5>'.$system['name'].'</h5>    
                                          <div id="'.$system['name'].'-sites"></div>                                      
                                          <button onclick="setDestenation('.$system['id'].')" class="btn btn-default btn-dest">Set Destination</button>
                                        </div>
                                      </div>
                                  </div>';
                                } else {
                            echo '  <div class="system" id="'.$system['name'].'" onmouseover="showSystemInfo(\''.$system['name'].'\', \''.$system['id'].'\')" onmouseout="hideSystemInfo(\''.$system['name'].'\')" style="position: absolute; left: '.($system['x']+36).'px; top: '.($system['y']+5).'px; width: 16px; height: 16px; cursor: pointer; background-color: #FFF;">
                                      <div class="system-name"><h5>'.$system['name'].'</h5></div>
                                      <div id="'.$system['name'].'-info" class="system-info-popup">
                                        <div class="col-xs-12">
                                          <h5>'.$system['name'].'</h5>
                                          <div id="'.$system['name'].'-sites"></div>
                                          <button onclick="setDestenation('.$system['id'].')" class="btn btn-default btn-dest">Set Destination</button>
                                        </div>
                                      </div>
                                  </div>';  
                                }                                      
                          }
                          $connections = $content['map']['connections'];
                          foreach($connections as $connection) { 
                              echo '<script> drawConnection('.$connection['x1'].','.$connection['y1'].','.$connection['x2'].','.$connection['y2'].'); </script>';
                          } 
                        }
                    ?>    
                </canvas>    
            </div>
            
            
        </div>        
        <div class="col-xs-12 footer">
            <div class="col-sm-6">
                All EVE related materials are property of <a href="https://www.ccpgames.com/">CCP Games</a> <br>                
                Brought to you by <a href="https://evewho.com/pilot/Kallen+Ashford">Kallen Ashford</a>
            </div>            
        </div>
    </body>

    <script type="text/javascript">
        $(document).ready(function(){
            $("#no-filter").click(function(){
                $("#no-filter").css("background-color", "#337ab7");
            });

            $("#relic-filter").click(function(){
                clearRegionMap();
                $("#relic-filter").css("background-color", "#FF8C00");
                $.ajax({
                    url:'/getRelicSites.php',
                    type:'GET',                    
                    beforeSend:function () {

                    },
                    success:function (data) {                      
                        if (data == 'NONE') {
                        } else {
                          var json = JSON.parse(data);
                          for (var key in json) {
                            if (json.hasOwnProperty(key)) {
                              $("#"+json[key].system).css("background-color", "#FF8C00");
                            }
                          }
                        }
                    }
                }); 
            });

            $("#data-filter").click(function(){
                clearRegionMap();
                $("#data-filter").css("background-color", "cyan");
                $.ajax({
                    url:'/getDataSites.php',
                    type:'GET',                    
                    beforeSend:function () {

                    },
                    success:function (data) {
                        if (data == 'NONE') {
                        } else {
                          var json = JSON.parse(data);
                          for (var key in json) {
                            if (json.hasOwnProperty(key)) {
                              $("#"+json[key].system).css("background-color", "cyan");
                            }
                          }                        
                        }
                    }
                }); 

            });

            $("#gas-filter").click(function(){
                clearRegionMap();
                $("#gas-filter").css("background-color", "grey");
                $.ajax({
                    url:'/getGasSites.php',
                    type:'GET',                    
                    beforeSend:function () {

                    },
                    success:function (data) {
                        if (data == 'NONE') {
                        } else {
                          var json = JSON.parse(data);
                          for (var key in json) {
                            if (json.hasOwnProperty(key)) {
                              $("#"+json[key].system).css("background-color", "grey");
                            }
                          }
                        }
                    }
                }); 
            });

            $("#combat-filter").click(function(){
                clearRegionMap();
                $("#combat-filter").css("background-color", "red");
                $.ajax({
                    url:'/getCombatSites.php',
                    type:'GET',                    
                    beforeSend:function () {

                    },
                    success:function (data) {
                        if (data == 'NONE') {
                        } else {
                          var json = JSON.parse(data);
                          for (var key in json) {
                            if (json.hasOwnProperty(key)) {
                              $("#"+json[key].system).css("background-color", "red");
                            }
                          }
                        }
                    }
                }); 
            });

            $("#wormhole-filter").click(function(){
                clearRegionMap();
                $("#wormhole-filter").css("background-color", "yellow");                
                $.ajax({                    
                    url:'/getWormholes.php',
                    type:'GET',                    
                    beforeSend:function () {                      
                    },
                    success:function (data) {  
                        if (data == 'NONE') {
                        } else {                      
                          var json = JSON.parse(data);
                          for (var key in json) {
                            if (json.hasOwnProperty(key)) {
                              $("#"+json[key].system).css("background-color", "yellow");
                            }
                          }
                        }
                    }
                }); 
            });

            $("#jump-filter").click(function(){
                showJumpHistory();
                $("#no-filter").css("background-color", "white");
                $("#jump-filter").css("background-color", "#7FFF00");                
            });

        });
    </script>
    <script type="text/javascript">
      $(document).ready(function(){
        $.ajax({
            url:'/getMap.php',
            type:'GET',
            data:'system='+<?php echo "\"".$_SESSION['CharacterSystemID']."\"";?>,
            beforeSend:function () {

            },
            success:function (data) {
                if (data == null || data == '' || data == 'NONE') {
                } else {      
                    $("#canvas-container").html("");
                    $("#canvas-container").html(data);                    
                    $("#"+<?php echo "\"".$_SESSION['CharacterSystemName']."\""; ?>).css("background-color", "#337ab7");
                }                            
            }
        }); 
      });
    </script>
    <script type="text/javascript">
        $(document).ready(function(){
          $("#search").on("paste keyup", function() {         
             $.ajax({
                url:'/getSearchResult.php',
                type:'GET',
                data:'s='+$(this).val(),
                beforeSend:function () {

                },
                success:function (data) {              
                  $(".all-signature-list").html(data);
                }
            });
          });
        });
        setInterval(function() {
          $(document).ready(function(){
            $.ajax({
                url:'/updateSystem.php',
                type:'GET',
                beforeSend:function () {

                },
                success:function (data2) {
                    if (data2 == 'logout') {                      
                    } else if (data2 == null || data2 == '' || data2 == 'NONE') {
                    } else {                      
                      $.ajax({
                        url:'/checkSystem.php',
                        type:'GET',
                        data:'system='+data2,
                        beforeSend:function () {                            
                        },
                        success:function (data) {
                            if (data == null || data == '' || data == 'NONE') {
                            } else {
                                $("#system-info").html(data);
                                $("#jump-history").html("");
                                $("#"+desto).css("background-color", "red");
                                                       
                                var text = "";
                                var i;
                                if (queue.length > 10) {
                                    queue.shift();
                                }
                                for (i = 0; i < queue.length; i++) {
                                    text += "> <span class= \"jump-history\" onmouseover=\"showSystem(\'"+queue[i]+"\')\" onmouseout=\"hideSystem(\'"+queue[i]+"\')\" alt=\""+queue[i]+"\">"+queue[i]+"</span> ";                                    
                                }
                                text += "</h4>";

                                $("#jump-history").html(text);
                                $.ajax({
                                    url:'/getMap.php',
                                    type:'GET',
                                    data:'system='+data2,
                                    beforeSend:function () {

                                    },
                                    success:function (data) {
                                        if (data == null || data == '' || data == 'NONE') {
                                        } else {      
                                            $("#canvas-container").html("");
                                            $("#canvas-container").html(data);
                                            $.ajax({
                                                url:'/getSignatures.php',
                                                type:'GET',                    
                                                beforeSend:function () {

                                                },
                                                success:function (data) {                      
                                                    if (data == 'NONE') {                                                      
                                                    } else {
                                                      $("#all-sigs").html(data);                                                      
                                                    }
                                                }
                                            });                                             
                                        }                            
                                    }
                                }); 
                            }
                        }
                      });                       
                    }
                  }
            });             
          });          
        }, 1000*5);
    </script>  
    <script type="text/javascript">
        function showSystem(system) {            
            $("#"+system).css("background-color", "#7FFF00");
        }
        function hideSystem(system) {
            $("#"+system).css("background-color", "white");
        }

    </script>  
    <script type="text/javascript">
        function clearRegionMap() {            
            $(".level-one").css("background-color", "white");
            $("#jump-filter").css("background-color", "white");
            $(".system").css("background-color", "white");            
            if (desto != "") {
              $("#"+desto).css("background-color", "red");
            }            
        }

        function showJumpHistory() {
            for (i = 0; i < queue.length; i++) {
                if (i == queue.length-1) {
                    $("#"+queue[i]).css("background-color", "#337ab7");
                } else {
                    $("#"+queue[i]).css("background-color", "#7FFF00");    
                }                
            }            
        }
    </script>  
    <script type="text/javascript">
      $("#all-sigs-button").click(function(){
          $("#system-info").css("display", "none");
          $("#players").css("display", "none");
          $("#all-sigs").css("display", "block");
          $("#help").css("display", "none");
          $("#all-sigs-button").css("background-color", "#222");
          $("#system-info-button").css("background-color", "#444");
          $("#players-button").css("background-color", "#444");
          $("#help-button").css("background-color", "#444");
      });
      $("#system-info-button").click(function(){
          $("#system-info").css("display", "block");
          $("#all-sigs").css("display", "none");
          $("#players").css("display", "none");
          $("#help").css("display", "none");
          $("#all-sigs-button").css("background-color", "#444");
          $("#players-button").css("background-color", "#444");
          $("#system-info-button").css("background-color", "#333");
          $("#help-button").css("background-color", "#444");
      });
      $("#players-button").click(function(){
          $("#system-info").css("display", "none");
          $("#all-sigs").css("display", "none");
          $("#players").css("display", "block");
          $("#help").css("display", "none");
          $("#all-sigs-button").css("background-color", "#444");
          $("#players-button").css("background-color", "#333");
          $("#system-info-button").css("background-color", "#444");
          $("#help-button").css("background-color", "#444");
      });
      $("#help-button").click(function(){
          $("#system-info").css("display", "none");
          $("#all-sigs").css("display", "none");
          $("#players").css("display", "none");
          $("#help").css("display", "block");
          $("#all-sigs-button").css("background-color", "#444");
          $("#players-button").css("background-color", "#444");
          $("#system-info-button").css("background-color", "#444");
          $("#help-button").css("background-color", "#222");
      });
    </script>  

    <script type="text/javascript">
      
    </script>
    <script type="text/javascript">
      setInterval(function() {
            $.ajax({
                url:'/refreshToken.php',
                type:'GET',
                beforeSend:function () {

                },
                success:function (data) {                  
                  $(".output-test").html(data);
                }
            });           
        }, 1000*60*10);
    </script>
    <script type="text/javascript">
      $( ".notification-left" ).click(function() {
        $( ".notification-note" ).html("");      
        $(".notification-right").animate({width: "0"},800,"linear",function(){
            $(".notification-left").html("");
            $(".notification-left").animate({height: "0"},400);    
            $(".notification-container").animate({height: "0"},400);    
        });
      });
    </script>
    <script>
      var acc = document.getElementsByClassName("accordion");
      var i;

      for (i = 0; i < acc.length; i++) {
        acc[i].onclick = function() {
          this.classList.toggle("active");
          var panel = this.nextElementSibling;
          if (panel.style.maxHeight){
            panel.style.maxHeight = null;
          } else {
            panel.style.maxHeight = panel.scrollHeight + "px";
          } 
        }
      }
    </script>

    <script type="text/javascript">
        function showSystemInfo(system, id) {
          $("#"+system+"-info").css("display", "block");
          $.ajax({
              url:'/getSystemInfo.php',
              type:'GET',
              data:'system='+id,
              beforeSend:function () {
              },
              success:function (data) {                                  
                $("#"+system+"-sites").html(data);
              }
          });
        }
        function hideSystemInfo(system) {
          $("#"+system+"-info").css("display", "none");
        }
        function setDestenation(system) {
          $.ajax({
              url:'/setDestenation.php',
              type:'GET',
              data:'system='+system,
              beforeSend:function () {

              },
              success:function (data) {        
                desto = data;
                clearRegionMap();                
              }
          });
        }
    </script>
    <script type="text/javascript">
      function getIntel(system) {
        $.ajax({
            url:'/getIntel.php',
            type:'GET',
            data:'system='+system,
            beforeSend:function () {
              $("#"+system+"-intel").html("<h5>Loading Intel...</h5>");
            },
            success:function (data) {        
              $("#"+system+"-intel").html(data);
            }
        });
      }
    </script>
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>    
    <!-- <script>
      $(function () {

        $('#add-sig').on('submit', function (e) {

          e.preventDefault();          
          $.ajax({
            type: 'POST',
            url: '/sigadd.php',
            data: $('#add-sig').serialize(),
            success: function (data) {
              if (data == "false") {
                $('#sig-report').html("<div class=\"col-md-2\"></div>\
                          <div class=\"col-xs-12 col-md-8 notification-container notification-failed\">\
                            <div class=\"notification-left\"><i class=\"fa fa-times\" aria-hidden=\"true\"></i></div>\
                            <div class=\"notification-right\"><div class=\"notification-note\">A signature failed to add.</div></div>\
                          </div>");
              } else if (data == "true") {
                $('#sig-report').html("<div class=\"col-md-2\"></div>\
                          <div class=\"col-xs-12 col-md-8 notification-container notification-success\">\
                            <div class=\"notification-left\"><i class=\"fa fa-times\" aria-hidden=\"true\"></i></div>\
                            <div class=\"notification-right\"><div class=\"notification-note\">All Signatures added.</div></div> \
                          </div>");

                $.ajax({
                    url:'/getSystemSigs.php',
                    type:'GET',                    
                    beforeSend:function () {

                    },
                    success:function (data) {                                            
                      $(".signature-list").html("");
                      $(".signature-list").html(data);                                                      
                        
                    }
                });     
              }
            }
          });

        });

      });
    </script> -->

    
</html>