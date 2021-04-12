<?php
require_once("_model/Provedores.php");
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
	} else {
		header("Location: index.php");
		exit;
	}
}

$classe_provedor = new Provedor();
$classe_interface = new Manipulacao();
$lista_interface = $classe_interface->listar(null, "interfaces");

?>
<!DOCTYPE HTML>
<html>

<head>
	<title>FOX - Adicionar provedor</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="_img/fox_icon.png" />
	<link rel="stylesheet" href="_css/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" href="_css/primary.css" />
	<script src="_js/jquery/jquery.js"></script>
</head>

<body id="body-dashboard" class="body-provedores body-add-provedor body-interface">

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
				<span class="nivelAcesso <?php echo $nivel_login; ?>">
				</span>
				<a href="#">Provedores</a>
				| <a href="#" class="active">Adicionar provedor</a>
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
		<article class="container">

			
					<div class="interface">
						<div class="legend">
							<i class="fas fa-globe"></i>
							<h3 class="d-inline">Adicionar provedor</h3>
						</div>
						<button id="delete" class="btn btn-outline-danger">Apagar</button>
						<form id="formProvedor" class="div-conf div-conf-enabled">

							<div class="div-info-valor div_provedor" id="">
								<label for="">Provedor: </label>
								<input name="provedor" class="provedor" type="text" required=true>
							</div>
						
							<div class="div-info-valor div_eth">
								<label for="eth">Interface: </label>
								<select name="interface" id="">
								<?php 
									
									foreach($lista_interface as $interfaces){
										if($interfaces['ethernet'] == $interface){
											echo '<option value="'.$interfaces['ethernet'].'">
											'.$interfaces['ethernet'].'
											</option>';
										}else{
											continue;
										}
										
									}

									foreach($lista_interface as $interfaces){
										if($interfaces['ethernet'] != $interface){
											echo '<option value="'.$interfaces['ethernet'].'">
											'.$interfaces['ethernet'].'
											</option>';
										}else{
											continue;
										}
									}
								
								
								?>
								</select>
							</div>
							
							<div class="div-info-valor div_prioridade">
								<label for="">Prioridade: </label>
								<select name="prioridade" class="" id="select_prioriodade">
										<option value="principal">principal</option>
										<option value="backup">backup</option>
								</select>
							</div>
							
							<div class="div-info-valor div_modo ">
								<label for="">Modo de operação: </label>
								<select name="modo_operacao" id="selectModoPlaca">
									<option value="nat">nat</option>
									<option value="bridge">bridge</option>
									<option value="pppoe">PPPoE</option>
								</select>
							</div>
								
							<div class="div-info-valor div_d_pppoe">
								<label for="">Dispositivo PPPoE: </label>
								<input name="d_pppoe" class="d_pppoe" type="text" required=required>
							</div>
							
							<div class="div-info-valor div_u_pppoe">
								<label for="">Usuario: </label>
								<input name="u_pppoe" class="u_pppoe" type="text" required=required>
							</div>
							
							<div class="div-info-valor div_s_pppoe">
								<label for="">Senha: </label>
								<input name="s_pppoe" class="s_pppoe" type="text" required=required>
							</div>
										
								

							<div class="div-info-valor" id="divStatusPlaca">
								<label for="">Status: </label>
								<select name="status" id="selectStatusPlaca">
									<option value="1">ativa</option>
									<option value="0">inativo</option>
								</select>
							</div>
							
							<button id="save" class="btn btn-block btn-outline-dark">Aplicar</button>

							<p class="aviso"></p>

						</form>
					</div>





		</article>
	</section>
	</div>
	<script src="_js/bootstrap/popper.js"></script>
	<script src="_js/bootstrap/bootstrap.min.js"></script>
	<script src="_js/fawesome/all.js"></script>
	<script src="_js/primary.js"></script>
</body>

</html>