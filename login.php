<?php
include 'core/init.php';
include 'includes/overall/header.php';
logged_in_redirect();

if(empty($_POST) == false) {
	$username = $_POST['username'];
	$password = $_POST['password'];
	
	if(empty($username) || empty($password)) {
		$errors[] = 'You need to fill up the username and password!';
	} else if(user_exists($username) == false) {
		$errors[] = 'This user doesn\'t exists';
	} else if(user_active($username) == false) {
		$errors[] = 'You have\'t activated your account!';
	} else {
		$login = login($username, $password);
		if($login == false) {
			$errors[] = 'Username and password combination is incorrect';
		} else {
			$_SESSION['user_id'] = $login;
			header('Location: index.php');
			exit();
		}
	}
	if(empty($errors) == false) {
		echo '<h2>Login failed...</h2>';
		echo output_errors($errors);
	}
} else {
?>
<h2>Login</h2>
<form action="" method="post">
	<input class="span4" type="text" name="username"  placeholder="Username" tabindex="1" /><br />
	<input class="span4" type="password" name="password" placeholder="Password" tabindex="2" /><br /><br />
	
	<input class="btn btn-primary span4" type="submit" name="submit" value="Login" tabindex="3" />
</form>
<?php
}
include 'includes/overall/footer.php';
?>