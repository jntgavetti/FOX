<?php
require_once("_model/Lan.php");


if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_usuario'])) {
	session_destroy();
	header("Location: index.php");
	exit;
}else{
	$nivel_login = $_SESSION['nivel'];
	$nome_usuario = $_SESSION['nome'];

	if($nivel_login == 'admin'){
		require_once('menu_admin.php');
		$hash_nivel_acesso = "#";
	}else{
		header("Location: index.php");
		exit;
	}
}

?>
<!DOCTYPE HTML>
<html>

<head>
	<title>FOX - Adicionar interface</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="_img/fox_icon.png" />
	<link rel="stylesheet" href="_css/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" href="_css/primary.css" />
	<script src="_js/jquery/jquery.js"></script>
</head>

<body id="body-dashboard" class="body-add-lan body-interface">
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
				<a href="listagem_lan.php">LAN |</a>
				<a href="#" class="active"> Adicionar interface</a>
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


			<div class="interface">
				<div class="legend">
					<h3 class="d-inline">Adicionar interface</h3>
				</div>
				<form method="POST" id="formInterfaceLan" class="div-conf div-conf-enabled">
					
					<div class="div-info-valor div_tipo_placa">
						<label for="">Tipo de placa: </label>
						<select name="tipo_placa" class="tipo_placa" >
							<option value="fisica">fisica</option>
							<option value="virtual">virtual</option>
						</select>
					</div>

					<div class="div-info-valor div_modo">
						<label for="">Modo de configuração: </label>
						<select name="modo_placa" id="selectModoPlaca">
							<option value="estatica">estatica</option>
							<option value="dinamica">dinamica</option>
						</select>
					</div>
					<div class="div-info-valor div_status">
						<label for="">Status: </label>
						<select name="status" id="selectStatusPlaca">
							<option value="1">ativa</option>
							<option value="0">inativa</option>
						</select>
					</div>
					<div class="div-info-valor div_eth" id="divEth">
						<label for="eth">Interface: </label>
						<input name="interface" id="eth" type="text" value="" size="10" required=true>
					</div>
					<div class="div-info-valor">
						<label for="">Endereço IPv4: </label>
						<input name="ip" class="" type="text" value="" size="15" required=true>
					</div>
					<div class="div-info-valor">
						<label for="">Máscara de sub-rede: </label>
						<input name="mask" class="mask" type="text" value="" size="15" required=true>
					</div>
					<div class="div-info-valor">
						<label for="">Rede IPv4: </label>
						<input name="rede" class="" type="text" value="" size="15" required=true>
					</div>
					<div class="div-info-valor">
						<label for="">Broadcast IPv4: </label>
						<input name="bcast" class="" type="text" value="" size="15" required=true>
					</div>
					<p class="aviso"></p>
					<button id="save" class="btn btn-block btn-outline-dark">Salvar interface</button>
				</form>

			</div>

		</article>
	</section>



	<script src="_js/bootstrap/popper.js"></script>
	<script src="_js/bootstrap/bootstrap.min.js"></script>
	<script src="_js/fawesome/all.js"></script>
	<script src="_js/primary.js"></script>

</body>

</html>