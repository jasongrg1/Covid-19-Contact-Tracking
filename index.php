<?php
    session_start();
    define("LOGIN","Login");
    $un = htmlentities($_POST["username"]);
    $pwd = htmlentities($_POST["password"]);
    $action = htmlentities($_POST["action"]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<title>COVID-CT: Login</title>
    <link rel="stylesheet" href="styles1.css">
</head>
<body>
    <h1>COVID - 19 Contact Tracing</h1>
    <div class="page">
        <div class="login">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <input type="text"  name="username" placeholder="Username" required><br>
                <input type="password" name="password" placeholder="Password" required><br>
                <input type="submit" name="action" value="<?=LOGIN?>">
                <input type="reset" value="Cancel"><br>
            </form>          
            <a href="registration.php"><button class="button">Register</button></a>  
        </div>       
    </div>
    <?php
    if (isset($action)) {
        require "auth.php";
        $conn = mysqli_connect(hostname, username, password, database, port);
        if (!$conn) {
            die("Could not connect. " . mysqli_connect_error());
        }
        if ($action === LOGIN) {
            $sql = "SELECT passwd from users WHERE uname = ?";


            $stmt = mysqli_stmt_init($conn);
            mysqli_stmt_prepare($stmt, $sql);
            mysqli_stmt_bind_param($stmt, "s", $un);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);


            if (mysqli_stmt_num_rows($stmt) !==1)
                die("Username does not exist");

            mysqli_stmt_bind_result($stmt, $dbPwd);
            mysqli_stmt_fetch($stmt);

            if (password_verify($pwd , $dbPwd)) {
                echo "Password is correct";
                mysqli_stmt_free_result($stmt);
                mysqli_close($conn);
                $_SESSION["name"] = $un;

                header("Location: home.php");
                exit;
            }
            else {
                echo "Wrong password";
            }
        } 
        mysqli_stmt_free_result($stmt);
        mysqli_close($conn);

        session_destroy();
    }
    ?>
</body>
</html>
		
    