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


$date = htmlentities($_POST["date"]);
$time = htmlentities($_POST["time"]);


if (($handle = curl_init())===false) {
  echo 'Curl-Error: ' . curl_error($handle);
} else {
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_FAILONERROR, true);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	  <title>COVID-CT: Visits Overview</title>
    <link rel="stylesheet" href="styles6.css">
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
            <h2>Report an Infection</h2>
            <hr>
            <p>Please report the date and time when you were tested positive for COVID - 19.
            </p><br><br><br><br>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <input type="text"  name="date" placeholder="Date" required><br>
            <input type="text"  name="time" placeholder="Time" required><br>
            <input type="submit" name="action" value="<?=REPORT?>" align = "left">
            <input type="reset" value="Cancel" align = "right"><br>
            </form>
          </div>       
        </div>
    </div>
    <?php
    if ($action === REPORT) {
      require "auth.php";
      $conn = mysqli_connect(hostname, username, password, database, port);
      if (!$conn) {
         die("Could not connect. " . mysqli_connect_error());
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
      else{
        $sql = "INSERT INTO infections (infection_date, infection_time, user) VALUES (?, ?, ?)";
        
        $stmt = mysqli_stmt_init($conn);
	  		mysqli_stmt_prepare($stmt, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $date, $time, $name);
        
        
        if (mysqli_stmt_execute($stmt)===false){
	  			die("Error2".$sql);
        }
        mysqli_stmt_free_result($stmt);
        mysqli_close($conn);
  
        $conn = mysqli_connect(hostname, username, password, database, port);
        if (!$conn) {
          die("Could not connect. " . mysqli_connect_error());
        }
  
        $sql = "SELECT x, y, date, time, duration FROM visits WHERE user = ?";
        $stmt = mysqli_stmt_init($conn);
	  		mysqli_stmt_prepare($stmt, $sql);
        mysqli_stmt_bind_param($stmt, "s", $name);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
      
        $url = "http://ml-lab-7b3a1aae-e63e-46ec-90c4-4e430b434198.ukwest.cloudapp.azure.com:60999/report"; 
        $infections=[];
  
        if (mysqli_stmt_num_rows($stmt) > 0) {
          mysqli_stmt_bind_result($stmt, $dbX, $dbY, $dbDate, $dbTime, $dbDuration);
          while(mysqli_stmt_fetch($stmt)) {
            array_push($infections, array("x"=>$dbX, "y"=>$dbY, "date"=>$dbDate, "time"=>$dbTime, "duration"=>$dbDuration));
          }
          foreach($infections as $infection) {
            if (($handle = curl_init())===false) {
                echo 'Curl-Error: ' . curl_error($handle);
              } else {
                curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($handle, CURLOPT_FAILONERROR, true);
              }
            curl_setopt($handle, CURLOPT_URL, $url);
            curl_setopt($handle, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
            curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($infection));
            curl_exec($handle);
          }
          mysqli_stmt_free_result($stmt);
          mysqli_close($conn);    
        }
      }
    }
    ?>
</body>
</html>
		