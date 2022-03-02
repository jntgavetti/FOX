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
	<title>FOX - Listagem de grupos</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="_img/fox_icon.png" />
	<link rel="stylesheet" href="_css/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" href="_css/primary.css" />
	<script src="_js/jquery/jquery.js"></script>
</head>

<body id="body-dashboard" class="body-groups">
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
				<a href="#">Navegação |</a>

				<a href="#" class="active">Grupos</a>
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
						<h3 class="text-center mb-4">Grupos
							<a href="#" class="btn_add_group" data-toggle="tooltip" data-placement="right" title="Adicionar grupo">
								<i class="fas fa-plus-circle"></i>
							</a>
						</h3>

						<div class="div_grupos">
							<div class="grupos_perso">
								<p class="p-3 text-center" id="g_default">Não existem grupos cadastrados. <a href="#">Cadastrar</a></p>
							</div>
						</div>
					</div>
				</div>

				<div class="col-4 col-info">
					<div class="info-box">
						<h3 class="text-center mb-4">Politicas</h3>
						<ul class="nav nav-tabs ul_alterna_div">
							<li class="nav-item lista_sites">
								<span class="aviso_wrong"><i class="fa fa-exclamation-circle"></i></span>
								<a id="lista_sites" class="nav-link active" href="#">Sites</a>
							</li>
							<li class="nav-item lista_palavras">
								<span class="aviso_wrong"><i class="fa fa-exclamation-circle"></i></span>
								<a id="lista_palavras" class="nav-link" href="#">Palavras</a>
							</li>
							<li class="nav-item lista_ips">
								<span class="aviso_wrong"><i class="fa fa-exclamation-circle"></i></span>
								<a id="lista_ips" class="nav-link" href="#">IPs</a>
							</li>
							<button type="button" class="btn_delete delete btn btn-outline-danger btn-sm">
								<i class="far fa-trash-alt "></i>
								Excluir
							</button>
						</ul>


						<div id="div_lista_sites" class="div_alterna div_lista_sites active">
							<div class="div_controle_topicos">
								<div class='div_topico div_topico_padrao'>
									<div class="draggable">
										<div class="text">
											<h5>Solte aqui</h5>
											<i class="fa fa-arrow-alt-circle-down fa-2x"></i>
										</div>
									</div>
									<div class='div_info_topico'>
										<input type='text' value='Novo topico' class='form-control titulo_topico'>
										<button class='btn btn-sm btn_add_tp'><i class='fa fa-plus-circle'></i></button>
										<button class='btn btn-sm btn_del'><i class='fa fa-times-circle'></i></button>
									</div>
									<div class='div_info_linha'>
										<span class="btn btn-sm btn_move"><i class="fa fa-arrows-alt"></i></span>
										<input type='text' value='' class='form-control linha_topico'>
										<button class='btn btn-sm btn_add'><i class='fa fa-plus-circle'></i></button>
										<button class='btn btn-sm btn_del'><i class='fa fa-times-circle'></i></button>
									</div>
								</div>

							</div>
						</div>

						<div id="div_lista_palavras" class="div_alterna div_lista_palavras">
							<div class="div_controle_topicos">

							</div>
						</div>

						<div id="div_lista_ips" class="div_alterna div_lista_ips">
							<div class="div_controle_topicos">

							</div>
						</div>


						<button class="btn_save btn btn-sm mt-2"><i class="far fa-save mr-2"></i> Salvar</button>
						<button type="button" class="btn btn-sm mt-2 btn_apply"><i class="fas fa-upload mr-2"></i>Aplicar</button>
						<button class="btn_add_topico btn btn-sm btn-outline-success mt-2"><i class="fa fa-plus mr-1"></i> Adicionar tópico</button>
					</div>
				</div>
		</article>


	</section>



	<script src="_js/bootstrap/popper.js"></script>
	<script src="_js/bootstrap/bootstrap.min.js"></script>
	<script src="_js/fawesome/all.js"></script>
	<script src="_js/primary.js"></script>
	<script src="_js/grupos.js"></script>
</body>

</html>