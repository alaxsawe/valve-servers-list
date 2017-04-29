<?php
include 'core/init.php';
protect_page();


if(empty($_POST) == false) {
	$fields = array('current_password', 'password', 'password_again');
	foreach($_POST as $key=>$value) {
		if(empty($value) && in_array($key, $fields) == true){
			$errors[] = 'All fields are required';
			break 1;
		}
	}

	if(md5($_POST['current_password']) == $user_data['password']) {
		if(trim($_POST['password']) !== trim($_POST['password_again'])) {
			$errors[] = 'New passwords no not match!';
		} elseif(strlen($_POST['password']) < 6) {
			$errors[] = 'Your new password is too short!';
		}
	} else {
		$errors[] = 'Your current password is incorrect!';
	}
}

include 'includes/overall/header.php'; 
?>
<h2>Change Password</h2>
<?php
if(isset($_GET['success']) && empty($_GET['success'])) {
	echo '<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong>Congratulations!</strong> Your password was successfully changed ! <a href="changepassword.php">Back</a></div>';
} else {
if(empty($_POST) == false && empty($errors) == true) {
	change_password($session_user_id, $_POST['password']);
	header('Location: changepassword.php?success');
} else if (empty($errors) == false) {
	echo output_errors($errors);
}
?>
<form action="" method="post">
	<label>Current password</label>
	<input type="password" name="current_password">
	<label>New Password</label>
	<input type="password" name="password">
	<label>New Password Again</label>
	<input type="password" name="password_again"><br />

	<input class="btn btn-primary" type="submit" value="Submit Password">
</form>
	
<?php
}
include 'includes/overall/footer.php';
?>