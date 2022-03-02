<?php
require_once("_model/Provedores.php");

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
$listagem = $classe_provedor->listar('-1');
?>
<!DOCTYPE HTML>
<html>

<head>
	<title>FOX - Provedores de internet</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="_img/fox_icon.png" />
	<link rel="stylesheet" href="_css/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" href="_css/primary.css" />
	<link rel="stylesheet" href="_js/jquery-mobile/jquery.mobile-1.4.5.min.css" />
	<script src="_js/jquery/jquery.js"></script>
	<script src="_js/jquery-ui-1.12.1/jquery-ui.min.js"></script>

</head>

<body id="body-dashboard" class="body-interface body-lista-provedor">

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
				<a href="#" class="active">Provedores de internet</a>
			</div>
			<div class="conf">
				<span><?php echo $nome_usuario; ?></span>
				<i class="fas fa-cog"></i>
				<a href='#' onclick='window.location.assign("_controler/logout.php")'>
					<i class='fas fa-power-off'></i>
				</a>
			</div>
		</nav>


		<article id="lista">
			<div class="lista-provedor">
				<h3 class="text-center">Selecione um provedor</h3>

				<aside class="aside-add">
					<a href="#">
						<i class="fas fa-plus-circle fa-2x"></i>
					</a>
				</aside>

				<hr>
				<table class="table table-bordered table-hover">
					<thead>
						<th>Provedor</th>
						<th>Modo de operação</th>
						<th>Prioridade</th>
						<th>Interface</th>
						<th>IPv4 Válido</th>
						<th>Status</th>
					</thead>
					<tbody>
						<?php
							
							
							foreach ($listagem as $coluna) {
								if ($coluna['status'] == 1) {
									$status = "<td class='text-success'>ativo</td>";
								} else {
									$status = "<td class='text-danger'>inativo</td>";
								}
								echo '
									<tr>	
										<td class="d-none">' . $coluna["id_provedor"] . '</td>
										<td>' . $coluna["provedor"] . '</td>
										<td>' . $coluna["modo_operacao"] . '</td>
										<td>' . $coluna["prioridade"] . '</td>
										<td>' . $coluna["interface"] . '</td>
										<td>' . $coluna["ip_valido"] . '</td>
										
										' . $status . '
									</tr>
							';
							}
						?>
					</tbody>
				</table>
			</div>
		</article>

	</section>



	<script src="_js/bootstrap/popper.js"></script>
	<script src="_js/bootstrap/bootstrap.min.js"></script>
	<script src="_js/fawesome/all.js"></script>

	<script src="_js/primary.js"></script>

</body>

</html>