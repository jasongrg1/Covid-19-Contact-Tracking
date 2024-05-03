<?php
session_start();
if(!isset($_SESSION["name"])) {
    header("Location: index.php");
    exit();
}else {
    $name = $_SESSION["name"];
}
define("LOGOUT","Logout");
$action = htmlentities($_POST["action"]);

if (isset($action) && $action ===LOGOUT) {
  session_destroy();
  header("Location: index.php");
  exit;
}   

if(!isset($_COOKIE["window"])) {
  setcookie("window", "7", time() + (86400 * 30));
}
if(!isset($_COOKIE["distance"])) {
  setcookie("distance", "100", time() + (86400 * 30));
}

$distance = $_COOKIE["distance"];
$window = $_COOKIE["window"];

require "auth.php";

$conn = mysqli_connect(hostname, username, password, database, port);
if (!$conn) {
  die("Could not connect. " . mysqli_connect_error());
}

$sql = "SELECT name from users where uname = ?";
$stmt = mysqli_stmt_init($conn);
mysqli_stmt_prepare($stmt, $sql);
mysqli_stmt_bind_param($stmt, "s", $name);

if (mysqli_stmt_execute($stmt)===false){
  die("Error executing".$sql);
}
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
  mysqli_stmt_bind_result($stmt, $realName);
  mysqli_stmt_fetch($stmt);
}

mysqli_stmt_free_result($stmt);
mysqli_close($conn);




$conn = mysqli_connect(hostname, username, password, database, port);
if (!$conn) {
  die("Could not connect. " . mysqli_connect_error());
}

$sql = "SELECT visits.x, visits.y, visits.date, visits.time, visits.duration FROM users INNER JOIN infections ON users.uname = infections.user 
INNER JOIN visits ON users.uname = visits.user WHERE visits.date BETWEEN DATE_SUB(CURDATE(), 
INTERVAL ? DAY) AND CURDATE() AND EXISTS (SELECT * FROM visits V WHERE V.user = ? AND 
SQRT (POWER(V.x - visits.x, 2) + POWER(V.y - visits.y, 2)) < ? 
AND (visits.date BETWEEN V.date AND DATE_ADD(V.date, INTERVAL V.duration MINUTE) OR 
(V.date BETWEEN visits.date AND DATE_ADD(visits.date, INTERVAL visits.duration MINUTE))))";

$stmt = mysqli_stmt_init($conn);
mysqli_stmt_prepare($stmt, $sql);
mysqli_stmt_bind_param($stmt, "sss", $window, $name, $distance);



if (mysqli_stmt_execute($stmt)===false){
  die("Error executing".$sql);
}



mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
  $red_infections=[];
  mysqli_stmt_bind_result($stmt, $redX, $redY, $redDate, $redTime, $redDuration);
  while(mysqli_stmt_fetch($stmt)) {
    array_push($red_infections, array($redX, $redY, $redDate, $redTime, $redDuration));
  }
}

mysqli_stmt_free_result($stmt);
mysqli_close($conn);

$conn = mysqli_connect(hostname, username, password, database, port);
if (!$conn) {
  die("Could not connect. " . mysqli_connect_error());
}

$sql = "SELECT visits.x, visits.y, visits.date, visits.time, visits.duration FROM users INNER JOIN infections ON users.uname = infections.user 
INNER JOIN visits ON users.uname = visits.user WHERE visits.date BETWEEN DATE_SUB(CURDATE(), 
INTERVAL ? DAY) AND CURDATE() AND EXISTS (SELECT * FROM visits V WHERE V.user = ?
AND (visits.date BETWEEN V.date AND DATE_ADD(V.date, INTERVAL V.duration MINUTE) OR 
(V.date BETWEEN visits.date AND DATE_ADD(visits.date, INTERVAL visits.duration MINUTE))))";

$stmt = mysqli_stmt_init($conn);
mysqli_stmt_prepare($stmt, $sql);
mysqli_stmt_bind_param($stmt, "ss", $window, $name);



if (mysqli_stmt_execute($stmt)===false){
  die("Error executing".$sql);
}


mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
  $black_infections=[];
  mysqli_stmt_bind_result($stmt, $blackX, $blackY, $blackDate, $blackTime, $blackDuration);
  while(mysqli_stmt_fetch($stmt)) {
    foreach ($red_infections as $red_infection){
      if (!array($blackX, $blackY, $blackDate, $blackTime, $blackDuration) == $red_infection) {
    array_push($red_infections, array($blackX, $blackY, $blackDate, $blackTime, $blackDuration));
      }
    }
  }
}

mysqli_stmt_free_result($stmt);
mysqli_close($conn);



if (($handle = curl_init())===false) {
  echo 'Curl-Error: ' . curl_error($handle);
} else {
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_FAILONERROR, true);
}

$service_infections=[];
$url = "http://ml-lab-7b3a1aae-e63e-46ec-90c4-4e430b434198.ukwest.cloudapp.azure.com:60999/infections?ts=" . $_COOKIE["window"];
curl_setopt($handle, CURLOPT_URL, $url);
curl_setopt($handle, CURLOPT_HTTTPGET, true);
curl_setopt($handle, CURLOPT_HEADER, false);

if (($output = curl_exec($handle)) !==false) {
  $service_infections = json_decode($output, true);
  foreach($service_infections as $service_infection) {
    if ($service_infection["x"] <=700 && $service_infection["y"] <=700){
    array_push($black_infections, array($service_infection["x"], $service_infection["y"], $service_infection["date"], $service_infection["time"], $service_infection["duration"]));
    }
  }
}else {
  echo 'Curl-Error: ' . curl_error ($handle);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	  <title>COVID-CT: Home Page</title>
    <link rel="stylesheet" href="styles3.css">
    <script type="text/javascript">
    var blackInfections = <?php echo json_encode($black_infections); ?>;
    var redInfections = <?php echo json_encode($red_infections); ?>;
    function createRedMarkers(){
      for(var i=0; i<redInfections.length; i++){
        var redMarker = document.createElement("IMG");
        redMarker.setAttribute("src", "marker_red.png");
        redMarker.setAttribute("width", "20");
        redMarker.setAttribute("height", "20");
        redMarker.setAttribute("position", "absolute");
        redMarker.setAttribute("id", i);
        document.getElementById("columnRight").appendChild(redMarker);
        }
        }
    function createBlackMarkers(){
      for(var j=0; j<blackInfections.length; j++){
    var blackMarker = document.createElement("IMG");
    blackMarker.setAttribute("src", "marker_black.png");
    blackMarker.setAttribute("width", "20");
    blackMarker.setAttribute("height", "20");
    blackMarker.setAttribute("position", "absolute");
    blackMarker.setAttribute("id", "a"+j);
    document.getElementById("columnRight").appendChild(blackMarker);
   }
   }

    function moveMarker(infections, i, id){
      var marker = document.getElementById(id);
      var map = document.getElementById("map");
      var xValue = parseInt(infections[i][0]);
      var yValue = parseInt(infections[i][1]);

      imgCoord= map.getBoundingClientRect();
      var x = imgCoord.left + xValue - 15;
      var y = imgCoord.bottom - yValue - 145;

      marker.style.position = "absolute";
      marker.style.left = (x) +'px';
      marker.style.top = (y)+'px';
      }
    </script>
</head>
<body>
    <h1>COVID - 19 Contact Tracing</h1>
    <div class="page">
        <div class ="container">
          <div class="row">
          <div class="column left">
            <ul class="sidebar">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
              <li><a href="home.php">Home</a></li>
              <li><a href="overview.php">Overview</a></li>
              <li><a href="add_visit.php">Add Visit</a></li>
              <li><a href="report.php">Report</a></li>	
              <li><a href="settings.php">Settings</a></li>
              <li class="logout"> <input type="submit" name="action" value="<?=LOGOUT?>"></li>
            </form>
            </ul>
          </div>
          <div class="column right" id="columnRight">
            <h2>Status</h2>
            <hr>
            <img src="exeter.jpg" class = "image map" id = "map" width = 700px height = 700px align = "right">
            <p>Hi <?=$realName?>, you might have had a connection to an infected person at the location shown in red.</p>
            <br><br><br><br><br><br><br><br><br><br><br>
            <p>Click on the marker to see details about the infection.</p>
          </div>       
        </div>
    </div>  
    <script>
      createRedMarkers();
      createBlackMarkers();
              
      for (var k=0; k<redInfections.length; k++){
        moveMarker(redInfections, k, k);
        }
      for (var l=0; l<blackInfections.length; l++){
        moveMarker(blackInfections, l, "a"+l);
        }


      for (let m=0; m<redInfections.length; m++){
        document.getElementById(m).addEventListener('click', function (e){
          alert("Visit Date: " + redInfections[m][2] + "\nVisit Time: " + redInfections[m][3] + "\nVisit Duration: " + redInfections[m][4]);
          });
      }
      for (let o=0; o<blackInfections.length; o++){
        document.getElementById("a"+o).addEventListener('click', function (e){
          alert("Visit Date: " + blackInfections[o][2] + "\nVisit Time: " + blackInfections[o][3] + "\nVisit Duration: " + blackInfections[o][4]);
          });
      }
    </script>
</body>
</html>
		
    