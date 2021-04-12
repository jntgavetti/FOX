<?php
ini_set('display_errors', 0);
error_reporting(0);

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
	<title>FOX - Listagem de dispositivos</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="_img/fox_icon.png" />
	<link rel="stylesheet" href="_css/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" href="_css/primary.css" />
	<script src="_js/jquery/jquery.js"></script>
</head>

<body id="body-dashboard" class="body-users">
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

	<div class="div_loading_main">
		<img src="_img/loading_main.svg" alt="">
	</div>


	<section class="section-main">
		<aside class="aside-back">
			<i class="fas fa-step-backward"></i>
			<a href="#">Voltar</a>
		</aside>

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
						<button type="button" class="btn btn-sm btn-outline-primary btn_apply btn_apply_modal">Aplicar</button>
						<button type="button" class="btn btn-sm btn-outline-danger btn_close" data-dismiss="modal">Fechar</button>
					</div>
				</div>
			</div>
		</div>

		<nav class="menuH">
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

				<a href="#" class="active">Dispositivos</a>
			</div>
			<div class="conf">
				<span><?php echo $nome_usuario; ?></span>
				<a href='#' onclick='window.location.assign("_controler/logout.php")'>
					<i class='fas fa-power-off'></i>
				</a>
			</div>
		</nav>


		<article class="container-fluid">

			<div class="row lista_usuarios">
				<div class="col-lg-2 col-md-3 col-sm-6 col_usuarios">

					<div class="div_pesquisa text-center">
						<h3 class="">Selecione um dispositivo</h3>

						<a href="add_device.php" data-toggle="tooltip" data-placement="right" title="Adicionar dispositivo">
							<i class="fas fa-plus-circle"></i>
						</a>

						<input type="text" autocomplete="off" class="form-control form-control-sm" id="search_user" placeholder="Pesquise um usuario">
					</div>

					<div class="div_usuarios">
						<div class="div_usuario_padrao">
							<p class="">Não há usuários cadastrados.</p>
							<a href="add_device.php">Adicionar novos dispositivos.</a>
						</div>
					</div>
				</div>

				<div class="col-lg-5 col-md-7 col-sm-12 col_info_usuarios">


					<div class="div_info_geral">

						<ul class="nav nav-tabs">
							<li id="btn_div_user" class="nav-item">
								<a class="nav-link active" href="#">Informações</a>
							</li>
							
							<button type="button" class="btn_delete delete btn btn-outline-danger btn-sm">
								<i class="far fa-trash-alt "></i>
								Excluir
							</button>


						</ul>

						<div class="div_alterna div_info_usuario" id="div_info_usuario">
							<form class="">

								<div class="form-group row">
									<label for="nome" class="col-lg-3 col-md-6 col-sm-6">Nome:</label>
									<input autocomplete="off" type="text" class="col-lg-3 col-md-3 col-sm-3 form-control form-control-sm" id="nome" name="nome" placeholder="">
								</div>

								<div class="form-group row">
									<label for="setor" class="col-lg-3 col-md-6 col-sm-6">Setor/Localização: </label>
									<input autocomplete="off" type="text" class="col-lg-3 col-md-3 col-sm-3 form-control form-control-sm" id="setor" name="setor" placeholder="">
								</div>

								<div class="div_ipv4 form-group row">
									<label for="ipv4" class="col-lg-3 col-md-6 col-sm-6">Endereço IPv4:</label>
									<input disabled autocomplete="off" type="text" class="col-lg-3 col-md-3 col-sm-3 form-control form-control-sm" id="ipv4" name="ipv4" placeholder="" required=true>
									<a href="#" id="edit" class="edit">
										<i class="far fa-edit mr-2"></i>
										Editar
									</a>
								</div>

								<div class="div_mac form-group row">
									<label for="mac" class="col-lg-3 col-md-6 col-sm-6">Endereço Físico: (MAC) </label>
									<input autocomplete="off" type="text" class="col-lg-3 col-md-3 col-sm-3 form-control form-control-sm" id="mac" name="mac" placeholder="" required=true>
									<img src="_img/loading_main.svg" width="20px" class="ml-2">
									<span class="text-danger mt-2 ml-2"></span>
								</div>
						</div>
					</div>
					</form>



					<div class="btn_crud mt-3">
						<button type="button" class="btn_save btn btn-sm">
							<i class="far fa-save mr-1"></i>
							Salvar
						</button>
						<button type="button" class="btn_apply btn btn-sm">
							<i class="fas fa-upload mr-1"></i>
							Aplicar
						</button>

					</div>

				</div>
			</div>
		</article>

		<footer>
			<button type="button" class="btn_save btn btn-sm">
				<i class="far fa-save mr-2"></i>
				Salvar
			</button>

			<button type="button" class="btn_delete btn btn-sm">
				<i class="far fa-trash-alt "></i>
				Excluir
			</button>
		</footer>

	</section>



	<script src="_js/bootstrap/popper.js"></script>
	<script src="_js/bootstrap/bootstrap.min.js"></script>
	<script src="_js/fawesome/all.js"></script>
	<script src="_js/primary.js"></script>
	<script src="_js/dispositivos.js"></script>
</body>

</html>