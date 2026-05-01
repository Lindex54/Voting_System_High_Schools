<?php
	include 'includes/session.php';

	if(isset($_POST['upload'])){
		if(!require_csrf()){
			header('location: candidates.php');
			exit();
		}

		$id = (int) $_POST['id'];
		try{
			$filename = upload_photo($_FILES['photo'], '../images');
		}
		catch(RuntimeException $e){
			$_SESSION['error'] = $e->getMessage();
			header('location: candidates.php');
			exit();
		}
		
		$stmt = $conn->prepare("UPDATE candidates SET photo = ? WHERE id = ?");
		$stmt->bind_param("si", $filename, $id);
		if($stmt->execute()){
			$_SESSION['success'] = 'Photo updated successfully';
		}
		else{
			$_SESSION['error'] = $stmt->error;
		}
		$stmt->close();

	}
	else{
		$_SESSION['error'] = 'Select candidate to update photo first';
	}

	header('location: candidates.php');
?>
