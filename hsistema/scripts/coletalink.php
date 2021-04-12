<?php 
    
    $handle_interfaces = fopen('interfaces', 'r');
    $i = 0;
    $interface = new stdClass;
    $new_int = false;

    while(!feof($handle_interfaces)){
        $line = fgets($handle_interfaces);

        $regex_new_interface = "/^allow-hotplug |^auto [^lo\r\n]/i";
        $regex_iface = "/^iface [^lo\r\n]/i";
        $regex_name_interface = "/eth[0-9:]+|(^tap.+$|^tap$)/";
       
        
        if(preg_match($regex_iface, $line)){
            continue;
        }

        if(preg_match($regex_new_interface, $line)){
            preg_match($regex_name_interface, $line, $int);
            $nome = $int[0];
            $interface -> $nome = [];
            $new_int = true;
            $i = 0;
            continue;
        }
        
        if($new_int){
            $interface -> $nome[$i] = $line;
            $i++;
        }
        
        
    }
    
    
    var_dump($interface);
?>