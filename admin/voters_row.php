<?php 
	include 'includes/session.php';

	if(isset($_POST['id'])){
		$id = (int) $_POST['id'];
		$stmt = $conn->prepare("SELECT id, voters_id, student_number, firstname, lastname, class, stream, photo FROM voters WHERE id = ?");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$query = $stmt->get_result();
		$row = $query->fetch_assoc();
		$stmt->close();

		echo json_encode($row);
	}
?>
