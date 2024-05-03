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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>COVID-CT: Visits Overview</title>
  <link rel="stylesheet" href="styles4.css">
</head>
<body>
    <h1>COVID - 19 Contact Tracing</h1>
    <div class="page">
        <div class ="container">
          <div class="row">
          <div class="column left">
            <ul class="sidebar">
              <li><a href="home.php">Home</a></li>
              <li><a href="overview.php">Overview</a></li>
              <li><a href="add_visit.php">Add Visit</a></li>
              <li><a href="report.php">Report</a></li>	
              <li><a href="settings.php">Settings</a></li>
              <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
              <li class="logout"> <input type="submit" name="action" value="<?=LOGOUT?>"></li>
              </form>
            </ul>
          </div>
          <div class="column right">
          <div id="table">
          <table>
          <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Duration</th>
            <th>X</th>
            <th>Y</th>
            </tr>
          <?php
            require "auth.php";
            $conn = mysqli_connect(hostname, username, password, database, port);
            if (!$conn) {
              die("Could not connect. " . mysqli_connect_error());
            }
            $sql = "SELECT visits.id, visits.x, visits.y, visits.date, visits.time, visits.duration from visits WHERE user = ?";

            $stmt = mysqli_stmt_init($conn);
            mysqli_stmt_prepare($stmt, $sql);
            mysqli_stmt_bind_param($stmt, "s", $name);

            if (mysqli_stmt_execute($stmt)===false){
              die("Error executing".$sql);
            }

            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) > 0) {
              $visits=[];
              mysqli_stmt_bind_result($stmt, $visitId, $visitX, $visitY, $visitDate, $visitTime, $visitDuration);
              while(mysqli_stmt_fetch($stmt)) {
                array_push($visits, array("id"=>$visitId,"x"=>$visitX, "y"=>$visitY, "date"=>$visitDate, "time"=>$visitTime, "duration"=>$visitDuration));
              }
              foreach($visits as $visit) {
                echo "<tr>";
                echo "<td>" . $visit['date'] . "</td>";
                echo "<td>" . $visit['time'] . "</td>";
                echo "<td>" . $visit['duration'] . "</td>";
                echo "<td>" . $visit['x'] . "</td>";
                echo "<td>" . $visit['y'] . "</td>";
                echo "<td>" . "<input type='image' value='". $visit['id'] . "' onclick='removeVisit(this)' class='button' src='cross.png'></td></tr>"  . "</td>";
                echo "</tr>";
              }
            }
            mysqli_stmt_free_result($stmt);
            mysqli_close($conn);
            ?>
            </table>
          </div>
          </div>       
        </div>
    </div>
    <script>
    function removeVisit(button) {
      var xmlhttp=new XMLHttpRequest();
      xmlhttp.onreadystatechange=function() {
        if (this.readyState==4 && this.status==200) {
          window.location = window.location.pathname;
          }
          }
      xmlhttp.open("GET", "visit_table.php?q="+button.value, true);
      xmlhttp.send();
  }
</script>
</body>
</html>
		
    