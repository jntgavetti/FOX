<?php
    function exec_shell($command){
        
        $use = "php_exec";
        $passwd = '@Hp_#x$c';
        $conne = ssh2_connect('172.17.0.1', 5322);
        if (!$conne) die('Falha na conexão SSH !');
        ssh2_auth_password($conne, $use, $passwd);

        $cmd = ssh2_exec($conne, $command);
    
        $err_stream = ssh2_fetch_stream($cmd, SSH2_STREAM_STDERR);
        $dio_stream = ssh2_fetch_stream($cmd, SSH2_STREAM_STDIO);
    
        stream_set_blocking($err_stream, true);
        stream_set_blocking($dio_stream, true);
    
        $result_err = stream_get_contents($err_stream);
        $result_dio = stream_get_contents($dio_stream);
    
        if (strlen($result_err) == 0 || empty($result_err)) {
            echo $result_dio;
        } else {
            echo 0;
        }
    }
?>