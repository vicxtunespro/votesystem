<?php
	session_start();
	if(isset($_SESSION['admin'])){
		header('location:home.php');
		exit;
	}

	$pageTitle = 'Admin Sign In';
?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition login-page">
<div class="login-page-wrap">

	<div class="login-box">
		<div class="masthead">
			<div class="mark">
				<img src="../images/logo.png" alt="Logo">
			</div>
			<h1>Admin Portal</h1>
			<p>Sign in to manage this election</p>
		</div>

		<div class="stub">
			<div class="seal" aria-hidden="true">
				<svg viewBox="0 0 24 24"><path d="M12 2 4 5v6c0 5 3.4 9.3 8 10 4.6-.7 8-5 8-10V5l-8-3z"></path></svg>
			</div>

			<h2>Sign in to start your session</h2>

			<!-- admin form -->
			<form action="login.php" method="POST">
				<div class="field">
					<label for="username">Username</label>
					<input type="text" id="username" name="username" placeholder="Enter your username" required>
				</div>
				<div class="field">
					<label for="password">Password</label>
					<input type="password" id="password" name="password" placeholder="••••••••" required>
				</div>
				<button type="submit" class="submit" name="login">Sign In</button>
			</form>

			<?php
				if(isset($_SESSION['error'])){
					echo "<div class='error'>".htmlspecialchars($_SESSION['error'])."</div>";
					unset($_SESSION['error']);
				}
			?>
		</div>

		<p class="foot-note"><a href="../index.php">Login as Voter instead</a></p>
		<p class="credit">Powered by <span>Dementa Technologies</span></p>
	</div>

</div>

<style>
	.login-page-wrap {
		min-height: 100vh;
		display: flex;
		align-items: center;
		justify-content: center;
		padding: 20px;
		background-image: radial-gradient(circle, var(--line) 1px, transparent 1px);
		background-size: 22px 22px;
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
		border-radius: 50%;
		background: var(--white);
		border: 1px solid var(--line);
		margin-bottom: 14px;
		overflow: hidden;
		box-shadow: 0 4px 14px rgba(20, 38, 74, 0.08);
	}

	.masthead .mark img {
		width: 100%;
		height: 100%;
		object-fit: cover;
	}

	.masthead h1 {
		font-weight: 600;
		font-size: clamp(20px, 5vw, 26px);
		margin: 0 0 4px;
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
		fill: var(--white);
		stroke: none;
	}

	.stub h2 {
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
		font-size: 13px;
	}

	.credit {
		text-align: center;
		margin-top: 10px;
		font-size: 11px;
		color: #9BA1AF;
	}

	.credit span {
		color: var(--slate);
		font-weight: 600;
	}

	@media (max-width: 380px) {
		.stub { padding: 26px 20px 22px; }
		.masthead .mark { width: 56px; height: 56px; }
		.seal { width: 38px; height: 38px; right: 20px; }
		.seal svg { width: 16px; height: 16px; }
	}
</style>

<?php include 'includes/scripts.php' ?>
</body>
</html>