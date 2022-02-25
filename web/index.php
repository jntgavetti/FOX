<?php

/*
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_usuario'])) {
	session_destroy();
	header("Location: index.php");
	exit;
} else {
	$nivel_login = $_SESSION['nivel'];
	$nome_usuario = $_SESSION['nome'];
}
*/
?>
<!DOCTYPE HTML>
<html>

<head>
	<title>FOX - Rede e internet</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="_img/fox_icon.png" />
	<link rel="stylesheet" href="bootstrap/css/bootstrap.css">
	<link rel="stylesheet" href="_css/primary.css" />
	<script src="jquery/jquery.js"></script>
	

</head>

<body class="body-interface">

	<div class="row p-0">

		<header class="col p-0">

			<nav class="nav-main nav-ident">
				<div class="p-0 logo">
					<a class="" href="#">
						<img src="_img/fox_icon.png" width="40" height="40" class="" alt="">
					</a>

				</div>
				<hr>
				<ul class="nav-ident">

					<li class="nav-ident liDisabled menu-item">
						<a class="link-item link_page nav-ident" href="internet.php">
							<img src="_img/internet.svg" class="desc-icon" alt="">
							<span class="internet">Internet</span>
						</a>
					</li>

					<li class="nav-ident liDisabled menu-item">
						<a class="link-item link_page nav-ident" href="interfaces.php">
							<img src="_img/interfaces.svg" class="desc-icon" alt="">
							<span class="internet">Interfaces</span>
						</a>
					</li>



					<li class="nav-ident liDisabled menu-item">
						<a class="link-item nav-ident" href="#">
							<img src="_img/proxy.svg" class="desc-icon" alt="">
							<span class="navega">Navegação</span>
							<i class="arrow-icon arrow-disabled icon-disabled fas fa-chevron-right text-secondary nav-ident"></i>
						</a>
						<ul class="dropdown-ul">
							<li>
								<a href="listagem_dispositivos.php" class="link_page">Dispositivos</a>
							</li>
							<li>
								<a href="listagem_grupos.php" class="link_page">Grupos</a>
							</li>
						</ul>
					</li>
					<li class="nav-ident liDisabled menu-item">
						<a class="link-item nav-ident" href="#">
							<img src="_img/firewall.svg" class="desc-icon" alt="">
							<span class="rede">Firewall</span>
							<i class="arrow-icon arrow-disabled icon-disabled fas fa-chevron-right text-secondary nav-ident"></i>
						</a>
						<ul class="dropdown-ul">
							<li>
								<a href="redireciona_portas.php" class="link_page">Redirecionamento de portas</a>
							</li>
							<li>
								<a href="libera_forward.php" class="link_page">LAN - Encaminhamento</a>
							</li>

							<li>
								<a href="listagem_servicos.php" class="link_page">Serviços</a>
							</li>
						</ul>
					</li>



				</ul>


			</nav>


		</header>


		<div class="col-10 p-0">

			<nav class="menuH">
				<button class="btn btn-show-menu navbar-toggler" type="button">
					<i class="fas fa-bars"></i>
				</button>
				<a class="navbar-brand" href="#">
					<img src="" width="30" height="30" class="mr-2 d-inline-block align-bottom" alt="">
					H2 Informatica
				</a>
				<div class="divBread">
					<span class="nivelAcesso <?php echo $nivel_login; ?>">
					</span>
					<a href="#" class="active">LAN</a>
				</div>
				<div class="conf">
					<span><?php echo $nome_usuario; ?></span>
					<i class="fas fa-cog"></i>
					<a href='#' onclick='window.location.assign("logout.php")'>
						<i class='fas fa-power-off'></i>
					</a>
				</div>
			</nav>

			<div id="conteudo">

			</div>

		</div>
	</div>


	<script src="bootstrap/js/bootstrap.min.js"></script>
	<script src="fawesome/all.js"></script>
	<script src="_js/primary.js"></script>
	<script src="_js/menu.js"></script>
</body>

</html>