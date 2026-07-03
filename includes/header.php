<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle).' | ' : ''; ?>Voting System</title>
	<!-- Tell the browser to be responsive to screen width -->
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link rel="icon" href="images/logo.png" type="image/png">

	<!-- Bootstrap 3.3.7 -->
	<link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
	<!-- iCheck for checkboxes and radio inputs -->
	<link rel="stylesheet" href="plugins/iCheck/all.css">
	<!-- DataTables -->
	<link rel="stylesheet" href="bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="dist/css/AdminLTE.min.css">
	<!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
	<link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

	<!-- Google Fonts: display / body / data -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@500&display=swap" rel="stylesheet">

	<style>
		/* ---- Design tokens, shared across every page ---- */
		:root {
			--ink: #14264A;
			--paper: #F4F6FA;
			--blue: #2455D9;
			--blue-dark: #1B3EAD;
			--slate: #5B6478;
			--line: #DBE2EE;
			--white: #FFFFFF;
		}

		body {
			font-family: 'Inter', sans-serif;
			color: var(--ink);
			background-color: var(--paper);
		}

		h1, h2, h3, h4, .modal-title, .page-header {
			font-family: 'Fraunces', serif;
		}

		a {
			color: var(--blue);
		}
		a:hover, a:focus {
			color: var(--blue-dark);
		}

		.btn-primary {
			background-color: var(--blue);
			border-color: var(--blue);
		}
		.btn-primary:hover, .btn-primary:focus, .btn-primary:active {
			background-color: var(--blue-dark);
			border-color: var(--blue-dark);
		}

		/* ---- Page-level utility classes still in use ---- */
		.mt20 {
			margin-top: 20px;
		}

		#candidate_list {
			margin-top: 20px;
		}

		#candidate_list ul {
			list-style-type: none;
		}

		#candidate_list ul li {
			margin: 0 30px 30px 0;
			vertical-align: top;
		}
	</style>
</head>