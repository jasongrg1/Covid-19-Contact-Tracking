<?php
    define("REGISTER","Register");
    $un = htmlentities($_POST["username"]);
    $pwd = htmlentities($_POST["password"]);
    $name = htmlentities($_POST["name"]);
    $sname = htmlentities($_POST["surename"]);
    $action = htmlentities($_POST["action"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<title>COVID-CT: Registration</title>
    <link rel="stylesheet" href="styles2.css">
</head>
<body>
    <h1>COVID - 19 Contact Tracing</h1>
    <div class="page">
        <div class="register">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <input type="text"  name="name" placeholder="Name" required><br>
            <input type="text" name="surename" placeholder="Surename" required><br>
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="submit" name="action" value="<?=REGISTER?>">
        </form>
        </div>       
    </div>
    <?php
    if (isset($action)) {
        require "auth.php";
        $conn = mysqli_connect(hostname, username, password, database, port);
        if (!$conn) {
            die("Could not connect. " . mysqli_connect_error());
        }
        if ($action === REGISTER) {
            $options = [
                'cost' => 12,
            ];
            $passwd=password_hash($pwd, PASSWORD_BCRYPT, $options);

            $sql = "INSERT INTO users (uname, passwd, name, sname) VALUES (?, ?, ?, ?)";
		
			$stmt = mysqli_stmt_init($conn);
			mysqli_stmt_prepare($stmt, $sql);
            mysqli_stmt_bind_param($stmt, "ssss", $un, $passwd, $name, $sname);
     
			
			if (mysqli_stmt_execute($stmt)===false){
				die("Error2".$sql);
            }else{
                echo '<script type="text/javascript">'; 
                echo 'alert("Registration is successful. \nLogin with the registered details.");';
                echo 'window.location.href = "index.php";';
                echo '</script>';
                }
            }
            mysqli_stmt_free_result($stmt);
            mysqli_close($conn);
        }
    ?>
</body>
</html>
		
    