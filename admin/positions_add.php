<?php
	include 'includes/session.php';

	if(isset($_POST['add'])){
		if(!require_csrf()){
			header('location: positions.php');
			exit();
		}

		$description = clean_input($_POST['description']);
		$max_vote = max(1, (int) $_POST['max_vote']);

		$sql = "SELECT * FROM positions ORDER BY priority DESC LIMIT 1";
		$query = $conn->query($sql);
		$row = $query->fetch_assoc();

		$priority = $row ? $row['priority'] + 1 : 1;
		
		$stmt = $conn->prepare("INSERT INTO positions (description, max_vote, priority) VALUES (?, ?, ?)");
		$stmt->bind_param("sii", $description, $max_vote, $priority);
		if($stmt->execute()){
			$_SESSION['success'] = 'Position added successfully';
		}
		else{
			$_SESSION['error'] = $stmt->error;
		}
		$stmt->close();

	}
	else{
		$_SESSION['error'] = 'Fill up add form first';
	}

	header('location: positions.php');
?>
