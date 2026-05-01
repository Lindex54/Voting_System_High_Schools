<?php
	include 'includes/session.php';

	if(isset($_POST['edit'])){
		if(!require_csrf()){
			header('location: positions.php');
			exit();
		}

		$id = (int) $_POST['id'];
		$description = clean_input($_POST['description']);
		$max_vote = max(1, (int) $_POST['max_vote']);

		$stmt = $conn->prepare("UPDATE positions SET description = ?, max_vote = ? WHERE id = ?");
		$stmt->bind_param("sii", $description, $max_vote, $id);
		if($stmt->execute()){
			$_SESSION['success'] = 'Position updated successfully';
		}
		else{
			$_SESSION['error'] = $stmt->error;
		}
		$stmt->close();
	}
	else{
		$_SESSION['error'] = 'Fill up edit form first';
	}

	header('location: positions.php');

?>
