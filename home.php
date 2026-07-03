<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue layout-top-nav">
<div class="wrapper">

	<?php include 'includes/navbar.php'; ?>

	  <div class="content-wrapper">
	    <div class="container">

	      <!-- Main content -->
	      <section class="content ballot-content">
	      	<?php
	      		$parse = parse_ini_file('admin/config.ini', FALSE, INI_SCANNER_RAW);
    			$title = $parse['election_title'];
	      	?>
	      	<h1 class="page-header text-center title"><b><?php echo strtoupper($title); ?></b></h1>
	        <div class="row">
	        	<div class="col-sm-10 col-sm-offset-1">
	        		<?php
				        if(isset($_SESSION['error'])){
				        	?>
				        	<div class="alert alert-danger alert-dismissible">
				        		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					        	<ul>
					        		<?php
					        			foreach($_SESSION['error'] as $error){
					        				echo "
					        					<li>".$error."</li>
					        				";
					        			}
					        		?>
					        	</ul>
					        </div>
				        	<?php
				         	unset($_SESSION['error']);

				        }
				        if(isset($_SESSION['success'])){
				          	echo "
				            	<div class='alert alert-success alert-dismissible'>
				              		<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
				              		<h4><i class='icon fa fa-check'></i> Success!</h4>
				              	".$_SESSION['success']."
				            	</div>
				          	";
				          	unset($_SESSION['success']);
				        }

				    ?>

				    <div class="alert alert-danger alert-dismissible" id="alert" style="display:none;">
		        		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			        	<span class="message"></span>
			        </div>

				    <?php
				    	// NOTE: consider moving this to a prepared statement — see chat note on SQL injection risk.
				    	$sql = "SELECT * FROM votes WHERE voters_id = '".$voter['id']."'";
				    	$vquery = $conn->query($sql);
				    	if($vquery->num_rows > 0){
				    		?>
				    		<div class="text-center already-voted">
					    		<div class="check-badge"><i class="fa fa-check"></i></div>
					    		<h3>You have already voted for this election.</h3>
					    		<p class="text-muted">Your ballot was received and recorded.</p>
					    		<a href="#view" data-toggle="modal" class="btn btn-flat btn-primary btn-lg">View Ballot</a>
					    	</div>
				    		<?php
				    	}
				    	else{
				    		?>
			    			<!-- Voting Ballot -->
						    <form method="POST" id="ballotForm" action="submit_ballot.php">
				        		<?php
				        			include 'includes/slugify.php';

				        			$candidate = '';
				        			$sql = "SELECT * FROM positions ORDER BY priority ASC";
									$query = $conn->query($sql);
									while($row = $query->fetch_assoc()){
										$sql = "SELECT * FROM candidates WHERE position_id='".$row['id']."'";
										$cquery = $conn->query($sql);
										while($crow = $cquery->fetch_assoc()){
											$slug = slugify($row['description']);
											$checked = '';
											if(isset($_SESSION['post'][$slug])){
												$value = $_SESSION['post'][$slug];

												if(is_array($value)){
													foreach($value as $val){
														if($val == $crow['id']){
															$checked = 'checked';
														}
													}
												}
												else{
													if($value == $crow['id']){
														$checked = 'checked';
													}
												}
											}
											$inputId = $slug.'-'.$crow['id'];
											$input = ($row['max_vote'] > 1) ? '<input type="checkbox" id="'.$inputId.'" class="ballot-input '.$slug.'" name="'.$slug."[]".'" value="'.$crow['id'].'" '.$checked.'>' : '<input type="radio" id="'.$inputId.'" class="ballot-input '.$slug.'" name="'.slugify($row['description']).'" value="'.$crow['id'].'" '.$checked.'>';
											$image = (!empty($crow['photo'])) ? 'images/'.$crow['photo'] : 'images/profile.jpg';
											$candidate .= '
												<li>
													<label for="'.$inputId.'" class="candidate-card">
														'.$input.'
														<span class="candidate-photo"><img src="'.$image.'" alt="'.$crow['firstname'].' '.$crow['lastname'].'"></span>
														<span class="cname">'.$crow['firstname'].' '.$crow['lastname'].'</span>
														<span class="candidate-check"><i class="fa fa-check"></i></span>
													</label>
												</li>
											';
										}

										$instruct = ($row['max_vote'] > 1) ? 'You may select up to '.$row['max_vote'].' candidates' : 'Select only one candidate';

										echo '
											<div class="row">
												<div class="col-xs-12">
													<div class="ballot-box" id="'.$row['id'].'">
														<div class="ballot-box-header">
															<h3 class="box-title">'.$row['description'].'</h3>
															<p class="instruct">'.$instruct.'
																<span class="pull-right">
																	<button type="button" class="btn btn-default btn-sm btn-flat reset" data-desc="'.slugify($row['description']).'"><i class="fa fa-refresh"></i> Reset</button>
																</span>
															</p>
														</div>
														<div id="candidate_list">
															<ul class="candidate-grid">
																'.$candidate.'
															</ul>
														</div>
													</div>
												</div>
											</div>
										';

										$candidate = '';

									}

				        		?>
				        		<div class="text-center ballot-actions">
					        		<button type="button" class="btn btn-default btn-flat" id="preview"><i class="fa fa-file-text"></i> Preview</button>
					        		<button type="submit" id="vote" class="vote btn btn-primary btn-flat" name="vote"><i class="fa fa-check-square-o"></i> Submit</button>
					        	</div>
				        	</form>
				        	<!-- End Voting Ballot -->
				    		<?php
				    	}

				    ?>

	        	</div>
	        </div>
	      </section>

	    </div>
	  </div>

  	<?php include 'includes/footer.php'; ?>
  	<?php include 'includes/ballot_modal.php'; ?>
</div>

<style>
	:root {
		--blue: #2455D9;
		--blue-dark: #1B3EAD;
		--ink: #14264A;
		--slate: #5B6478;
		--line: #DBE2EE;
		--paper: #F4F6FA;
	}

	.ballot-content .title { color: var(--ink); }

	.ballot-box {
		background: #fff;
		border: 1px solid var(--line);
		border-radius: 10px;
		margin-bottom: 22px;
		overflow: hidden;
	}

	.ballot-box-header {
		padding: 16px 20px 12px;
		border-bottom: 1px solid var(--line);
		background: var(--paper);
	}

	.ballot-box-header .box-title {
		margin: 0 0 6px;
		font-size: 17px;
		color: var(--ink);
	}

	.ballot-box-header .instruct {
		margin: 0;
		font-size: 13px;
		color: var(--slate);
	}

	#candidate_list { padding: 18px 20px; }

	.candidate-grid {
		list-style: none;
		margin: 0;
		padding: 0;
		display: grid;
		grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
		gap: 14px;
	}

	.candidate-card {
		position: relative;
		display: flex;
		flex-direction: column;
		align-items: center;
		text-align: center;
		border: 2px solid var(--line);
		border-radius: 10px;
		padding: 16px 12px 14px;
		cursor: pointer;
		margin: 0;
		font-weight: 400;
		transition: border-color 0.15s ease, background 0.15s ease;
	}

	.candidate-card:hover { border-color: #A9BCE8; }

	.ballot-input {
		position: absolute;
		opacity: 0;
		width: 1px;
		height: 1px;
	}

	.candidate-photo {
		width: 72px;
		height: 72px;
		border-radius: 50%;
		overflow: hidden;
		margin-bottom: 10px;
		border: 1px solid var(--line);
	}

	.candidate-photo img { width: 100%; height: 100%; object-fit: cover; }

	.cname {
		font-size: 13px;
		font-weight: 600;
		color: var(--ink);
	}

	.candidate-check {
		position: absolute;
		top: 8px;
		right: 8px;
		width: 20px;
		height: 20px;
		border-radius: 50%;
		background: var(--blue);
		color: #fff;
		display: none;
		align-items: center;
		justify-content: center;
		font-size: 11px;
	}

	.ballot-input:checked + .candidate-photo + .cname { color: var(--blue-dark); }
	.ballot-input:checked ~ .candidate-check { display: flex; }
	.candidate-card:has(.ballot-input:checked) {
		border-color: var(--blue);
		background: #EEF3FE;
	}

	.ballot-input:focus-visible ~ .candidate-check,
	.candidate-card:has(.ballot-input:focus-visible) {
		outline: 3px solid rgba(36, 85, 217, 0.3);
		outline-offset: 2px;
	}

	.ballot-actions { margin: 24px 0 10px; }
	.ballot-actions .btn { min-width: 140px; margin: 0 6px 10px; }
	.ballot-actions .btn-primary { background: var(--blue); border-color: var(--blue); }
	.ballot-actions .btn-primary:hover { background: var(--blue-dark); border-color: var(--blue-dark); }

	.already-voted { padding: 40px 16px; }
	.already-voted .check-badge {
		width: 60px; height: 60px; border-radius: 50%;
		background: var(--blue); color: #fff;
		display: inline-flex; align-items: center; justify-content: center;
		font-size: 22px; margin-bottom: 16px;
	}

	.votelist {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 12px 14px;
		border: 1px solid var(--line);
		border-radius: 8px;
		margin-bottom: 8px;
		background: var(--paper);
	}

	.votelist-position {
		font-size: 12px;
		font-weight: 600;
		text-transform: uppercase;
		letter-spacing: 0.03em;
		color: var(--slate);
	}

	.votelist-candidate {
		font-size: 14px;
		font-weight: 600;
		color: var(--ink);
	}

	@media (max-width: 480px) {
		.candidate-grid { grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); }
		.candidate-photo { width: 60px; height: 60px; }
	}
</style>

<?php include 'includes/scripts.php'; ?>
<script>
$(function(){

	$(document).on('click', '.reset', function(e){
	    e.preventDefault();
	    var desc = $(this).data('desc');
	    $('.'+desc).prop('checked', false);
	});

	$(document).on('click', '.platform', function(e){
		e.preventDefault();
		$('#platform').modal('show');
		var platform = $(this).data('platform');
		var fullname = $(this).data('fullname');
		$('.candidate').html(fullname);
		$('#plat_view').html(platform);
	});

	$('#preview').click(function(e){
		e.preventDefault();
		var form = $('#ballotForm').serialize();
		if(form == ''){
			$('.message').html('You must vote atleast one candidate');
			$('#alert').show();
		}
		else{
			$.ajax({
				type: 'POST',
				url: 'preview.php',
				data: form,
				dataType: 'json',
				success: function(response){
					if(response.error){
						var errmsg = '';
						var messages = response.message;
						for (i in messages) {
							errmsg += messages[i];
						}
						$('.message').html(errmsg);
						$('#alert').show();
					}
					else{
						$('#preview_modal').modal('show');
						$('#preview_body').html(response.list);
					}
				}
			});
		}

	});

	// Lock the submit button the instant the form submits — closes the
	// double-click / slow-connection race that let two ballots go out.
	var ballotSubmitted = false;
	$('#ballotForm').on('submit', function(){
		if(ballotSubmitted){
			return false;
		}
		ballotSubmitted = true;
		$('#vote').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Submitting...');
	});

});
</script>
</body>
</html>