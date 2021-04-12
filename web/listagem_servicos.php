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
	<title>FOX - Listagem de servicos</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="_img/fox_icon.png" />
	<link rel="stylesheet" href="_css/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" href="_css/primary.css" />
	<script src="_js/jquery/jquery.js"></script>
</head>

<body id="body-dashboard" class="body-groups body-servicos">
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
				<a href="#">Firewall |</a>

				<a href="#" class="active">Serviços</a>
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

			<div class="row">

				<div class="col-3 col_lista_grupos">
					<div class="">
						<h3 class="text-center mb-4">Serviços
							<a href="#" class="btn_add_group" data-toggle="tooltip" data-placement="right" title="Adicionar grupo">
								<i class="fas fa-plus-circle"></i>
							</a>
						</h3>

						<div class="div_grupos">
							<div class="grupos_perso">
								<div class="text-center">
									<p class="p-3 " id="g_default">Não existem serviços cadastrados. <a href="#">Cadastrar</a></p>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-4 col-info">
					<div class="info-box">
						<h3 class="text-center mb-4">Portas</h3>
						<ul class="nav nav-tabs ul_alterna_div">
							<li class="nav-item lista_sites">
								<span class="aviso_wrong"><i class="fa fa-exclamation-circle"></i></span>
								<a id="lista_sites" class="nav-link active" href="#">Portas do protocolo</a>
							</li>

							<button type="button" class="btn_delete delete btn btn-outline-danger btn-sm">
								<i class="far fa-trash-alt "></i>
								Excluir
							</button>
						</ul>


						<div id="div_lista_sites" class="div_alterna div_lista_sites active">
							<h6 class="pt-4 pl-3">Especifique as portas correspondentes ao protocolo</h6>
							<div class="div_controle_topicos">
								<div class='div_topico div_topico_padrao'>
									<div class='div_info_linha'>
										<span><i class="fas fa-door-open"></i></span>
										<input type='text' class='form-control linha_topico'>
										<button class='btn btn-sm btn_add'><i class='fa fa-plus-circle'></i></button>
										<button class='btn btn-sm btn_del'><i class='fa fa-times-circle'></i></button>
									</div>
								</div>

							</div>
						</div>


						<button class="btn_save btn btn-sm mt-2"><i class="far fa-save mr-2"></i> Salvar</button>
						<button type="button" class="btn btn-sm mt-2 btn_apply"><i class="fas fa-upload mr-2"></i>Aplicar</button>
					</div>
				</div>
		</article>


	</section>



	<script src="_js/bootstrap/popper.js"></script>
	<script src="_js/bootstrap/bootstrap.min.js"></script>
	<script src="_js/fawesome/all.js"></script>
	<script src="_js/primary.js"></script>
	<script src="_js/servicos.js"></script>
</body>

</html>