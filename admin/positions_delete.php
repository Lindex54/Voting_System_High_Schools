<?php
	include 'includes/session.php';

	if(isset($_POST['delete'])){
		if(!require_csrf()){
			header('location: positions.php');
			exit();
		}

		$id = (int) $_POST['id'];
		$stmt = $conn->prepare("DELETE FROM positions WHERE id = ?");
		$stmt->bind_param("i", $id);
		if($stmt->execute()){
			$_SESSION['success'] = 'Position deleted successfully';
		}
		else{
			$_SESSION['error'] = $stmt->error;
		}
		$stmt->close();
	}
	else{
		$_SESSION['error'] = 'Select item to delete first';
	}

	header('location: positions.php');
	
?>
