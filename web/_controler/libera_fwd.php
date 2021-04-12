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
	$file = '../regras/firewall/libera_fwd_int';
	$arr_processamento = [];

	if ($action == "get") {
		$req = $_POST['req'];
		$html = [];
		$count_props = 0;
		$handle = fopen($file, "r");

		if ($req == "null") {
			while (!feof($handle)) {
				$line = trim(fgets($handle));
				$line = rtrim($line, ";");


				if ($line[0] != "#" && !empty($line) && strlen($line) != 0) {

					$html[$count_props] = "<tr>";
					$html[$count_props] .= "<td class='tdCheckbox'><input type='checkbox'></td>";
					$split_line = explode(";", $line);

					foreach ($split_line as $key => $prop) {

						switch ($key) {

							case 1:

								if ($prop == "b") {
									$prop = "bidirecional";
								} else if ($prop == "u") {
									$prop = "unidirecional";
								}
								break;

							case 2:
								if ($prop == "all" || $prop == "tcp/udp/icmp") {$prop = "todos";} 
								break;
							case 3:
							case 5:
								if ($prop == "+") {
									$prop = "qualquer";
								}
								break;

							case 4:
								if ($prop == "0.0.0.0/0") {
									$prop = "qualquer";
								}
								break;
						}

						$html[$count_props] .= "<td>" . $prop . "</td>";
					}

					$html[$count_props] .= "</tr>";
					$count_props++;
				}
			}

			echo json_encode($html);
		} else {

			$saida = shell_exec("sudo cat /opt/hsistema/config/redes_int 2>&1");
			$saida_explode = explode("\n", $saida);
			$redes = [];

			foreach ($saida_explode as $key => $rede) {
				if (!empty($rede) && strlen($rede) != 0) {
					$redes[$key] = $rede;
				}
			}
			$obj->redes = $redes;


			$saida = shell_exec("sudo cat /opt/hsistema/config/placas_internas 2>&1");
			$saida_explode = explode("\n", $saida);
			$int = [];

			foreach ($saida_explode as $key => $eth) {
				if (!empty($eth) && strlen($eth) != 0) {
					$int[$key] = $eth;
				}
			}
			$obj->placas = $int;

			echo json_encode($obj);
		}
	}

	if ($action == "add") {

		$form_json = json_decode($_POST['form_json']);
		$line = "";

		foreach ($form_json as $key => $prop) {
			$line .= $prop . ";";
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
		$form_json = json_decode($_POST['form_json']);
		$old_form_json = json_decode($_POST['old_form_json']);
		$line = "";
		$new_line = "";
		$final_array = [];
		$score = 0;
		$i = 0;


		$handle = fopen($file, "r");
		while (!feof($handle)) {
			$line = trim(fgets($handle));
			$line = rtrim($line, ";");
			$score = 0;


			if ($line[0] != "#" && !empty($line) && strlen($line) != 0) {
				$split_line[$i] = explode(";", $line);

				foreach ($old_form_json as $key => $old_prop) {
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
					foreach ($form_json as $prop) {
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

		$form_json = json_decode($_POST['form_json']);

		$line = "";
		$final_array = [];
		$i = 0;

		$handle = fopen($file, "r");
		while (!feof($handle)) {

			$line = trim(fgets($handle));
			$line = rtrim($line, ";");
			$score = 0;


			if ($line[0] != "#" && !empty($line) && strlen($line) != 0) {
				$split_line[$i] = explode(";", $line);

				foreach ($form_json as $key => $prop) {

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
