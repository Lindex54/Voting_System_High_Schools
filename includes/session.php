<?php
	include 'includes/conn.php';
	include 'includes/functions.php';
	session_start();

	if(isset($_SESSION['voter'])){
		$stmt = $conn->prepare("SELECT * FROM voters WHERE id = ?");
		$stmt->bind_param("i", $_SESSION['voter']);
		$stmt->execute();
		$query = $stmt->get_result();
		$voter = $query->fetch_assoc();
		$stmt->close();
	}
	else{
		header('location: index.php');
		exit();
	}

?>
