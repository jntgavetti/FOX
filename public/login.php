<!DOCTYPE HTML>
<html>

<head>
	<title>FOX - Login</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="_img/fox_icon.png" />
	<link rel="stylesheet" href="_css/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" href="_css/primary.css" />
	<script src="_js/jquery/jquery.js"></script>
</head>

<body id="" class="body-login">

	<section class="section-main">

		<article class="login">
			<div class="container">
				<div class="text-center">
					<img src="_img/login_logo.png" alt="" width="60px" id="logo_login_img">
					<h1 id="logo_login_h1">Firewall</h1>
					<h2 class="h2_dashboard">Painel de controle</h2>
				</div>
				<form autocomplete="off">
					<div class="form-group">

						<label for="">Usuario: </label>
						<input type="text" class="form-control form-control-sm" name="usuario" id="user">

						<label for="">Senha:</label>
						<input type="password" class="form-control form-control-sm" name="senha" id="">
						<button name="" id="save" class="my-2 btn-block btn btn-dark">Entrar</button>
						<p class="aviso mt-4 text-center"></p>
					</div>
			</div>
			</form>
		</article>
	</section>
	<script src="_js/bootstrap/bootstrap.min.js"></script>
	<script src="_js/login.js"></script>
	
</body>

</html>