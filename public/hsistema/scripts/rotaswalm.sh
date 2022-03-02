#!/bin/bash

## variaveis
echo -e " - Variaveis"
SF="/sbin/iptables"
RT="/sbin/route"

LO="127.0.0.1"
DEV_LO="lo"

#Dominio
DOM_CLI=`cat /opt/hsistema/config/cliente.txt | grep dominio_cliente | cut -d '=' -f 2`

#default gateway
DEV_EXT01=`cat /opt/hsistema/config/cliente.txt | grep int_prov1 | cut -d '=' -f 2`

#link2
DEV_EXT02=`cat /opt/hsistema/config/cliente.txt | grep int_prov2 | cut -d '=' -f 2`

DEV_INT01=`cat /opt/hsistema/config/cliente.txt | grep int_interna1 | cut -d '=' -f 2`

NET_EXT="0.0.0.0/0.0.0.0"
IP_EXT01=`cat /opt/hsistema/config/$DEV_EXT01.conf | grep address | cut -d ' ' -f 2`
IP_EXT02=`cat /opt/hsistema/config/$DEV_EXT02.conf | grep address | cut -d ' ' -f 2`
IP_INT01=`cat /opt/hsistema/config/$DEV_INT01.conf | grep address | cut -d ' ' -f 2`

NET_EXT01=`cat /opt/hsistema/config/$DEV_EXT01.conf | grep range | cut -d ' ' -f 2`
NET_EXT02=`cat /opt/hsistema/config/$DEV_EXT02.conf | grep range | cut -d ' ' -f 2`
NET_INT01=`cat /opt/hsistema/config/$DEV_INT01.conf | grep range | cut -d ' ' -f 2`

GATE01=`cat /opt/hsistema/config/$DEV_EXT01.conf | grep gateway | cut -d ' ' -f 2`
GATE02=`cat /opt/hsistema/config/$DEV_EXT02.conf | grep gateway | cut -d ' ' -f 2`

DNS0001=`cat /opt/hsistema/config/cliente.txt | grep dns_01 | cut -d '=' -f 2`
DNS0002=`cat /opt/hsistema/config/cliente.txt | grep dns_02 | cut -d '=' -f 2`
DNS0003=`cat /opt/hsistema/config/cliente.txt | grep dns_03 | cut -d '=' -f 2`
DNS0004=`cat /opt/hsistema/config/cliente.txt | grep dns_04 | cut -d '=' -f 2`

DNS0101=`cat /opt/hsistema/config/$DEV_EXT01.conf | grep dns1 | cut -d ' ' -f 2`
DNS0102=`cat /opt/hsistema/config/$DEV_EXT01.conf | grep dns2 | cut -d ' ' -f 2`

DNS0201=`cat /opt/hsistema/config/$DEV_EXT02.conf | grep dns1 | cut -d ' ' -f 2`
DNS0202=`cat /opt/hsistema/config/$DEV_EXT02.conf | grep dns2 | cut -d ' ' -f 2`

LINK1="www.ossec.net"
LINK2="www.icann.org"
LINK3="www.registro.br"

#Rotas fixas
for i in `cat /opt/hsistema/regras/rotas_fixas`; do
        ROTA=`echo $i | cut -d ',' -f 1`
        RANGE=`echo $i | cut -d ',' -f 2`
        NMASK=`echo $i | cut -d ',' -f 3`
        if [ $STATUS = "l1" ]; then
                $RT add -net $RANGE netmask $NMASK gw GATE01
        fi
        if [ $STATUS = "l2" ]; then
                $RT add -net $RANGE netmask $NMASK gw GATE02
        fi
done

iniciar(){

        # Rota LK1 e LK2

        $RT del default gw $GATE01
        $RT del default gw $GATE02

        $RT add default gw $GATE01

        /opt/hsistema/scripts/firewall

        $SF -t mangle -N route2
        $SF -t mangle -A route2 -j MARK --set-mark 4
        ###### regras de excessao para o link 2 ###########
        $SF -t mangle -I route2 -d $NET_EXT02  -j RETURN
        for EVPN in $(cat /opt/hsistema/regras/end_vpns)
        do
                $SF -t mangle -I route2 -d $EVPN -j RETURN
        done

        for IP in $(cat /opt/hsistema/regras/link2)
        do
                $SF -A PREROUTING -s $IP -t mangle -j route2
        done

        # definindo as regras do iproute
        # link1
        ip rule del fwmark 6 table link1
        ip route del default via $GATE01 dev $DEV_EXT01 table link1
        ip rule add fwmark 6 table link1
        ip route add default via $GATE01 dev $DEV_EXT01 table link1

        # link2
        ip rule del fwmark 4 table link2
        ip route del default via $GATE02 dev $DEV_EXT02 table link2
        ip rule add fwmark 4 table link2
        ip route add default via $GATE02 dev $DEV_EXT02 table link2

        # atualizando as regras
        ip route flush cache

        /usr/local/squid/sbin/squid -k reconfigure

        echo "Rotas redirecionadas para os 2 Links"
}

link1(){

        $RT del default gw $GATE01
        $RT del default gw $GATE02

        $RT add -net $LINK1 netmask 255.255.255.255 gw $GATE01
        $RT add -net $LINK2 netmask 255.255.255.255 gw $GATE02
        $RT add -net $LINK3 netmask 255.255.255.255 gw $GATE02

        $RT add -net $DNS0001 netmask 255.255.255.255 gw $GATE01
        $RT add -net $DNS0002 netmask 255.255.255.255 gw $GATE01
        $RT add -net $DNS0101 netmask 255.255.255.255 gw $GATE01
        $RT add -net $DNS0102 netmask 255.255.255.255 gw $GATE01
        $RT add -net $DNS0201 netmask 255.255.255.255 gw $GATE02
        $RT add -net $DNS0202 netmask 255.255.255.255 gw $GATE02

        $RT add default gw $GATE01

        echo domain $DOM_CLI > /etc/resolv.conf
        echo search $DOM_CLI >> /etc/resolv.conf
        echo nameserver 8.8.8.8 >> /etc/resolv.conf

        /usr/local/squid/sbin/squid -k reconfigure

        /opt/hsistema/scripts/firewall

        echo "Rotas redirecionadas para o Link 1"
}

link2(){

        $RT del default gw $GATE01
        $RT del default gw $GATE02

        $RT add -net $LINK1 netmask 255.255.255.255 gw $GATE01
        $RT add -net $LINK2 netmask 255.255.255.255 gw $GATE02
        $RT add -net $LINK3 netmask 255.255.255.255 gw $GATE02

        $RT add -net $DNS0001 netmask 255.255.255.255 gw $GATE02
        $RT add -net $DNS0002 netmask 255.255.255.255 gw $GATE02
        $RT add -net $DNS0101 netmask 255.255.255.255 gw $GATE01
        $RT add -net $DNS0102 netmask 255.255.255.255 gw $GATE01
        $RT add -net $DNS0201 netmask 255.255.255.255 gw $GATE02
        $RT add -net $DNS0202 netmask 255.255.255.255 gw $GATE02

        $RT add default gw $GATE02

        echo domain $DOM_CLI > /etc/resolv.conf
        echo search $DOM_CLI >> /etc/resolv.conf
        echo nameserver $DNS0001 >> /etc/resolv.conf
        echo nameserver $DNS0002 >> /etc/resolv.conf
        echo nameserver $DNS0201 >> /etc/resolv.conf
        echo nameserver $DNS0202 >> /etc/resolv.conf

        /usr/local/squid/sbin/squid -k reconfigure

        /opt/hsistema/scripts/firewall

        echo "Rotas redirecionadas para o Link 2"
}

case "$1" in
"start") iniciar ;;
"l1") link1 ;;
"l2") link2 ;;
*) echo "Use os parametros, start, l1 ou l2"
esac

