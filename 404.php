<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">        
        <link rel="stylesheet" href="css/404.css">
        <link href='https://fonts.googleapis.com/css?family=Raleway' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Arvo' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet' type='text/css'>

        <title>Page not found</title>
    </head>
    <style type="text/css">
        body, html {
          margin: 0;
          padding: 0;
        }

        body {
          background: #060606;
          background: url('../img/backdrop.jpg');
        }
  
          canvas {
            display: block;
          }

          h3 {
            position: absolute;
            text-align: center;
            
            left: 0;
            right: 0;
            margin: auto;
            margin-top: 20%;
          }


    </style>
    <body class="container-fluid homepage">
        <h3 style="color: white;">It seems that you're lost in a perpetual black hole.</h3>
        <canvas id="particle"></canvas>
    </body>
    <script type="text/javascript">
        // Global Animation Setting
window.requestAnimFrame = 
  window.requestAnimationFrame ||
  window.webkitRequestAnimationFrame ||
  window.mozRequestAnimationFrame ||
  window.oRequestAnimationFrame ||
  window.msRequestAnimationFrame ||
  function(callback) {
    window.setTimeout(callback, 1000/60);
};

// Global Canvas Setting
var canvas = document.getElementById('particle');
var ctx = canvas.getContext('2d');
canvas.width = window.innerWidth;
canvas.height = window.innerHeight;


// Particles Around the Parent
function Particle(x, y, distance) {
  this.angle = Math.random() * 2 * Math.PI;
  this.radius = Math.random() ; 
  this.opacity =  (Math.random()*5 + 2)/10;
  this.distance = (1/this.opacity)*distance;
  this.speed = this.distance*0.00003;
  
  this.position = {
    x: x + this.distance * Math.cos(this.angle),
    y: y + this.distance * Math.sin(this.angle)
  };
  
  this.draw = function() {
    ctx.fillStyle = "rgba(255,255,255," + this.opacity + ")";
    ctx.beginPath();
    ctx.arc(this.position.x, this.position.y, this.radius, 0, Math.PI*2, false);
    ctx.fill();
    ctx.closePath();
  }
  this.update = function() {
    this.angle += this.speed; 
    this.position = {
      x: x + this.distance * Math.cos(this.angle),
      y: y + this.distance * Math.sin(this.angle)
    };
    this.draw();
  }
}

function Emitter(x, y) {
  this.position = { x: x, y: y};
  this.radius = 30;
  this.count = 3000;
  this.particles = [];
  
  for(var i=0; i< this.count; i ++ ){
    this.particles.push(new Particle(this.position.x, this.position.y, this.radius));
  }
}


Emitter.prototype = {
  draw: function() {
    ctx.fillStyle = "rgba(0,0,0,1)";
    ctx.beginPath();
    ctx.arc(this.position.x, this.position.y, this.radius, 0, Math.PI*2, false);
    ctx.fill();
    ctx.closePath();    
  },
  update: function() {  
   for(var i=0; i< this.count; i++) {
     this.particles[i].update();
   }
    this.draw(); 
  }
}


var emitter = new Emitter(canvas.width/2, canvas.height/2);

function loop() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  emitter.update();
  requestAnimFrame(loop);
}

loop();
    </script>
</html>