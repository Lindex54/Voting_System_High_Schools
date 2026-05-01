<?php
	include 'includes/session.php';

	function countRows($conn, $sql){
		$query = $conn->query($sql);
		$row = $query->fetch_assoc();
		return (int) $row['total'];
	}

	function generateResultsRows($conn){
		$contents = '';
		$positions = $conn->query("SELECT * FROM positions ORDER BY priority ASC");

		while($position = $positions->fetch_assoc()){
			$position_id = (int) $position['id'];
			$stmt = $conn->prepare("
				SELECT candidates.firstname, candidates.lastname, COUNT(votes.id) AS total_votes
				FROM candidates
				LEFT JOIN votes ON votes.candidate_id = candidates.id
				WHERE candidates.position_id = ?
				GROUP BY candidates.id
				ORDER BY total_votes DESC, candidates.lastname ASC, candidates.firstname ASC
			");
			$stmt->bind_param("i", $position_id);
			$stmt->execute();
			$candidates = $stmt->get_result();

			$rows = array();
			$topVotes = null;
			while($candidate = $candidates->fetch_assoc()){
				$votes = (int) $candidate['total_votes'];
				if($topVotes === null){
					$topVotes = $votes;
				}
				$rows[] = $candidate;
			}
			$stmt->close();

			$contents .= '
				<tr style="background-color:#d9edf7;">
					<td colspan="3" align="center"><b>'.e($position['description']).'</b></td>
				</tr>
				<tr>
					<td width="60%"><b>Candidate</b></td>
					<td width="20%" align="center"><b>Votes</b></td>
					<td width="20%" align="center"><b>Result</b></td>
				</tr>
			';

			if(empty($rows)){
				$contents .= '<tr><td colspan="3">No candidates added.</td></tr>';
				continue;
			}

			foreach($rows as $candidate){
				$votes = (int) $candidate['total_votes'];
				$isWinner = $topVotes !== null && $topVotes > 0 && $votes === $topVotes;
				$contents .= '
					<tr>
						<td>'.e($candidate['lastname'].', '.$candidate['firstname']).'</td>
						<td align="center">'.$votes.'</td>
						<td align="center">'.($isWinner ? '<b>Winner</b>' : '').'</td>
					</tr>
				';
			}
		}

		return $contents;
	}

	function generateTurnoutRows($conn){
		$contents = '';
		$sql = "
			SELECT
				COALESCE(NULLIF(voters.class, ''), 'Unassigned') AS class_name,
				COALESCE(NULLIF(voters.stream, ''), 'Unassigned') AS stream_name,
				COUNT(voters.id) AS total_voters,
				COUNT(DISTINCT votes.voters_id) AS voters_voted
			FROM voters
			LEFT JOIN votes ON votes.voters_id = voters.id
			GROUP BY class_name, stream_name
			ORDER BY class_name ASC, stream_name ASC
		";
		$query = $conn->query($sql);

		while($row = $query->fetch_assoc()){
			$total = (int) $row['total_voters'];
			$voted = (int) $row['voters_voted'];
			$turnout = $total > 0 ? round(($voted / $total) * 100, 1) : 0;
			$contents .= '
				<tr>
					<td>'.e($row['class_name']).'</td>
					<td>'.e($row['stream_name']).'</td>
					<td align="center">'.$total.'</td>
					<td align="center">'.$voted.'</td>
					<td align="center">'.$turnout.'%</td>
				</tr>
			';
		}

		if($contents === ''){
			$contents = '<tr><td colspan="5">No voters found.</td></tr>';
		}

		return $contents;
	}

	$settings = get_election_settings($conn);
	$title = $settings['title'];
	$statusLabel = array('draft' => 'Not Started', 'open' => 'Open', 'closed' => 'Closed');
	$totalVoters = countRows($conn, "SELECT COUNT(*) AS total FROM voters");
	$votersVoted = countRows($conn, "SELECT COUNT(DISTINCT voters_id) AS total FROM votes");
	$turnout = $totalVoters > 0 ? round(($votersVoted / $totalVoters) * 100, 1) : 0;
	$printedAt = date('M d, Y h:i A');

	require_once('../tcpdf/tcpdf.php');
	$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetTitle('Rock High School Results: '.$title);
	$pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
	$pdf->SetDefaultMonospacedFont('helvetica');
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetMargins(PDF_MARGIN_LEFT, '10', PDF_MARGIN_RIGHT);
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);
	$pdf->SetAutoPageBreak(TRUE, 10);
	$pdf->SetFont('helvetica', '', 10);
	$pdf->AddPage();

	$content = '
		<h1 align="center">Rock High School</h1>
		<h2 align="center">'.e($title).'</h2>
		<h4 align="center">Official Election Results</h4>
		<table border="1" cellspacing="0" cellpadding="4">
			<tr>
				<td width="25%"><b>Status</b></td>
				<td width="25%">'.e($statusLabel[$settings['status']] ?? 'Closed').'</td>
				<td width="25%"><b>Printed</b></td>
				<td width="25%">'.e($printedAt).'</td>
			</tr>
			<tr>
				<td><b>Total Voters</b></td>
				<td>'.$totalVoters.'</td>
				<td><b>Voters Voted</b></td>
				<td>'.$votersVoted.'</td>
			</tr>
			<tr>
				<td><b>Turnout</b></td>
				<td>'.$turnout.'%</td>
				<td><b>Voting Window</b></td>
				<td>'.e((!empty($settings['start_at']) ? date('M d, Y h:i A', strtotime($settings['start_at'])) : 'Not set').' - '.(!empty($settings['end_at']) ? date('M d, Y h:i A', strtotime($settings['end_at'])) : 'Not set')).'</td>
			</tr>
		</table>
		<br>
		<h3>Results By Position</h3>
		<table border="1" cellspacing="0" cellpadding="4">
			'.generateResultsRows($conn).'
		</table>
		<br>
		<h3>Class And Stream Turnout</h3>
		<table border="1" cellspacing="0" cellpadding="4">
			<tr style="background-color:#f5f5f5;">
				<td width="25%"><b>Class</b></td>
				<td width="25%"><b>Stream</b></td>
				<td width="15%" align="center"><b>Voters</b></td>
				<td width="20%" align="center"><b>Voted</b></td>
				<td width="15%" align="center"><b>Turnout</b></td>
			</tr>
			'.generateTurnoutRows($conn).'
		</table>
	';

	$pdf->writeHTML($content);
	$pdf->Output('rock_high_school_election_results.pdf', 'I');
?>
