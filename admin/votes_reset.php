<?php
	include 'includes/session.php';

	if(isset($_POST['reset'])){
		if(!require_csrf()){
			header('location: votes.php');
			exit();
		}

		$sql = "DELETE FROM votes";
		if($conn->query($sql)){
			$_SESSION['success'] = "Votes reset successfully";
		}
		else{
			$_SESSION['error'] = "Something went wrong in resetting";
		}
	}
	else{
		$_SESSION['error'] = "Confirm reset first";
	}

	header('location: votes.php');

?>
