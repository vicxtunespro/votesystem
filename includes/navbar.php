<style>
	.main-header .navbar {
		background: var(--blue, #2455D9);
		min-height: 56px;
	}

	.main-header .navbar-brand {
		display: flex;
		align-items: center;
		gap: 8px;
		color: #fff !important;
		font-weight: 600;
		height: 56px;
		padding: 0 15px;
	}

	.main-header .navbar-brand img {
		width: 28px;
		height: 28px;
		border-radius: 50%;
		object-fit: cover;
		background: #fff;
	}

	.main-header .navbar-nav > li > a {
		color: rgba(255,255,255,0.85) !important;
		font-size: 13px;
		font-weight: 600;
		letter-spacing: 0.02em;
		padding-top: 18px;
	}

	.main-header .navbar-nav > li > a:hover,
	.main-header .navbar-nav > li.active > a {
		color: #fff !important;
		background: rgba(255,255,255,0.12) !important;
	}

	.main-header .navbar-toggle {
		border-color: rgba(255,255,255,0.3);
		margin-top: 12px;
	}
	.main-header .navbar-toggle .fa {
		color: #fff;
	}
	.main-header .navbar-toggle:hover {
		background: rgba(255,255,255,0.12);
	}

	.main-header .navbar-custom-menu .user-menu {
		display: flex;
		align-items: center;
	}

	.main-header .user-display {
		display: flex;
		align-items: center;
		gap: 8px;
		padding: 8px 12px;
		color: #fff;
	}

	.main-header .user-image {
		width: 30px;
		height: 30px;
		border-radius: 50%;
		object-fit: cover;
		border: 1px solid rgba(255,255,255,0.4);
	}

	.main-header .navbar-custom-menu a[href="logout.php"] {
		color: rgba(255,255,255,0.85) !important;
		font-size: 13px;
		font-weight: 600;
	}
	.main-header .navbar-custom-menu a[href="logout.php"]:hover {
		color: #fff !important;
		background: rgba(255,255,255,0.12) !important;
	}

	@media (max-width: 767px) {
		.main-header .navbar-collapse {
			background: var(--blue, #2455D9);
		}
	}
</style>

<header class="main-header">
	<nav class="navbar navbar-static-top">
		<div class="container">
			<div class="navbar-header">
				<a href="<?php echo isset($_SESSION['voter']) ? 'home.php' : 'index.php'; ?>" class="navbar-brand">
					<img src="images/logo.png" alt="Logo">
					<span>Voting Portal</span>
				</a>
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
					<i class="fa fa-bars"></i>
				</button>
			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse pull-left" id="navbar-collapse">
				<ul class="nav navbar-nav">
					<?php
						if(isset($_SESSION['voter'])){
							$currentPage = basename($_SERVER['PHP_SELF']);
							$homeActive = ($currentPage === 'home.php') ? 'active' : '';
							echo "
								<li class='{$homeActive}'><a href='home.php'>HOME</a></li>
								<li><a href='#' id='viewVotesLink'>VIEW MY VOTES</a></li>
							";
						}
					?>
				</ul>
			</div>
			<!-- /.navbar-collapse -->
			<!-- Navbar Right Menu -->
			<div class="navbar-custom-menu">
				<ul class="nav navbar-nav">
					<li class="user user-menu">
						<span class="user-display">
							<img src="<?php echo (!empty($voter['photo'])) ? 'images/'.htmlspecialchars($voter['photo']) : 'images/profile.jpg'; ?>" class="user-image" alt="User photo">
							<span class="hidden-xs"><?php echo htmlspecialchars($voter['firstname'].' '.$voter['lastname']); ?></span>
						</span>
					</li>
					<li><a href="logout.php"><i class="fa fa-sign-out"></i> LOGOUT</a></li>
				</ul>
			</div>
			<!-- /.navbar-custom-menu -->
		</div>
		<!-- /.container-fluid -->
	</nav>
</header>

<script>
	document.addEventListener('DOMContentLoaded', function(){
		var viewLink = document.getElementById('viewVotesLink');
		if(!viewLink) return;

		viewLink.addEventListener('click', function(e){
			e.preventDefault();
			if(window.jQuery && $('#view').length){
				// Already on a page with the modal available (home.php)
				$('#view').modal('show');
			}
			else{
				// Navigate to home.php, then auto-open the modal once it loads
				window.location.href = 'home.php?view=1';
			}
		});

		// Auto-open the modal if we arrived here via ?view=1
		if(window.location.search.indexOf('view=1') !== -1 && window.jQuery && $('#view').length){
			$('#view').modal('show');
		}
	});
</script>