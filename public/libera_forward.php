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
	<title>FOX - LAN | Encaminhamento</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="_img/fox_icon.png" />
	<link rel="stylesheet" href="_css/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" href="_css/primary.css" />
	<script src="_js/jquery/jquery.js"></script>
</head>

<body id="body-dashboard" class="body-redirec body-lib-fwd">


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
				<a href="#">LAN |</a>
				<a href="#" class="active">Encaminhamento</a>
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

			<h5>LAN - Encaminhamento</h5>
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
										<div id="divsForm" class="row">

											<div class="col">
												<div id="divForm1">
													<label for="popupInp1">Descrição</label>
													<input type="text" id="popupInp1" size="10" name="descricao" class="inp" value="Teste">
												</div>

												<div id="divForm2">
													<label for="popupSelStatus">Fluxo</label>
													<select name="fluxo" id="popupSelStatus" class="inp">
														<option value="bidirecional">bidirecional</option>
														<option value="unidirecional">unidirecional</option>
													</select>
												</div>

												<div id="divForm3">
													<label for="popupSelProto">Protocolo(s)</label>
													<select name="protocolo" id="popupSelProto" class="inp">
														<option value="tcp">TCP</option>
														<option value="udp">UDP</option>
														<option value="icmp">ICMP</option>
														<option value="tcp/udp">TCP/UDP</option>
														<option value="tcp/icmp">TCP/ICMP</option>
														<option value="udp/icmp">UDP/ICMP</option>
														<option value="todos">Todos</option>
													</select>
												</div>
											</div>

											<div class="col">

												<div id="divForm4">
													<label for="popupInp4">Placa de origem</label>
													<input id="popupInp4" size="10" name="int-origem" class="inp" list="data-4" required="required"  autocomplete="off">
													<datalist id="data-4">
														<option>Qualquer</option>
													</datalist>
												</div>
												<div id="divForm5">
													<label for="popupInp5">IP de origem</label>
													<input id="popupInp5" size="15" name="ip-origem" class="inp" list="data-5" required="required" autocomplete="off">
													<datalist id="data-5">
														<option>Qualquer</option>
													</datalist>
												</div>

												<div id="divForm6">
													<label for="popupInp6">Placa de destino</label>
													<input type="text" size="10" id="popupInp6" name="int-destino" class="inp" list="data-6" required="required"  autocomplete="off">
													<datalist id="data-6">
														<option>Qualquer</option>
													</datalist>
												</div>

												<div id="divForm7">
													<label for="popupInp7">IP de destino</label>
													<input type="text" size="15" id="popupInp7" name="ip-destino" class="inp" list="data-7" required="required"  autocomplete="off">
													<datalist id="data-7">
														
													</datalist>
												</div>

												<div id="divForm8">
													<label for="popupInp8">Porta</label>
													<input type="text" id="popupInp8" size="5" name="porta" class="inp" value="1:65535" required="required">
												</div>
											</div>
										</div>
									</form>
									<p id="aviso_status"></p>
									<div class="orienta">
										<p class="aviso">Preencha os campos obrigatórios.</p>
										<h6 class="mb-5">Instruções</h6>
										<p class="text-dark"><strong>Fluxo unidirecional:</strong> permite comunicação em um único sentido.</p>
										<p class="text-dark"><strong>Fluxo bidirecional:</strong> permite comunicação em ambos os sentidos.</p>
										<p class="text-dark"><strong>Porta:</strong> Cada porta separada por virgula, intervalos separados por <strong>:</strong></p>
										<p class="text-dark"><strong>EX:</strong> 80, 443 <-> 40000:50000</p>
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
							<th>Fluxo</th>
							<th>Protocolo</th>
							<th>Placa de origem</th>
							<th>IP de origem</th>
							<th>Placa de destino</th>
							<th>IP de destino</th>
							<th>Porta</th>
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
	<script src="_js/libera_fwd.js"></script>
</body>

</html>