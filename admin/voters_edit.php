<?php
	include 'includes/session.php';

	if(isset($_POST['edit'])){
		if(!require_csrf()){
			header('location: voters.php');
			exit();
		}

		$id = (int) $_POST['id'];
		$firstname = clean_input($_POST['firstname']);
		$lastname = clean_input($_POST['lastname']);
		$class = clean_input($_POST['class'] ?? '');
		$stream = clean_input($_POST['stream'] ?? '');
		$password = $_POST['password'] ?? '';

		$stmt = $conn->prepare("SELECT * FROM voters WHERE id = ?");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$query = $stmt->get_result();
		$row = $query->fetch_assoc();
		$stmt->close();

		if(empty($password)){
			$password = $row['password'];
		}
		else{
			$password = password_hash($password, PASSWORD_DEFAULT);
		}

		$stmt = $conn->prepare("UPDATE voters SET firstname = ?, lastname = ?, class = ?, stream = ?, password = ? WHERE id = ?");
		$stmt->bind_param("sssssi", $firstname, $lastname, $class, $stream, $password, $id);
		if($stmt->execute()){
			$_SESSION['success'] = 'Voter updated successfully';
		}
		else{
			$_SESSION['error'] = $stmt->error;
		}
		$stmt->close();
	}
	else{
		$_SESSION['error'] = 'Fill up edit form first';
	}

	header('location: voters.php');

?>
