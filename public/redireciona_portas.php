<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_usuario'])) {
	session_destroy();
	header("Location: index.php");
	exit;
} else {
	
	$nivel_login = $_SESSION['nivel'];
	$nome_usuario = $_SESSION['nome'];

	if ($nivel_login == 'admin') {
		require_once('menu_admin.php');
	} else {
		require_once('menu_cliente.php');
	}
}

?>
<!DOCTYPE HTML>
<html>

<head>
	<title>FOX - Painel de controle</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="_img/fox_icon.png" />
	<link rel="stylesheet" href="_css/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" href="_css/primary.css" />
	<script src="_js/jquery/jquery.js"></script>
</head>

<body id="body-dashboard" class="body-redirec">


	<header>

	</header>

	<div class="div_loading_main">
		<img src="_img/loading_main.svg" alt="">
	</div>

	<section class="section-main">

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
				<a href="#">Firewall |</a>

				<a href="#" class="active">Redirecionamento de portas</a>
			</div>
			<div class="conf">
				<span><?php echo $nome_usuario; ?></span>
				<i class="fas fa-cog"></i>
				<a href='#' onclick='window.location.assign("_controler/logout.php")'>
					<i class='fas fa-power-off'></i>
				</a>
			</div>
		</nav>

		<article class="redirec">

			<h5>Redirecionamentos Existentes</h5>
			<hr>
			<div class="group-btn-crud">
				<a href="#" class="add btn-style" data-target=".modal" data-toggle="modal">
					<i class="fas fa-plus-circle"></i>
				</a>
				<a href="#" class="edit disabled btn-style" data-target=".modal" data-container="body" data-toggle="popover" data-placement="top" data-content="">
					<i class="fas fa-edit"></i>
				</a>
				<a href="#" class="delete disabled btn-style" data-container="body" data-toggle="popover" data-placement="top" data-content="">
					<i class="fas fa-trash-alt"></i>
				</a>
			</div>

			<div class="table-div">
				<div class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
					<div class="modal-dialog modal-xl">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="exampleModalLabel">Adicionar redirecionamento</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								<div class="form-popup">
									<form id="formPopUp" method="post">
										<div id="divsForm">

											<div id="divForm1">
												<label for="popupInp1">Descrição</label>
												<input type="text" id="popupInp1" size="10" name="descricao" class="inp_redirec" value="Redirec">
											</div>

											<div id="divForm7">
												<label for="popupSelStatus">Status</label>
												<select name="status" id="popupSelStatus" class="inp_redirec">
													<option value="ativo">ativo</option>
													<option value="inativo">inativo</option>
												</select>
											</div>

											<div id="divForm6">
												<label for="popupSelProto">Protocolo</label>
												<select name="protocolo" id="popupSelProto" class="inp_redirec">
													<option value="tcp/udp">tcp/udp</option>
													<option value="tcp">tcp</option>
													<option value="udp">udp</option>
												</select>
											</div>
											<div id="divForm2">
												<label for="popupInp2">Origem</label>
												<input type="text" id="popupInp2" size="10" name="origem" class="inp_redirec" value="0.0.0.0/0">
											</div>

											<div id="divForm3">
												<label for="popupInp3">Porta externa</label>
												<input type="text" id="popupInp3" size="10" name="portaOrigem" required="required" class="inp_redirec">
											</div>

											<div id="divForm4">
												<label for="popupInp4">Destino</label>
												<input type="text" id="popupInp4" size="10" name="destino" required="required" class="inp_redirec">
											</div>

											<div id="divForm5">
												<label for="popupInp5">Porta interna</label>
												<input type="text" id="popupInp5" size="10" name="portaDestino" required="required" class="inp_redirec">
											</div>
										</div>
									</form>
									<p id="aviso_status"></p>
									<div class="orienta">
										<p class="aviso">Preencha os campos obrigatórios.</p>
										<h6>Instruções</h6>
										<p class="ml-5 mt-5 text-info">Caso os campos opcionais fiquem em branco os valores padrões irão ser considerados</p>
										<p class="ml-5 text-info">Caso o campo origem esteja sem <strong class="text-dark">Máscara(CIDR)</strong> irá ser considerado o <strong class="text-dark">CIDR /32</strong></p>
									</div>
								</div>
							</div>
							<div class="modal-footer">

								<a href="#" id="save">
									<i class="fas fa-save mr-1"></i>
									Salvar alterações
								</a>
								<a href="#" id="cancel" data-dismiss="modal">
									<i class="fas fa-times mr-1"></i>
									Cancelar
								</a>
							</div>
						</div>
					</div>
				</div>


				<table id="tableRedirec" class="table table-bordered table-hover">
					<thead>
						<div id="tess">
							<th>
								<input type="checkbox" id="checkMain">
							</th>
							<th>Descrição</th>
							<th>Status</th>
							<th>Protocolo</th>
							<th>Origem</th>
							<th>Porta externa</th>
							<th>Destino</th>
							<th>Porta interna</th>
					</thead>
			</div>
			<tbody>
				
			</tbody>
			</table>
			</div>
		</article>
		<aside class="aside-buttons">
			<div>
				<a href="#" class="add btn-style" data-target=".modal" data-toggle="modal">
					<i class="fas fa-plus-circle"></i>
				</a>
				<a href="#" class="edit disabled btn-style" data-target=".modal" data-container="body" data-toggle="popover" data-placement="top" data-content="">
					<i class="fas fa-edit"></i>
				</a>
				<a href="#" class="delete disabled btn-style">
					<i class="fas fa-trash-alt"></i>
				</a>
			</div>
		</aside>


	</section>



	<script src="_js/bootstrap/popper.js"></script>
	<script src="_js/bootstrap/bootstrap.min.js"></script>
	<script src="_js/fawesome/all.js"></script>
	<script src="_js/primary.js"></script>
	<script src="_js/redirec.js"></script>
</body>

</html>