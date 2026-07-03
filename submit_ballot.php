<?php
	include 'includes/session.php';
	include 'includes/slugify.php';

	if(!isset($_POST['vote'])){
		$_SESSION['error'][] = 'Select candidates to vote first';
		header('location: home.php');
		exit;
	}

	if(count($_POST) == 1){
		$_SESSION['error'][] = 'Please vote atleast one candidate';
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
		$_SESSION['error'][] = 'Please vote atleast one candidate';
		header('location: home.php');
		exit;
	}

	// --- Double-submission guard ---
	// Everything below runs inside one transaction. We re-check "has this
	// voter already voted?" here, right before writing, with a row lock
	// (FOR UPDATE) rather than trusting the check the ballot page did on
	// load. This is what actually closes the race: if two requests from
	// the same voter arrive together, the second one blocks on the lock
	// until the first transaction finishes, then sees the vote already
	// exists and backs out cleanly instead of inserting a duplicate.
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
			if($conn->errno == 1062){ // duplicate key — the DB-level constraint caught it
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
		$_SESSION['success'] = 'Ballot Submitted';
	}

	header('location: home.php');
	exit;
?>