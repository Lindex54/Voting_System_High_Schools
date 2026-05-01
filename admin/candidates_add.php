<?php
	include 'includes/session.php';

	if(isset($_POST['add'])){
		if(!require_csrf()){
			header('location: candidates.php');
			exit();
		}

		$firstname = clean_input($_POST['firstname']);
		$lastname = clean_input($_POST['lastname']);
		$position = (int) $_POST['position'];
		$platform = clean_input($_POST['platform']);
		try{
			$filename = upload_photo($_FILES['photo'], '../images');
		}
		catch(RuntimeException $e){
			$_SESSION['error'] = $e->getMessage();
			header('location: candidates.php');
			exit();
		}

		$stmt = $conn->prepare("INSERT INTO candidates (position_id, firstname, lastname, photo, platform) VALUES (?, ?, ?, ?, ?)");
		$stmt->bind_param("issss", $position, $firstname, $lastname, $filename, $platform);
		if($stmt->execute()){
			$_SESSION['success'] = 'Candidate added successfully';
		}
		else{
			$_SESSION['error'] = $stmt->error;
		}
		$stmt->close();

	}
	else{
		$_SESSION['error'] = 'Fill up add form first';
	}

	header('location: candidates.php');
?>
