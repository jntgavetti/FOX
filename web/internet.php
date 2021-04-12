<?php
ini_set('display_errors', 1);
error_reporting('E_ALL');

if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_usuario'])) {
	session_destroy();
	header("Location: index.php");
	exit;
} else {
	$nivel_login = $_SESSION['nivel'];
	$nome_usuario = $_SESSION['nome'];
}
?>
<!DOCTYPE HTML>
<html>

<head>
	<title>FOX - Internet</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="_img/fox_icon.png" />
	<link rel="stylesheet" href="_css/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" href="_css/primary.css" />
	<script src="_js/jquery/jquery.js"></script>
</head>

<body id="body-dashboard" class="body-internet">
	<div id="divMenuEscuro"></div>
	<div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
		<div class="toast-header">

			<strong class="mr-auto">Status de execução</strong>

			<button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
				<span class="text-light" aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="toast-body">

		</div>
	</div>


	<header>
		<?php
		if ($nivel_login == 'admin') {
			require_once('menu_admin.php');
		} else {
			require_once('menu_cliente.php');
		}
		?>
	</header>


	<section class="section-main">

		<div class="modal fade" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title"></h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="div_loading">
							<img src="_img/loading_main.svg" alt="">
							<span class="mt-3">Aguarde ...</span>
						</div>

						<div class="div_status">
							<p class="status"></p><br>

							<p class="detalhe_desc">Detalhes do processamento: </p>

							<div class="div_detalhe">
								<p class="p_detalhe"></p>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-sm btn_apply btn_apply_modal">Aplicar</button>
						<button type="button" class="btn btn-sm btn-outline-danger btn_close" data-dismiss="modal">Fechar</button>
					</div>
				</div>
			</div>
		</div>

		<nav class="menuH">
			<div class="sombrinha-inicio"></div>
			<button class="btn btn-show-menu navbar-toggler" type="button">
				<i class="fas fa-bars"></i>
			</button>
			<a class="navbar-brand" href="#">
				<img src="_img/icon.png" width="30" height="30" class="d-inline-block align-bottom" alt="">
			</a>
			<div class="divBread">
				<span class="nivelAcesso <?php echo $nivel_login; ?>">
				</span>
				<a href="#" class="active">Internet</a>
			</div>
			<div class="conf">
				<span><?php echo $nome_usuario; ?></span>
				<a href='#' onclick='window.location.assign("_controler/logout.php")'>
					<i class='fas fa-power-off'></i>
				</a>
			</div>
			<div class="sombrinha-fim"></div>
		</nav>


		<article class="container-fluid">

		</article>


	</section>



	<script src="_js/bootstrap/popper.js"></script>
	<script src="_js/bootstrap/bootstrap.min.js"></script>
	<script src="_js/fawesome/all.js"></script>
	<script src="_js/primary.js"></script>
	<script src="_js/grupos.js"></script>
</body>

</html>