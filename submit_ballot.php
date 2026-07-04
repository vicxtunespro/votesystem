<?php
	include 'includes/session.php';
	include 'includes/slugify.php';

	// Debug - remove this line in production
	// echo "<pre>";
	// print_r($_POST);
	// exit;

	// Check if any POST data exists
	if(empty($_POST)){
		$_SESSION['error'][] = 'Select candidates to vote first';
		header('location: home.php');
		exit;
	}

	// Count how many positions have votes (excluding the 'vote' button)
	$voteButton = isset($_POST['vote']) ? 1 : 0;
	if(count($_POST) <= $voteButton){
		$_SESSION['error'][] = 'Please vote at least one candidate';
		header('location: home.php');
		exit;
	}

	$_SESSION['post'] = $_POST;

	// Validate against max_vote per position and build the list of rows to
	// insert. No writes happen yet — validation first, writes second.
	$sql = "SELECT * FROM positions";
	$query = $conn->query($sql);
	$errors = array();
	$votesToInsert = array();

	while($row = $query->fetch_assoc()){
		$position = slugify($row['description']);
		$pos_id = (int) $row['id'];

		// Check if this position has votes submitted (using slug as key)
		if(isset($_POST[$position])){
			if($row['max_vote'] > 1){
				$selected = (array) $_POST[$position];
				if(count($selected) > $row['max_vote']){
					$errors[] = 'You can only choose '.$row['max_vote'].' candidates for '.$row['description'];
				}
				else{
					foreach($selected as $candidateId){
						$votesToInsert[] = array('candidate_id' => (int) $candidateId, 'position_id' => $pos_id);
					}
				}
			}
			else{
				$votesToInsert[] = array('candidate_id' => (int) $_POST[$position], 'position_id' => $pos_id);
			}
		}
	}

	if(!empty($errors)){
		$_SESSION['error'] = $errors;
		header('location: home.php');
		exit;
	}

	if(empty($votesToInsert)){
		$_SESSION['error'][] = 'Please vote at least one candidate';
		header('location: home.php');
		exit;
	}

	// --- Double-submission guard ---
	$conn->begin_transaction();

	$lockStmt = $conn->prepare("SELECT id FROM votes WHERE voters_id = ? LIMIT 1 FOR UPDATE");
	$lockStmt->bind_param("s", $voter['id']);
	$lockStmt->execute();
	$alreadyVoted = $lockStmt->get_result()->num_rows > 0;
	$lockStmt->close();

	if($alreadyVoted){
		$conn->rollback();
		unset($_SESSION['post']);
		$_SESSION['error'][] = 'You have already voted for this election.';
		header('location: home.php');
		exit;
	}

	$insertStmt = $conn->prepare("INSERT INTO votes (voters_id, candidate_id, position_id) VALUES (?, ?, ?)");
	$insertFailed = false;
	$duplicateKey = false;

	foreach($votesToInsert as $v){
		$insertStmt->bind_param("sii", $voter['id'], $v['candidate_id'], $v['position_id']);
		if(!$insertStmt->execute()){
			$insertFailed = true;
			if($conn->errno == 1062){ // duplicate key
				$duplicateKey = true;
			}
			break;
		}
	}
	$insertStmt->close();

	if($insertFailed){
		$conn->rollback();
		unset($_SESSION['post']);
		$_SESSION['error'][] = $duplicateKey
			? 'You have already voted for this election.'
			: 'Something went wrong submitting your ballot. Please try again.';
	}
	else{
		$conn->commit();
		unset($_SESSION['post']);
		$_SESSION['success'] = 'Ballot Submitted Successfully!';
	}

	header('location: home.php');
	exit;
?>