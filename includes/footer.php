<style>
	.main-footer {
		background: var(--white, #FFFFFF);
		border-top: 1px solid var(--line, #DBE2EE);
		padding: 14px 0;
		font-size: 13px;
		color: var(--slate, #5B6478);
	}

	.main-footer a {
		color: var(--blue, #2455D9);
		font-weight: 600;
	}
	.main-footer a:hover {
		color: var(--blue-dark, #1B3EAD);
	}

	.main-footer .footer-credit {
		font-size: 11px;
		color: #9BA1AF;
	}

	.main-footer .footer-credit span {
		color: var(--slate, #5B6478);
		font-weight: 600;
	}

	@media (max-width: 480px) {
		.main-footer .container {
			text-align: center;
		}
		.main-footer .pull-right {
			float: none !important;
			margin-bottom: 4px;
		}
	}
</style>

<footer class="main-footer">
	<div class="container">
		<div class="pull-right hidden-xs footer-credit">
			Powered by <span>Dementa Technologies</span>
		</div>
		<strong>Copyright &copy; <?php echo date('Y'); ?> <a href="https://www.projectarena.vercel.app">ST. JUDE S.S</a></strong>
	</div>
	<!-- /.container -->
</footer>