#!/bin/bash
PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"
export LANG=pt_BR.UTF-8
ONTEM=$(date +%Y"-"%m"-"%d -d "1 days ago")
DIA=$(date +%Y"-"%m"-"%d)
DIAHORA=$(date +%d"/"%m"/"%Y"-"%H":"%M":"%S)

if [ "$1" != " " ]; then
        if [ "$1" = "versao" ]; then
                echo -e v1.00 32
                exit
        else
                echo
        fi
fi

if [ "$1" != " " ]; then
        if [ "$1" = "show" ]; then
                echo
        else
                exec 1>>/opt/hsistema/log/$DIA-linkteste.log 2>&1
        fi
fi

echo -e "\n\t$DIAHORA - Link teste \n"

SF="/sbin/iptables"
MD="/sbin/modprobe"
RT="/sbin/route"
IP="/sbin/ip"
NP="/usr/bin/nmap"
IS="/usr/sbin/ipsec"

SAFE_ADDR="/opt/hsistema/config/enderecos_seguros"
LINKS_EXT=$(cat /opt/hsistema/config/cliente.txt | grep links_externos | cut -d '=' -f2)

teste() {
        $1 = INTERFACE
        $2 = PORTA
        $3 = DESTINO
        $4 = QTD

        report_ping="$(ping -p 80 -c 3 -q -I eth0 192.168.0.1)"
        average="$(echo $report_ping | grep rtt | cut -d '=' -f2 | cut -d '/' -f 3)"
        packet_loss="$(echo $report_ping | grep -Po '[0-9]+%')"
}

link_fail() {
        LSTATUS=down
        IP_EXTERNO=$(cat /opt/hsistema/config/ip_$INTERFACE.txt)
        echo "$IP_EXTERNO" >/opt/hsistema/links/w"$enum"ipExterno
        echo "down" >/opt/hsistema/links/w"$enum"status
        echo "down" >/opt/hsistema/config/$INTERFACE.status
        ver_hist

}

link_ok() {
        LSTATUS=up
        IP_EXTERNO=$(lynx -connect_timeout=5 -dump https://www.h2info.com.br/suporte/ | grep "Seu IP" | cut -d ' ' -f4)
        echo "$IP_EXTERNO" >/opt/hsistema/links/w"$enum"ipExterno
        echo "$IP_EXTERNO" >/opt/hsistema/config/ip_$INTERFACE.txt
        echo "up" >/opt/hsistema/links/w"$enum"status
        echo "up" >/opt/hsistema/config/$INTERFACE.status
        ver_hist
}

ver_hist() {
        LSTATUSOLD=$(cat /opt/hsistema/links/w"$enum"status.old)
        if [ $LSTATUSOLD = $LSTATUS ]; then
                echo link inalterado
        else
                echo "$DIAHORA - $INTERFACE - $LSTATUS" >>/opt/hsistema/links/$INTERFACE-status.hist
                echo "$DIAHORA - $INTERFACE - $LSTATUS" >/opt/hsistema/links/w"$enum"status.hsite
                echo "$LSTATUS" >/opt/hsistema/links/w"$enum"status.old
        fi
}

# Teste de link

cont=0
while [ $cont -lt $LINKS_EXT ]; do

        cont=$(($cont + 1))

        STATUS_LINK=$(cat /opt/hsistema/config/cliente.txt | grep sta_prov$enum | cut -d '=' -f 2)

        if [ "$STATUS_LINK" != "" ]; then

                if [ $STATUS_LINK = "ativo" ]; then

                        INTERFACE=$(cat /opt/hsistema/config/cliente.txt | grep int_prov$enum | cut -d '=' -f2)
                        DEV_TYPE=$(cat /opt/hsistema/config/cliente.txt | grep dis_prov$enum | cut -d '=' -f 2)
                        OPERAD=$(cat /opt/hsistema/config/cliente.txt | grep oper_prov$enum | cut -d '=' -f 2)
                        LINK_S=$(cat /opt/hsistema/config/cliente.txt | grep def_prov$enum | cut -d '=' -f 2)
                        PROVE=$(cat /opt/hsistema/config/cliente.txt | grep def_provedor$enum | cut -d '=' -f 2)
                        ADDR=$(cat /opt/hsistema/config/$INTERFACE.conf | grep address | cut -d ' ' -f 2)
                        GATE=$(cat /opt/hsistema/config/$INTERFACE.conf | grep gateway | cut -d ' ' -f 2)


                        

                        $RT -n | grep $INTERFACE
                        if [ $? -eq 1 ]; then
                                link_fail
                        else
                                cont_addr=0

                                while read addr; do
                                        $DNS_ADDR="$addr" | cut -d ';' -f 1
                                        $IP_ADDR="$addr" | cut -d ';' -f 2

                                        if [ $DEV_TYPE = dev ]; then
                                                $RT add -net $DNS_ADDR netmask 255.255.255.255 gw $GATE dev $INTERFACE
                                                $RT add -net $IP_ADDR netmask 255.255.255.255 gw $GATE dev $INTERFACE
                                                $IP route flush cache
                                        else
                                                $RT add -net $LINK5 netmask 255.255.255.255 gw $GATE
                                                $IP route flush cache
                                        fi

                                done \
                                        < \
                                        \
                                        \
                                        $SAFE_ADDR

                        fi

                fi
        fi
done

DIAHORA=$(date +%d"/"%m"/"%Y"-"%H":"%M":"%S)
echo -e "\n\t$DIAHORA - Fim da Rotina Link teste \n"
