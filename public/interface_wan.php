<?php
require_once("_model/Wan.php");

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
		$hash_nivel_acesso = "#";
	} else {
		header("Location: index.php");
		exit;
	}
}

$manipulacao = new Manipulacao();
if (isset($_GET['interface'])) {
	$interface = $_GET['interface'];
	$interface_exibe = strtoupper($_GET['interface']);
	$listagem = $manipulacao->listar($interface, "interfaces");
} else {
	header("Location: listagem_wan.php");
	exit;
}

?>
<!DOCTYPE HTML>
<html>

<head>
	<title>FOX - <?php echo $interface_exibe; ?></title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="_img/fox_icon.png" />
	<link rel="stylesheet" href="_css/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" href="_css/primary.css" />
	<script src="_js/jquery/jquery.js"></script>
</head>

<body id="body-dashboard" class="body-wan body-interface">

<header>
	
	</header>

	<section class="section-main">

		<nav class="menuH">
			<button class="btn btn-show-menu navbar-toggler" type="button">
				<i class="fas fa-bars"></i>
			</button>
			<a class="navbar-brand" href="#">
				<img src="http://h2info.com.br/img/icon.png" width="30" height="30" class="mr-2 d-inline-block align-bottom" alt="">
				H2 Informatica
			</a>
			<div class="divBread">
				<span class="nivelAcesso">
					<?php echo $hash_nivel_acesso;?>
				</span>
				<a href="#">WAN</a>
				| <a href="#" class="active"><?php echo $interface_exibe; ?></a>
			</div>
			<div class="conf">
			<span><?php echo $nome_usuario; ?></span>
				<i class="fas fa-cog"></i>
				<a href='#' onclick='window.location.assign("_controler/logout.php")'>
					<i class='fas fa-power-off'></i>
				</a>
			</div>
		</nav>


		<aside class="aside-back">
			<i class="fas fa-chevron-circle-left"></i>
			<a href="#">Voltar</a>
		</aside>
		<article id="lan" class="container">

			<?php
			if (!empty($listagem)) {
				foreach ($listagem as $coluna) :
			?>
					<div class="interface">
						<div class="legend">
							<i class="fas fa-network-wired"></i>
							<h3 class="d-inline"><?php echo $interface_exibe; ?></h3>
						</div>
						<button id="delete" class="btn btn-outline-danger">Apagar</button>
						<form id="formInterfaceWan" class="div-conf div-conf-enabled">
							<div class="div-info-valor div_eth" id="divEth">
								<label for="eth">Interface: </label>
								<?php echo '<input name="interface" class="eth" type="text" disabled=true value="' . $interface . '">'; ?>
							</div>
							
							<div class="div-info-valor div_class_placa selectsPlaca" id="divClassPlaca">
								<label for="">Classificação: </label>
								<select name="class_placa" id="selectClassPlaca">
									<?php
									$classificacao = $coluna['classificacao'];
									if ($classificacao == 'interna') {

										echo '
										
										<option value="interna">Interna</option>
										<option value="externa">Externa</option>
									';
									} else {
										echo '
										
										<option value="externa">Externa</option>
										<option value="interna">Interna</option>
										
										';
									}

									?>
								</select>
							</div>
							<div class="div-info-valor div_modo selectsPlaca">
								<label for="">Modo de configuração: </label>
								<select name="modo_placa" id="selectModoPlaca">
									<?php

									$modo = $coluna['modo'];
									if ($modo == 'dinamica') {

										echo '
												<option value="dinamica">dinamica</option>
												<option value="estatica">estatica</option>
											';
									} else {

										echo '
												<option value="estatica">estatica</option>
												<option value="dinamica">dinamica</option>	
												';
									}

									?>
								</select>
							</div>


							<div class="div-info-valor div_status selectsPlaca">
								<label for="">Status: </label>
								<select name="status" id="selectStatusPlaca">
									<?php
									$status = $coluna['status'];
									if ($status == 1) {

										echo '
										
										<option value="1">ativa</option>
										<option value="0">inativa</option>
										
									';
									} else {
										echo '
											
										<option value="0">inativa</option>
										<option value="1">ativa</option>
											
										';
									}

									?>
								</select>
							</div>
							<div class="div-info-valor">
								<label for="">Endereço IPv4: </label>
								<?php echo '<input name="ip" class="" type="text" value="' . $coluna["ip"] . '">'; ?>
							</div>
							<div class="div-info-valor">
								<label for="">Máscara de sub-rede: </label>
								<?php echo '<input name="mask" class="mask" type="text" value="' . $coluna["mascara"] . '">'; ?>
							</div>

							<div class="div-info-valor">
								<label for="">Gateway padrão IPv4: </label>
								<?php echo '<input name="gw" class="gw" type="text" value="' . $coluna["gateway"] . '">'; ?>
							</div>

							<div class="div-info-valor">
								<label for="">Rede IPv4: </label>
								<?php echo '<input name="rede" class="" type="text" value="' . $coluna["rede"] . '">'; ?>
							</div>
							<div class="div-info-valor">
								<label for="">Broadcast IPv4: </label>
								<?php echo '<input name="bcast" class="" type="text" value="' . $coluna["broadcast"] . '">'; ?>
							</div>

							<div class="div-info-valor">
								<label for="">DNS Primário: </label>
								<?php echo '<input name="dns1" class="dns1" type="text" value="' . $coluna["dns1"] . '">'; ?>
							</div>

							<div class="div-info-valor">
								<label for="">DNS Secundário: </label>
								<?php echo '<input name="dns2" class="dns2" type="text" value="' . $coluna["dns2"] . '">'; ?>
							</div>
							
							<button id="save" class="btn btn-block btn-outline-dark">Aplicar</button>

							<p id="aviso">Nada pra exibir aqui.</p>

						</form>
				<?php
				endforeach;
			} else {

				echo "Não há interfaces cadastradas!";
			}
				?>
					</div>





		</article>
	</section>



	<script src="_js/bootstrap/popper.js"></script>
	<script src="_js/bootstrap/bootstrap.min.js"></script>
	<script src="_js/fawesome/all.js"></script>
	<script src="_js/primary.js"></script>

</body>

</html>