<?php
session_start();
if(!isset($_SESSION["name"])) {
    header("Location: index.php");
    exit();
}else {
    $name = $_SESSION["name"];
}
define("LOGOUT","Logout");
define("REPORT","Report");

$action = htmlentities($_POST["action"]);

if (isset($action) && $action ===LOGOUT) {
  session_destroy();
  header("Location: index.php");
  exit;
}   


if(isset($_POST["window"]) && isset($_POST["distance"])) {
    $window = htmlentities($_POST["window"]);
    $distance = htmlentities($_POST["distance"]);
    setcookie("window", $window, time() + (86400 * 30));
    setcookie("distance", $distance, time() + (86400 * 30));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	  <title>COVID-CT: Settings</title>
    <link rel="stylesheet" href="styles7.css">
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
            <h2>Alert Settings</h2>
            <hr>
            <p>Here you may change the alert distance and the time span for which the contact tracing will be performed.
            </p>
            <br>
            <br>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
              <label for="window">window</label>
              <select name="window" id="window">
                <option value="7">One Week</option>
                <option value="14">Two Weeks</option>
                <option value="21">Three Weeks</option>
                <option value="28">Four Weeks</option>
              </select><br>
              <label for="distance">distance</label>
              <input type="number"  name="distance" min="0" max="500" required><br>
              <input type="submit" name="action" value="Report" align = "left">
              <input type="reset" value="Cancel" align = "right"><br>
            </form>
          </div>       
        </div>
    </div>
</body>
</html>
		