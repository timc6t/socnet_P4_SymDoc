<?php
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {  
	
	$usu = check_user($_POST['user'], $_POST['password']);
	if($usu===false){
		$err = true;
		$user = $_POST['user'];
	}else{
		session_start();
		// $usu has two fields: mail and codRes
		$_SESSION['user'] = $usu;
		$_SESSION['cart'] = [];
		header("Location: categories.php");
		return;
	}	
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Login form</title>
		<meta charset = "UTF-8">
	</head>
	<body>	
		<?php if(isset($_GET["redirected"])){
			echo "<p>Login to continue</p>";
		}?>
		<?php if(isset($err) and $err == true){
			echo "<p>Check user and password</p>";
		}?>
		<form action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method = "POST">
			<label for = "user">user</label> 
			<input value = "<?php if(isset($user))echo $user;?>"
			id = "user" name = "user" type = "text">		
			<label for = "password">password</label> 
			<input id = "password" name = "password" type = "password">					
			<input type = "submit">
		</form>
	</body>
</html>