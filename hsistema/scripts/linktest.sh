#!/bin/bash
PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"
export LANG=pt_BR.UTF-8
ONTEM=`date +%Y"-"%m"-"%d -d "1 days ago"`
DIA=`date +%Y"-"%m"-"%d`
DIAHORA=`date +%d"/"%m"/"%Y"-"%H":"%M":"%S`

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
            exec 1>> /opt/hsistema/log/$DIA-linkteste.log 2>&1
        fi
fi

echo -e "\n\t$DIAHORA - Link teste \n"

SF="/sbin/iptables"
MD="/sbin/modprobe"
RT="/sbin/route"
IP="/sbin/ip"
NP="/usr/bin/nmap"
IS="/usr/sbin/ipsec"

LINK1=`cat /opt/hsistema/config/cliente.txt | grep host_12 | cut -d ' ' -f 3`


link_fail () 
                LSTATUS=down
                IP_EXTERNO=`cat /opt/hsistema/config/ip_$DEV_EXT.txt`
                echo "$IP_EXTERNO" > /opt/hsistema/links/w"$enum"ipExterno
                echo "down" > /opt/hsistema/links/w"$enum"status
                echo "down" > /opt/hsistema/config/$DEV_EXT.status
                ver_hist

}

link_ok () {
                LSTATUS=up
                IP_EXTERNO=`lynx -connect_timeout=5 -dump https://www.h2info.com.br/suporte/ | grep "Seu IP" | cut -d ' ' -f4`
                echo "$IP_EXTERNO" > /opt/hsistema/links/w"$enum"ipExterno
                echo "$IP_EXTERNO" > /opt/hsistema/config/ip_$DEV_EXT.txt
                echo "up" > /opt/hsistema/links/w"$enum"status
                echo "up" > /opt/hsistema/config/$DEV_EXT.status
                ver_hist
}

ver_hist () {
        LSTATUSOLD=`cat /opt/hsistema/links/w"$enum"status.old`
        if [ $LSTATUSOLD = $LSTATUS ]; then
                echo link inalterado
        else
                echo "$DIAHORA - $DEV_EXT - $LSTATUS" >> /opt/hsistema/links/$DEV_EXT-status.hist
                echo "$DIAHORA - $DEV_EXT - $LSTATUS" > /opt/hsistema/links/w"$enum"status.hsite
                echo "$LSTATUS" > /opt/hsistema/links/w"$enum"status.old
        fi
}


# Teste de link

enum=0
while [ $enum -lt $QLINKEXT ]; do
        enum=$(($enum+1))
        STATUS=`cat /opt/hsistema/config/cliente.txt | grep sta_prov$enum | cut -d '=' -f 2`
        if [ "$STATUS" != "" ]; then
                if [ $STATUS = "ativo" ]; then
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
			
                        $RT -n | grep $DEV_EXT
                        if [ $? -eq 1 ]; then
                                link_fail
                        else
                                if [ $DEV_TY = dev ]; then
                                        $RT add -net $LINK5 netmask 255.255.255.255 dev $DEV_EXT
                                        $RT add -net $LINK1 netmask 255.255.255.255 dev $DEV_EXT
                                        $RT add -net $LINK2 netmask 255.255.255.255 dev $DEV_EXT
                                        $RT add -net $LINK3 netmask 255.255.255.255 dev $DEV_EXT
                                        $RT add -net $LINK4 netmask 255.255.255.255 dev $DEV_EXT
                                        $IP route flush cache
                                else
                                        $RT add -net $LINK5 netmask 255.255.255.255 gw $GATE
                                        $RT add -net $LINK1 netmask 255.255.255.255 gw $GATE
                                        $RT add -net $LINK2 netmask 255.255.255.255 gw $GATE
                                        $RT add -net $LINK3 netmask 255.255.255.255 gw $GATE
                                        $RT add -net $LINK4 netmask 255.255.255.255 gw $GATE
                                        $IP route flush cache
                                fi
                                {
                                ping -p 80 -c 3 -I $DEV_EXT $LINK2 | grep ttl
                                if [ $? -eq 1 ]; then
                                        ping -p 80 -c 3 -I $DEV_EXT $LINK3 | grep ttl
                                        if [ $? -eq 1 ]; then
                                                link_fail
                                        else
                                                ping -p 443 -c 3 -I $DEV_EXT $LINK3 | grep ttl
                                                if [ $? -eq 1 ]; then
                                                        link_fail
                                                else
                                                        link_ok
                                                fi
                                        fi
                                else
                                        ping -p 443 -c 3 -I $DEV_EXT $LINK2 | grep ttl
                                        if [ $? -eq 1 ]; then
                                                link_fail
                                        else
                                                link_ok
                                        fi
                                fi
                                }

                                if [ $DEV_TY = "dev" ]; then
                                        $RT del -net $LINK5 netmask 255.255.255.255 dev $DEV_EXT
                                        $RT del -net $LINK1 netmask 255.255.255.255 dev $DEV_EXT
                                        $RT del -net $LINK2 netmask 255.255.255.255 dev $DEV_EXT
                                        $RT del -net $LINK3 netmask 255.255.255.255 dev $DEV_EXT
                                        $RT del -net $LINK4 netmask 255.255.255.255 dev $DEV_EXT
                                        $IP route flush cache
                                else
                                        $RT del -net $LINK5 netmask 255.255.255.255 gw $GATE
                                        $RT del -net $LINK1 netmask 255.255.255.255 gw $GATE
                                        $RT del -net $LINK2 netmask 255.255.255.255 gw $GATE
                                        $RT del -net $LINK3 netmask 255.255.255.255 gw $GATE
                                        $RT del -net $LINK4 netmask 255.255.255.255 gw $GATE
                                        $IP route flush cache
                                fi
                        fi
                fi
         fi
done

        DIAHORA=`date +%d"/"%m"/"%Y"-"%H":"%M":"%S`
        echo -e "\n\t$DIAHORA - Fim da Rotina Link teste \n"

