<?php
	include 'includes/session.php';

	if(isset($_POST['edit'])){
		if(!require_csrf()){
			header('location: candidates.php');
			exit();
		}

		$id = (int) $_POST['id'];
		$firstname = clean_input($_POST['firstname']);
		$lastname = clean_input($_POST['lastname']);
		$position = (int) $_POST['position'];
		$platform = clean_input($_POST['platform']);

		$stmt = $conn->prepare("UPDATE candidates SET firstname = ?, lastname = ?, position_id = ?, platform = ? WHERE id = ?");
		$stmt->bind_param("ssisi", $firstname, $lastname, $position, $platform, $id);
		if($stmt->execute()){
			$_SESSION['success'] = 'Candidate updated successfully';
		}
		else{
			$_SESSION['error'] = $stmt->error;
		}
		$stmt->close();
	}
	else{
		$_SESSION['error'] = 'Fill up edit form first';
	}

	header('location: candidates.php');

?>
