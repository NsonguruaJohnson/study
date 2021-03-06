<?php 

	// Initialize session
	session_start();

	// $user = "";
	// Check to see if the user is already looged in, if yes then redirect user to welcome page
	// if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
		
	// 	header("location: dashboard.php");
	// 	exit();
	// }

	if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
        // header("location: dashboard.php");
        header("location: ../dashboardpage/dashboard.php");
		exit();
	}

	if (isset($_COOKIE['userid']) || isset($_COOKIE['useremail'])){
        // header("location: dashboard.php");
		header("location: ../dashboardpage/dashboard.php");
	}

	


	// Include config file
    require_once('../config/connect.php');
	
	// Define variables and initialize with empty values
	$email = $password = $emailErr = $passwordErr = $rememberMe = "";

	// Processing form data when form is submitted
	if ($_SERVER["REQUEST_METHOD"] == "POST"){

		function test_input($data){
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
		}

		if (empty($_POST['email'])){
			$emailErr = "Please enter your email";
		} else {
			$email = test_input($_POST['email']);
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
				$emailErr = "Invalid email format";
			}
		}

		if (empty($_POST['password'])){
			$passwordErr = "Please enter your password";
		} else {
			$password = $_POST['password'];
		}

		if (empty($emailErr) && empty($passwordErr)){
			// Prepare a select statement
			$sql = "SELECT id, email, password FROM users WHERE email = :email";

			if ($stmt = $pdo->prepare($sql)){
				// Bind variables to the prepared statement as parameters
				$stmt->bindParam(":email", $param_email, PDO::PARAM_STR);

				// Set Parameters
				$param_email = test_input($_POST['email']);
				// $param_email = $email; This might work as well since the email has been cleaned.

				// Attempt to execute prepared statement
				if ($stmt->execute()){
					// Check if email already exists, if yes then verify password
					if($stmt->rowCount() == 1){
						if ($row = $stmt->fetch()){
							$id = $row["id"];
							$email = $row["email"];
							$hashedPassword = $row["password"];

							// Verify Password
							if (password_verify($password, $hashedPassword)){
								// Password is correct, so start a new session
								session_start();
								// Store data in session variables
								$_SESSION["loggedin"] = true;
								$_SESSION["id"] = $id;
								$_SESSION["email"] = $email;



								// Redirect user to dashboardpage
                                // header("location: dashboard.php");
                                header("location: ../dashboardpage/dashboard.php");
							} else {
								$passwordErr = "The password you entered is incorrect.";
							}

							if (isset($_POST['rememberMe'])){
		
								if($_POST['rememberMe'] == "on"){

									$hour = time() + 3600; // 1 hour
									// $id = $_SESSION['id'];
									// $email = $_SESSION['email'];
									setcookie('userid', $id, $hour);
									setcookie('useremail', $email, $hour);
									// setcookie('active', 1, $hour);
									// header('location: dashboard.php');
								}

							}

						}
					} else {
						$emailErr = "No account found with that email.";
					}

				} else {
					// Display an error message if username doesnt exists.
					echo "Ooops! Something went wrong. please try again later.";
					// Error page
					// header("location: error.php");
				}

				// Close statement
				unset($stmt);
			}

		}

		// var_dump($_POST);
		// if (isset($_POST['rememberMe'])){
		
		// 	if($_POST['rememberMe'] == "on"){

		// 		$hour = time() + 3600; // 1 hour
		// 		// $id = $_SESSION['id'];
		// 		// $email = $_SESSION['email'];
		// 		setcookie('userid', $row["id"], $hour);
		// 		setcookie('useremail', $row["email"], $hour);
		// 		setcookie('active', 1, $hour);
		// 		// header('location: dashboard.php');
		// 	}

		// }

		// var_dump($_POST);

		// Close connection
		unset($pdo);

	}


 ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN PAGE</title>
    <link rel="stylesheet" href="lib/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="lib/fontawesome/css/font-awesome.css">
    

    <!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">-->
    
    <script src="lib/jquery/jquery.js"></script>
    <script src="lib/bootstrap/js/bootstrap.min.js"></script>
     <!--<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>-->
    <!--<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>-->
    <!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>-->
    <link rel="stylesheet" href="style.css">
    <style type="text/css">
    	.error {color: #FF0000;}
    </style>
</head>

<body>
<div class="text-center">
<h2 class=" item-title">
    WELCOME
</h2>
</div>

<div class="container">
<div class="row">
    <div class="col-md-4 offset-md-4">
        <form action="<?php echo htmlspecialchars ($_SERVER['PHP_SELF'])?>" method="POST">
            <div class="form-group">
                <label for="email">Email address:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $email ?>" >
                <span class="error"><?php echo $emailErr ?></span>
            </div>
            <div class="form-group">
                <label for="pwd"><i class="fa fa-eye"></i> Password:</label>
                <input type="password" class="form-control" id="pwd" name="password" >
                <span class="error"><?php echo $passwordErr ?></span>
            </div>
            <div class="rem">
                <div class="form-check">
                     <label class="form-check-label">
                         <input class="form-check-input" type="checkbox" name="rememberMe" value="on" >Remember me
                         </label>
                </div>
                <div>
                    <a href="forgotpassword.php">Forgot Password</a>
                </div>
            </div>     
            <button type="submit" class="btn btn-primary mx-auto">LOGIN</button>   
        </form>
        <div class="login">
            <span>Or Login With</span>
            <a href="#" class="google"><i class="fa fa-google"></i></a>
            <p>Not a member?.... <a href="signup.php">SIGN UP</a></p>
        </div>
    </div>
</div>
</div>
</body>

</html>