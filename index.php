<?php
	session_start();
	if(isset($_SESSION['admin'])){
		header('location: admin/home.php');
		exit;
	}

	if(isset($_SESSION['voter'])){
		header('location: home.php');
		exit;
	}

	// Change this once per election — it drives the title everywhere on this page
	$electionTitle = "St. Jude S.S Elections 2026/27";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Sign In | <?php echo htmlspecialchars($electionTitle); ?></title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@500&display=swap" rel="stylesheet">
	<style>
		:root {
			--ink: #14264A;
			--paper: #F4F6FA;
			--blue: #2455D9;
			--blue-dark: #1B3EAD;
			--slate: #5B6478;
			--line: #DBE2EE;
			--white: #FFFFFF;
		}

		* { box-sizing: border-box; }

		body {
			margin: 0;
			min-height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
			padding: 20px;
			font-family: 'Inter', sans-serif;
			color: var(--ink);
			background-color: var(--paper);
			background-image: radial-gradient(circle, var(--line) 1px, transparent 1px);
			background-size: 22px 22px;
		}

		.login-page {
			width: 100%;
			display: flex;
			justify-content: center;
		}

		.login-box {
			position: relative;
			width: 100%;
			max-width: 380px;
		}

		.masthead {
			text-align: center;
			margin-bottom: 26px;
		}

		.masthead .mark {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			width: 64px;
			height: 64px;
			margin-bottom: 14px;
			box-shadow: 0 4px 14px rgba(20, 38, 74, 0.08);
		}

		.masthead .mark img {
			width: 100%;
			height: 100%;
			object-fit: cover;
		}

		.masthead h1 {
			font-family: 'Fraunces', serif;
			font-weight: 600;
			font-size: clamp(20px, 5vw, 26px);
			margin: 0 0 4px;
			letter-spacing: -0.01em;
			line-height: 1.2;
			padding: 0 8px;
		}

		.masthead p {
			margin: 0;
			font-size: 13px;
			color: var(--slate);
		}

		.stub {
			background: var(--white);
			border: 1px solid var(--line);
			border-radius: 14px;
			padding: 32px 28px 28px;
			position: relative;
			box-shadow: 0 1px 2px rgba(20, 38, 74, 0.04), 0 12px 32px rgba(20, 38, 74, 0.06);
		}

		/* perforated ballot-stub edge */
		.stub::before {
			content: "";
			position: absolute;
			top: -1px;
			left: 24px;
			right: 24px;
			height: 1px;
			background-image: radial-gradient(circle, var(--paper) 2.5px, transparent 2.5px);
			background-size: 14px 1px;
			background-repeat: repeat-x;
		}

		.seal {
			position: absolute;
			top: -18px;
			right: 26px;
			width: 44px;
			height: 44px;
			border-radius: 50%;
			background: var(--blue);
			border: 3px solid var(--paper);
			display: flex;
			align-items: center;
			justify-content: center;
			transform: rotate(8deg);
			box-shadow: 0 4px 10px rgba(36, 85, 217, 0.35);
		}

		.seal svg {
			width: 18px;
			height: 18px;
			stroke: var(--white);
			fill: none;
			stroke-width: 2.5;
			stroke-linecap: round;
			stroke-linejoin: round;
		}

		.stub h2 {
			font-family: 'Fraunces', serif;
			font-size: 15px;
			font-weight: 600;
			margin: 4px 0 22px;
			color: var(--slate);
		}

		.field {
			margin-bottom: 16px;
		}

		.field label {
			display: block;
			font-size: 12px;
			font-weight: 600;
			text-transform: uppercase;
			letter-spacing: 0.04em;
			color: var(--slate);
			margin-bottom: 6px;
		}

		.field input {
			width: 100%;
			padding: 12px 14px;
			border: 1px solid var(--line);
			border-radius: 8px;
			font-family: 'IBM Plex Mono', monospace;
			font-size: 14px;
			color: var(--ink);
			background: var(--paper);
			transition: border-color 0.15s ease, box-shadow 0.15s ease;
		}

		.field input::placeholder {
			font-family: 'Inter', sans-serif;
			color: #9BA1AF;
		}

		.field input:focus {
			outline: none;
			border-color: var(--blue);
			box-shadow: 0 0 0 3px rgba(36, 85, 217, 0.12);
			background: var(--white);
		}

		.submit {
			width: 100%;
			padding: 13px 16px;
			margin-top: 8px;
			border: none;
			border-radius: 8px;
			background: var(--blue);
			color: var(--white);
			font-family: 'Inter', sans-serif;
			font-size: 14px;
			font-weight: 600;
			letter-spacing: 0.01em;
			cursor: pointer;
			transition: background 0.15s ease, transform 0.1s ease;
		}

		.submit:hover { background: var(--blue-dark); }
		.submit:active { transform: translateY(1px); }
		.submit:focus-visible { outline: 3px solid rgba(36, 85, 217, 0.3); outline-offset: 2px; }

		.error {
			margin-top: 18px;
			padding: 12px 14px;
			background: #EAF0FE;
			border: 1px solid #C4D4F9;
			border-radius: 8px;
			color: var(--blue-dark);
			font-size: 13px;
			text-align: center;
		}

		.foot-note {
			text-align: center;
			margin-top: 20px;
			font-size: 12px;
			color: var(--slate);
		}

		.credit {
			text-align: center;
			margin-top: 10px;
			font-size: 11px;
			color: #9BA1AF;
			letter-spacing: 0.02em;
		}

		.credit span {
			color: var(--slate);
			font-weight: 600;
		}

		/* Responsive tightening for small phones */
		@media (max-width: 380px) {
			.stub { padding: 26px 20px 22px; }
			.masthead .mark { width: 56px; height: 56px; }
			.seal { width: 38px; height: 38px; right: 20px; }
			.seal svg { width: 16px; height: 16px; }
		}

		@media (prefers-reduced-motion: reduce) {
			.submit { transition: none; }
		}
	</style>
</head>
<body class="login-page">

	<div class="login-box">
		<div class="masthead">
			<div class="mark">
				<img src="images/logo.png" alt="School logo">
			</div>
			<h1><?php echo htmlspecialchars($electionTitle); ?></h1>
			<p>Every ballot counted, every voter verified</p>
		</div>

		<div class="stub">
			<div class="seal" aria-hidden="true">
				<svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
			</div>

			<h2>Sign in to start your session</h2>

			<form action="login.php" method="POST">
				<div class="field">
					<label for="voter">Voter's ID</label>
					<input type="text" id="voter" name="voter" placeholder="e.g. VTR-04821" required>
				</div>
				<div class="field">
					<label for="password">Password</label>
					<input type="password" id="password" name="password" placeholder="••••••••" required>
				</div>
				<button type="submit" class="submit" name="login">Sign In</button>
			</form>

			<?php
				if(isset($_SESSION['error'])){
					echo "<div class='error'>".$_SESSION['error']."</div>";
					unset($_SESSION['error']);
				}
			?>
		</div>

		<p class="foot-note">Having trouble accessing your account? Contact your election officer.</p>
		<p class="credit">Powered by <span>Dementa Technologies</span></p>
	</div>

</body>
</html>