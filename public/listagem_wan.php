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
	} else {
		header("Location: index.php");
		exit;
	}
}

$manipulacao = new Manipulacao();
$listagem = $manipulacao->listar(null, "interfaces");
?>
<!DOCTYPE HTML>
<html>

<head>
	<title>FOX - Rede e internet</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="_img/fox_icon.png" />
	<link rel="stylesheet" href="_css/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" href="_css/primary.css" />
	<link rel="stylesheet" href="_js/jquery-mobile/jquery.mobile-1.4.5.min.css" />
	<script src="_js/jquery/jquery.js"></script>
	<script src="_js/jquery-ui-1.12.1/jquery-ui.min.js"></script>

</head>

<body id="body-dashboard" class="body-interface body-lista-wan">

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
				<span class="hash_nivel_acesso <?php echo $nivel_login; ?>">
				</span>
				<a href="#" class="active">WAN</a>
			</div>
			<div class="conf">
				<span><?php echo $nome_usuario; ?></span>
				<i class="fas fa-cog"></i>
				<a href='#' onclick='window.location.assign("logout.php")'>
					<i class='fas fa-power-off'></i>
				</a>
			</div>
		</nav>



		<article id="lista">
			<div class="lista-interface">
				<h3 class="text-center">Selecione uma interface</h3>

				<aside class="aside-add">
					<a href="#">
						<i class="fas fa-plus-circle fa-2x"></i>
					</a>
				</aside>

				<hr>
				<table class="table table-bordered table-hover">
					<thead>
						<th>Interface</th>
						<th>Modo de configuração</th>
						<th>Endereço IPv4</th>
						<th>Gateway Padrão</th>
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
									<td>' . $coluna["ethernet"] . '</td>
									<td>' . $coluna["modo"] . '</td>
									<td>' . $coluna["ip"] . '</td>
									<td>' . $coluna["gateway"] . '</td>
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