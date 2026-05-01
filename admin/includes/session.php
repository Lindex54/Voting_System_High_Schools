<?php
	session_start();
	include 'includes/conn.php';
	include 'includes/functions.php';

	if(!isset($_SESSION['admin']) || trim($_SESSION['admin']) == ''){
		header('location: index.php');
	}

	$stmt = $conn->prepare("SELECT * FROM admin WHERE id = ?");
	$stmt->bind_param("i", $_SESSION['admin']);
	$stmt->execute();
	$query = $stmt->get_result();
	$user = $query->fetch_assoc();
	$stmt->close();
	
?>
