<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_usuario'])) {
	session_destroy();
	header("Location: index.php");
	exit;
} else {

	ini_set('display_errors', 1);
	error_reporting('E_ALL');
	require_once('ssh.php');
	$action = $_POST['action'];
	$file_redirecli = '../regras/firewall/redirec_cli';
	$file_redirepri = '../regras/firewall/redirec_pri';
	$arr_processamento = [];

	if ($action == "getRedirec") {
		$html = [];
		$count_props = 0;

		$handle = fopen($file_redirepri, "r");

		while (!feof($handle)) {
			$line = trim(fgets($handle));
			$line = rtrim($line, ";");


			if ($line[0] != "#" && !empty($line) && strlen($line) != 0) {

				if ($_SESSION['nivel'] != 'admin') {
					$html[$count_props] = "<tr class='admin disabled'>";
				} else {
					$html[$count_props] = "<tr class='admin'>";
				}
				$html[$count_props] .= "<td class='tdCheckbox'><input type='checkbox'></td>";
				$split_line = explode(";", $line);

				foreach ($split_line as $key => $prop) {
					if ($key == 1) {
						if ($prop == "a") {
							$html[$count_props] .= "<td class='text-success'>ativo</td>";
						} else if ($prop == "i") {
							$html[$count_props] .= "<td class='text-danger'>inativo</td>";
						} else {
							$html[$count_props] .= "<td>" . $prop . "</td>";
						}
					} else {
						$html[$count_props] .= "<td>" . $prop . "</td>";
					}
				}

				$html[$count_props] .= "</tr>";
				$count_props++;
			}
		}

		$handle = fopen($file_redirecli, "r");

		while (!feof($handle)) {
			$line = trim(fgets($handle));
			$line = rtrim($line, ";");


			if ($line[0] != "#" && !empty($line) && strlen($line) != 0) {
				$html[$count_props] = "<tr class='cliente'>";
				$html[$count_props] .= "<td class='tdCheckbox'><input type='checkbox'></td>";
				$split_line = explode(";", $line);

				foreach ($split_line as $key => $prop) {

					if ($key == 1) {
						if ($prop == "a") {
							$html[$count_props] .= "<td class='text-success'>" . "ativo" . "</td>";
						} else if ($prop == "i") {
							$html[$count_props] .= "<td class='text-danger'>" . "inativo" . "</td>";
						} else {
							$html[$count_props] .= "<td>" . $prop . "</td>";
						}
					} else {
						$html[$count_props] .= "<td>" . $prop . "</td>";
					}
				}

				$html[$count_props] .= "</tr>";
				$count_props++;
			}
		}

		echo json_encode($html);
	}

	if ($action == "add") {
		$redirec = json_decode($_POST['redirec']);
		$line = "";

		foreach ($redirec as $prop) {
			$line .= $prop . ";";
		}

		if ($_SESSION['nivel'] == 'admin') {
			$file = $file_redirepri;
		} else {
			$file = $file_redirecli;
		}

		if ($handle = fopen($file, 'a+')) {
			fwrite($handle, "\n" . $line);
			$status = 1;
		} else {
			$status = 0;
			$detalhe = "Não foi possível abrir o arquivo para gravação. <br> Codigo: ADAG03";
		}



		$arr_processamento["status"] = $status;
		$arr_processamento["detalhe"] = $detalhe;
		echo json_encode($arr_processamento);
	}

	if ($action == "edit") {
		$redirec = json_decode($_POST['redirec']);
		$redirec_old = json_decode($_POST['old_redirec']);
		$nivelTR = $_POST['nivelTR'];
		$line = "";
		$new_line = "";
		$final_array = [];
		$new_redirec = "";
		$score = 0;
		$i = 0;
		if ($_SESSION['nivel'] == 'admin') {
			if($nivelTR == 'cliente'){$file = $file_redirecli;}
			else{$file = $file_redirepri;}
		} else {
			$file = $file_redirecli;
		}


		$handle = fopen($file, "r");
		while (!feof($handle)) {
			$line = trim(fgets($handle));
			$line = rtrim($line, ";");
			$score = 0;


			if ($line[0] != "#" && !empty($line) && strlen($line) != 0) {
				$split_line[$i] = explode(";", $line);

				foreach ($redirec_old as $key => $old_prop) {
					foreach ($split_line[$i] as $split_prop) {
						if (strcasecmp($old_prop, $split_prop) == 0) {
							$score++;
							break;
						}
					}
					if ($score == 0 || $score == 7) {
						break;
					}
				}


				if ($score == 7) {
					foreach ($redirec as $prop) {
						$new_line .= $prop . ";";
					}
					$final_array[$i] = $new_line;
				} else {
					$final_array[$i] = $line;
				}
				$i++;
			}
		}

		if ($handle = fopen($file, 'w+')) {
			foreach ($final_array as $line) {
				fwrite($handle, $line . "\n");
			}
			$status = 1;
		} else {
			$status = 0;
			$detalhe = "Não foi possível abrir o arquivo para gravação. <br> Codigo: ADAG03";
		}



		$arr_processamento["status"] = $status;
		$arr_processamento["detalhe"] = $detalhe;
		echo json_encode($arr_processamento);
	}

	if ($action == "del") {

		$redirec = json_decode($_POST['redirec']);
		$nivelTR = $_POST['nivelTR'];
		$line = "";
		$final_array = [];
		$i = 0;

		
		if ($_SESSION['nivel'] == 'admin') {
			if($nivelTR == 'cliente'){$file = $file_redirecli;}
			else{$file = $file_redirepri;}
		} else {
			$file = $file_redirecli;
		}

		$handle = fopen($file, "r");
		while (!feof($handle)) {

			$line = trim(fgets($handle));
			$line = rtrim($line, ";");
			$score = 0;


			if ($line[0] != "#" && !empty($line) && strlen($line) != 0) {
				$split_line[$i] = explode(";", $line);

				foreach ($redirec as $key => $prop) {

					foreach ($split_line[$i] as $split_prop) {
						if (strcasecmp($prop, $split_prop) == 0) {
							$score++;
							break;
						}
					}

					if ($score == 0 || $score == 7) {
						break;
					}
				}


				if ($score == 7) {
					continue;
				} else {
					$final_array[$i] = $line;
				}
				$i++;
			}
		}


		if ($handle = fopen($file, 'w+')) {
			foreach ($final_array as $line) {
				fwrite($handle, $line . "\n");
			}
			$status = 1;
		} else {
			$status = 0;
			$detalhe = "Não foi possível abrir o arquivo para gravação. <br> Codigo: ADAG03";
		}

		$arr_processamento["status"] = $status;
		$arr_processamento["detalhe"] = $detalhe;
		echo json_encode($arr_processamento);
	}
}
