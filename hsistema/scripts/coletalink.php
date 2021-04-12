DEV_EXT=`cat /opt/hsistema/config/cliente.txt | grep int_prov$enum | cut -d '=' -f2`
DEV_TY=`cat /opt/hsistema/config/cliente.txt | grep dis_prov$enum | cut -d '=' -f 2`
OPERAD=`cat /opt/hsistema/config/cliente.txt | grep oper_prov$enum | cut -d '=' -f 2`
LINK_S=`cat /opt/hsistema/config/cliente.txt | grep def_prov$enum | cut -d '=' -f 2`
PROVE=`cat /opt/hsistema/config/cliente.txt | grep def_provedor$enum | cut -d '=' -f 2`
ADDR=`cat /opt/hsistema/config/$DEV_EXT.conf | grep address | cut -d ' ' -f 2`
GATE=`cat /opt/hsistema/config/$DEV_EXT.conf | grep gateway | cut -d ' ' -f 2`

echo "$DEV_EXT" > /opt/hsistema/links/w"$enum"dispositivo
echo "$OPERAD" > /opt/hsistema/links/w"$enum"nome
echo "$LINK_S" > /opt/hsistema/links/w"$enum"Prioridade
echo "$ADDR" > /opt/hsistema/links/w"$enum"ipPlaca
echo "$GATE" > /opt/hsistema/links/w"$enum"Gateway


<?php

$handle_interfaces = fopen('interfaces', 'r');
$i = 0;
$interface = new stdClass;
$new_int = false;

while (!feof($handle_interfaces)) {
    $line = fgets($handle_interfaces);

    $regex_new_interface = "/^allow-hotplug |^auto [^lo\r\n]/i";
    $regex_iface = "/^iface [^lo\r\n]/i";
    $regex_name_interface = "/eth[0-9:]+|(^tap.+$|^tap$)/";


    if (preg_match($regex_iface, $line)) {
        continue;
    }

    if (preg_match($regex_new_interface, $line)) {
        preg_match($regex_name_interface, $line, $int);
        $nome = $int[0];
        $interface->$nome = [];
        $new_int = true;
        $i = 0;
        continue;
    }

    if ($new_int) {
        $interface->$nome[$i] = $line;
        $i++;
    }
}


var_dump($interface);
?>