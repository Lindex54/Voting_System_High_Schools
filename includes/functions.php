<?php
	function clean_input($value){
		return trim((string) $value);
	}

	function e($value){
		return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
	}

	function csrf_token(){
		if(empty($_SESSION['csrf_token'])){
			$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
		}

		return $_SESSION['csrf_token'];
	}

	function csrf_field(){
		return '<input type="hidden" name="csrf_token" value="'.e(csrf_token()).'">';
	}

	function require_csrf(){
		$token = $_POST['csrf_token'] ?? '';
		if(empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)){
			$_SESSION['error'] = 'Security check failed. Please try again.';
			return false;
		}

		return true;
	}

	function upload_photo($file, $targetDir){
		if(empty($file['name'])){
			return '';
		}

		if(!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK){
			throw new RuntimeException('Photo upload failed.');
		}

		$allowed = array(
			'image/jpeg' => 'jpg',
			'image/png' => 'png',
			'image/gif' => 'gif'
		);

		$finfo = new finfo(FILEINFO_MIME_TYPE);
		$mime = $finfo->file($file['tmp_name']);
		if(!isset($allowed[$mime])){
			throw new RuntimeException('Only JPG, PNG, and GIF photos are allowed.');
		}

		if(!is_dir($targetDir)){
			throw new RuntimeException('Image folder was not found.');
		}

		$filename = 'upload_'.date('YmdHis').'_'.bin2hex(random_bytes(4)).'.'.$allowed[$mime];
		$destination = rtrim($targetDir, '/\\').DIRECTORY_SEPARATOR.$filename;

		if(!move_uploaded_file($file['tmp_name'], $destination)){
			throw new RuntimeException('Could not save uploaded photo.');
		}

		return $filename;
	}

	function get_election_settings($conn){
		$defaults = array(
			'title' => 'Rock High School Elections',
			'status' => 'closed',
			'start_at' => null,
			'end_at' => null
		);

		$query = $conn->query("SHOW TABLES LIKE 'election_settings'");
		if(!$query || $query->num_rows === 0){
			return $defaults;
		}

		$query = $conn->query("SELECT * FROM election_settings WHERE id = 1");
		if($query && $query->num_rows > 0){
			return array_merge($defaults, $query->fetch_assoc());
		}

		return $defaults;
	}

	function election_is_open($conn){
		$settings = get_election_settings($conn);
		if($settings['status'] !== 'open'){
			return false;
		}

		$now = time();
		if(!empty($settings['start_at']) && strtotime($settings['start_at']) > $now){
			return false;
		}

		if(!empty($settings['end_at']) && strtotime($settings['end_at']) < $now){
			return false;
		}

		return true;
	}

	function format_datetime_for_input($value){
		if(empty($value)){
			return '';
		}

		return date('Y-m-d\TH:i', strtotime($value));
	}

	function generate_voter_id($conn){
		$prefix = 'RHS-'.date('Y').'-';
		$like = $prefix.'%';
		$stmt = $conn->prepare("SELECT voters_id FROM voters WHERE voters_id LIKE ? ORDER BY voters_id DESC LIMIT 1");
		$stmt->bind_param("s", $like);
		$stmt->execute();
		$query = $stmt->get_result();
		$number = 1;

		if($query->num_rows > 0){
			$row = $query->fetch_assoc();
			$last = (int) substr($row['voters_id'], -3);
			$number = $last + 1;
		}

		$stmt->close();

		return $prefix.str_pad($number, 3, '0', STR_PAD_LEFT);
	}

	function generate_student_number($conn){
		$prefix = 'RHS-STU-';
		$like = $prefix.'%';
		$stmt = $conn->prepare("SELECT student_number FROM voters WHERE student_number LIKE ? ORDER BY student_number DESC LIMIT 1");
		$stmt->bind_param("s", $like);
		$stmt->execute();
		$query = $stmt->get_result();
		$number = 1;

		if($query->num_rows > 0){
			$row = $query->fetch_assoc();
			$last = (int) substr($row['student_number'], -3);
			$number = $last + 1;
		}

		$stmt->close();

		return $prefix.str_pad($number, 3, '0', STR_PAD_LEFT);
	}
?>
