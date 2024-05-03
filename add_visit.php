<?php
session_start();
if(!isset($_SESSION["name"])) {
    header("Location: index.php");
    exit();
}else {
    $name = $_SESSION["name"];
}

define("LOGOUT","Logout");
define("ADD","Add");
$action = $_POST["action"];

if (isset($action) && $action ===LOGOUT) {
  session_destroy();
  header("Location: index.php");
  exit;
}   

$x_value = htmlentities($_POST["X"]);
$y_value = htmlentities($_POST["Y"]);
$date = htmlentities($_POST["date"]);
$time = htmlentities($_POST["time"]);
$duration = htmlentities($_POST["duration"]);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	  <title>COVID - 19 Contact Tracing</title>
    <link rel="stylesheet" href="styles5.css">
    <script>
      function moveMarker(x, y){
        var marker = document.getElementById("marker");
        marker.style.position = "absolute";
        marker.style.left = (x - 15) +'px';
        marker.style.top = (y - 145)+'px';
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
          <div class="column right">
            <h2>Add a new visit</h2>
            <hr>
            <img src="exeter.jpg" id= "map" width = 700px height = 700px align = "right">
            <img src="marker_black.png" id= "marker" align = "right">
              <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
              <input type="text"  name="date" placeholder="Date" required><br>
                <input type="text" name="time" placeholder="Time" required><br>
                <input type="text" name="duration" placeholder="Duration" required><br>
                <input type="submit" name="action" value="<?=ADD?>"><br>
                <input type="reset" value="Cancel"><br>
                <input type="hidden" id="X" name="X" value="-">
                <input type="hidden" id="Y" name="Y" value="-">
            </form>
          </div>       
        </div>
    </div>
    <script>
        document.getElementById("map").addEventListener('click', function (e){
        imgCoord= this.getBoundingClientRect();
        var x = e.clientX - imgCoord.left;
        var y = imgCoord.bottom - e.clientY;
        document.getElementById("X").value = Math.round(x);
        document.getElementById("Y").value = Math.round(y);
        moveMarker(e.clientX, e.clientY);
      });
    </script>
    <?php
    if ($action === ADD) {
      require "auth.php";
      $conn = mysqli_connect(hostname, username, password, database, port);
      if (!$conn) {
        echo '<script language="javascript">';
        echo 'alert("Could not connect")';
        echo '</script>';
        die;
      }

      if (!DateTime::createFromFormat('Y-m-d', $date)) {
        echo '<script language="javascript">';
        echo 'alert("Date needs to be in yyyy-mm-dd format")';
        echo '</script>';
        die;
      }
      if (!DateTime::createFromFormat('H:i:s', $time)) {
        echo '<script language="javascript">';
        echo 'alert("Time must be in hh:mm:ss format")';
        echo '</script>';
        die;
      }
      if (!is_numeric($duration)){
        echo '<script language="javascript">';
        echo 'alert("Duration must be an integer.")';
        echo '</script>';
        die;
      }
      if (!is_numeric($x_value)){
        echo '<script language="javascript">';
        echo 'alert("Click the location on the map.")';
        echo '</script>';
        die;
      }
      else{
        $sql = "INSERT INTO visits (x, y, date, time, duration, user) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);
        mysqli_stmt_prepare($stmt, $sql);
        mysqli_stmt_bind_param($stmt, "iissis", $x_value, $y_value, $date, $time, $duration, $name);
      
        if (mysqli_stmt_execute($stmt)===false){
				  die;
        }else{
          echo '<script language="javascript">';
          echo 'alert("Visit successfully added.")';
          echo '</script>';
        }
      }
      mysqli_stmt_free_result($stmt);
      mysqli_close($conn);
    }
    ?>
</body>
</html>
		