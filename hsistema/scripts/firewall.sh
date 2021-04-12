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
        elif [ "$2" = "show" ]; then
            echo
        else
            exec 1> /opt/hsistema/log/firewall.log 2>&1
        fi
fi

echo -e "\n$DIAHORA - Reinicio do Firewall \n"

exec_padrao  () {
        FIRESTATUS=`cat /opt/hsistema/config/cliente.txt | grep tipo_firewall | cut -d '=' -f2`
        if [ "$FIRESTATUS" = "completo" ];then
                exec_completo
        elif [  "$FIRESTATUS" = "basico" ];then
                exec_basico
        else
                echo -e "** Verifique a configuracao do arquivo cliente, executando regras basicas ** \n"
                exec_basico
        fi
}

#------------------------------------------------------------- variaveis ----------------------------------------------------------------

echo -e "variaveis"

SF="/sbin/iptables"
MD="/sbin/modprobe"

VPNRANGE=`cat /opt/hsistema/config/cliente.txt | grep vpn_range | cut -d '=' -f2`
SOFTETHER=`cat /opt/hsistema/config/cliente.txt | grep softether_range | cut -d '=' -f2`
PROXYLIVRE=`cat /opt/hsistema/config/cliente.txt | grep lvr_proxy | cut -d '=' -f2`
PROXYTRANS=`cat /opt/hsistema/config/cliente.txt | grep sta_proxy | cut -d '=' -f2`
PROXYREDE=`cat /opt/hsistema/config/cliente.txt | grep range_proxy | cut -d '=' -f2`
IDSATIVA=`cat /opt/hsistema/config/cliente.txt | grep ids_firewall | cut -d '=' -f2`
MACCONTROL=`cat /opt/hsistema/config/cliente.txt | grep mac_controle | cut -d '=' -f2`
IPMONITOR=`cat /opt/hsistema/config/cliente.txt | grep endereco_monitor | cut -d '=' -f2`
IPGTWVPNMONITOR=`cat /opt/hsistema/config/cliente.txt | grep ip_gateway_monitor | cut -d '=' -f2`

LO="127.0.0.1"
DEV_LO="lo"

#rede externa
NET_EXT="0.0.0.0/0.0.0.0"

#interfaces externas


QLINKEXT=`cat /opt/hsistema/config/cliente.txt | grep links_externos  | cut -d '=' -f2`
echo -e Link Externos = $QLINKEXT
echo -e " "

enum=0
while [ $enum -lt $QLINKEXT ]; do
        enum=$(($enum+1))
        DEV_EXT=`cat /opt/hsistema/config/cliente.txt | grep int_prov$enum | cut -d '=' -f2`
        NET_DEVEXT=`cat /opt/hsistema/config/$DEV_EXT.conf | grep range | cut -d ' ' -f 2`
        IP_EXT=`cat /opt/hsistema/config/$DEV_EXT.conf | grep address | cut -d ' ' -f2`
        GATE=`cat /opt/hsistema/config/$DEV_EXT.conf | grep gateway | cut -d ' ' -f 2`
        echo -e WAN$enum = $DEV_EXT
        echo -e IP WAN$enum = $IP_EXT
        echo -e Gateway$enum = $GATE
        echo -e Sub-Rede = $NET_DEVEXT
        echo -e ""
	echo $NET_DEVEXT >> /opt/hsistema/config/NET_QUANT.conf
done

#interfaces internas

QLINKINT=`cat /opt/hsistema/config/cliente.txt | grep links_internos  | cut -d '=' -f2`
echo -e Link Internos = $QLINKINT
echo -e " "

for inum in $(seq -f '%02g' 1 $QLINKINT); do
        DEV_INT=`cat /opt/hsistema/config/cliente.txt | grep int_interna$inum  | cut -d '=' -f2`
        NET_INT=`cat /opt/hsistema/config/$DEV_INT.conf | grep range | cut -d ' ' -f2`
        IP_INT=`cat /opt/hsistema/config/$DEV_INT.conf | grep address | cut -d ' ' -f2`
        FIRELIVRE=`cat /opt/hsistema/config/cliente.txt | grep lvr_fire$inum | cut -d '=' -f2`
        echo -e LAN$inum = $DEV_INT
        echo -e IP LAN$inum = $IP_INT
        echo -e Sub-Rede = $NET_INT
        echo -e ""
done

#--------------------------------------------------------- Scripts ------------------------------------------------------------------------

exec_mac () {
if [ "$MACCONTROL" = "ativo" ]; then
     CMACADD=" -m mac --mac-source $MACSOURCE "
  else
     CMACADD=" "
fi
}


exec_limpa_regras () {
        # 1 - limpando regras

        echo -e "limpando"
        $SF -F
        $SF -X
        $SF -t nat -F
        $SF -t mangle -F
}

exec_ambiente () {
        # 2 - modulos

        echo -e "Modulos"
        for i in `cat /opt/hsistema/regras/modulos`; do
                $MD $i
        done

        ## ambiente
        echo -e "ambiente"

        echo "1" > /proc/sys/net/ipv4/ip_forward
        echo "0" > /proc/sys/net/ipv4/tcp_timestamps
        echo "1" > /proc/sys/net/ipv4/tcp_syncookies
        echo "1" > /proc/sys/net/ipv4/icmp_echo_ignore_broadcasts
        echo "1" > /proc/sys/net/ipv4/icmp_ignore_bogus_error_responses
        echo "0" > /proc/sys/net/ipv4/conf/all/accept_redirects
        echo "30" > /proc/sys/net/ipv4/tcp_fin_timeout
        echo "1800" > /proc/sys/net/ipv4/tcp_keepalive_time
        echo "0" > /proc/sys/net/ipv4/tcp_window_scaling
        echo "0" > /proc/sys/net/ipv4/tcp_sack
        for file in /proc/sys/net/ipv4/conf/all/accept_source_route; do
                echo "0" > $file
        done
        echo "0" > /proc/sys/net/ipv4/conf/all/log_martians

        for file in /proc/sys/net/ipv4/conf/*/rp_filter; do
                echo 0 > $file
        done
}

exec_bad_packets () {
        # 3 - Criando uma chain para "bad tcp packets"

        echo -e "Protecoes contra ataques"
        #$SF -N bad_tcp_packets
        #$SF -A bad_tcp_packets -p tcp --tcp-flags SYN,ACK SYN,ACK -m state --state NEW -j REJECT --reject-with tcp-reset
        #$SF -A bad_tcp_packets -p tcp ! --syn -m state --state NEW -j DROP
        #$SF -A bad_tcp_packets -p tcp --tcp-flags ALL FIN,URG,PSH -j DROP
        #$SF -A bad_tcp_packets -p tcp --tcp-flags ALL SYN,RST,ACK,FIN,URG -j DROP
        #$SF -A bad_tcp_packets -p tcp --tcp-flags ALL ALL -j DROP
        #$SF -A bad_tcp_packets -p tcp --tcp-flags ALL FIN -j DROP
        #$SF -A bad_tcp_packets -p tcp --tcp-flags SYN,RST SYN,RST -j DROP
        #$SF -A bad_tcp_packets -p tcp --tcp-flags SYN,ACK,FIN,RST RST -j DROP
        #$SF -A bad_tcp_packets -p tcp --tcp-flags SYN,FIN SYN,FIN -j DROP
        #$SF -A bad_tcp_packets -p tcp --tcp-flags ALL NONE -j DROP
        #$SF -A INPUT -p tcp -j bad_tcp_packets
        #$SF -A OUTPUT -p tcp -j bad_tcp_packets
        #$SF -A FORWARD -p tcp -j bad_tcp_packets

        # Limite de 12 conecxoes por segundo (burst to 24)
        #$SF -N syn-flood
        #$SF -A syn-flood -m limit --limit 12/s --limit-burst 24 -j RETURN
        #$SF -A syn-flood -j LOG --log-level info --log-prefix '#### Syn Flood ####'
        #$SF -A syn-flood -j DROP
        #$SF -A INPUT -p tcp --syn -j syn-flood
        #$SF -A OUTPUT -p tcp --syn -j syn-flood
        #$SF -A FORWARD -p tcp --syn -j syn-flood

        ########## Rejeitando Pacotes Invalidos #########################
        #$SF -I INPUT -s 0/0 -d 0/0 -m state --state INVALID -j DROP
        #$SF -I OUTPUT -s 0/0 -d 0/0 -m state --state INVALID -j DROP
        #$SF -I FORWARD -s 0/0 -d 0/0 -m state --state INVALID -j DROP
}

exec_lista_negra () {
        # 4 - Lista negra permanente

        for i in `cat /opt/hsistema/regras/listanegra`; do
                STATUS=`echo $i | cut -d ',' -f 1`
                IPSOURCE=`echo $i | cut -d ',' -f 2`
                if [ $STATUS = "host" ]; then
                $SF -A INPUT -s $IPSOURCE -j DROP
                $SF -A OUTPUT -s $IPSOURCE -j DROP
                $SF -A FORWARD -s $IPSOURCE -j DROP
                fi
        done

        for i in `cat /opt/hsistema/regras/psblock`; do
                STATUS=`echo $i | cut -d ',' -f 1`
                IPSOURCE=`echo $i | cut -d ',' -f 2`
                if [ $STATUS = "host" ]; then
                $SF -A INPUT -s $IPSOURCE -j DROP
                $SF -A OUTPUT -s $IPSOURCE -j DROP
                $SF -A FORWARD -s $IPSOURCE -j DROP
                fi
        done
}

exec_lib_teste () {
        # 5 - Libera teste 

        echo -e "teste de link"
        for IPSOURCE in `cat /opt/hsistema/regras/lista_dns`
           do
                enum=0
                while [ $enum -lt $QLINKEXT ]; do
                        enum=$(($enum+1))
                        DEV_EXT=`cat /opt/hsistema/config/cliente.txt | grep int_prov$enum | cut -d '=' -f2`
                        $SF -A INPUT -s $IPSOURCE -p icmp -i $DEV_EXT -j ACCEPT
                        $SF -A OUTPUT -d $IPSOURCE -p icmp -o $DEV_EXT -j ACCEPT
                        $SF -A INPUT -p tcp -m multiport -i $DEV_EXT -s $IPSOURCE --sport 80,443,587 -j ACCEPT
                        $SF -A OUTPUT -p tcp -m multiport -o $DEV_EXT -d $IPSOURCE --dport 80,443,587 -j ACCEPT
                done
           done
}

exec_regra_basica () {
        # 6 - regra basica

        echo -e "VPNS"
        #openvpn
        $SF -A INPUT -m multiport -p udp --dport $VPNRANGE -j ACCEPT
        $SF -A INPUT -m multiport -p udp --sport $VPNRANGE -j ACCEPT
        $SF -A OUTPUT -m multiport -p udp --dport $VPNRANGE -j ACCEPT
        $SF -A OUTPUT -m multiport -p udp --sport $VPNRANGE -j ACCEPT
	$SF -A OUTPUT -m multiport -p udp --dport $SOFTETHER -j ACCEPT
        $SF -A OUTPUT -m multiport -p udp --sport $SOFTETHER -j ACCEPT

        $SF -A INPUT -i tun+ -j ACCEPT
        $SF -A OUTPUT -o tun+ -j ACCEPT
        $SF -A FORWARD -i tun+ -j DROP
        $SF -A FORWARD -o tun+ -j DROP
        $SF -A INPUT -i tap+ -j ACCEPT
        $SF -A OUTPUT -o tap+ -j ACCEPT
        $SF -A FORWARD -i tap+ -j DROP
        $SF -A FORWARD -o tap+ -j DROP
        $SF -A INPUT -p esp -j ACCEPT
        $SF -A OUTPUT -p esp -j ACCEPT

        ##liberando psentry para a rede externa
        [ $IDSATIVA = "ativo" ] && echo -e "PSentry IDS"
        enum=0
        [ $IDSATIVA = "ativo" ] && while [ $enum -lt $QLINKEXT ]; do
                                        enum=$(($enum+1))
                                        DEV_EXT=`cat /opt/hsistema/config/cliente.txt | grep int_prov$enum | cut -d '=' -f2`
                                        $SF -A INPUT -m multiport -p tcp -i $DEV_EXT -s $NET_EXT --dport 1,7,9,11,666,4000,32771,32772,32773,32774,40421,40425,49724,54320 -j ACCEPT
                                        $SF -A OUTPUT -m multiport -p tcp -o $DEV_EXT -d $NET_EXT --sport 1,7,9,11,666,4000,32771,32772,32773,32774,40421,40425,49724,54320 -j ACCEPT
                                        $SF -A INPUT -m multiport -p udp -i $DEV_EXT -s $NET_EXT --dport 1,7,9,66,67,68,69,32770,32771,32772,32773,32774,31337,54321 -j ACCEPT
                                        $SF -A OUTPUT -m multiport -p udp -o $DEV_EXT -d $NET_EXT --sport 1,7,9,66,67,68,69,32770,32771,32772,32773,32774,31337,54321 -j ACCEPT
                                done


        echo -e "SQUID PROXY"
        enum=0
        while [ $enum -lt $QLINKEXT ]; do
                enum=$(($enum+1))
                DEV_EXT=`cat /opt/hsistema/config/cliente.txt | grep int_prov$enum | cut -d '=' -f2`
                $SF -A INPUT -p tcp -i $DEV_EXT -s $NET_EXT --sport 20:20000 -j ACCEPT
                $SF -A OUTPUT -p tcp -m owner --uid-owner squid-proxy -o $DEV_EXT -d $NET_EXT --dport 20:20000 -j ACCEPT
        done

        [ $PROXYLIVRE = "ativo" ] && for inum in $(seq -f '%02g' 1 $QLINKINT); do
                                        DEV_INT=`cat /opt/hsistema/config/cliente.txt | grep int_interna$inum  | cut -d '=' -f2`
                                        NET_INT=`cat /opt/hsistema/config/$DEV_INT.conf | grep range | cut -d ' ' -f2`
                                        $SF -A INPUT -p tcp -i $DEV_INT --dport 3128 -j ACCEPT
                                        $SF -A OUTPUT -p tcp -o $DEV_INT --sport 3128 -j ACCEPT
                                        $SF -A INPUT -p tcp -i $DEV_INT --dport 3129 -j ACCEPT
                                        $SF -A OUTPUT -p tcp -o $DEV_INT --sport 3129 -j ACCEPT
                                done

        echo -e "Acesso externo"
        enum=0
        while [ $enum -lt $QLINKEXT ]; do
                enum=$(($enum+1))
                DEV_EXT=`cat /opt/hsistema/config/cliente.txt | grep int_prov$enum | cut -d '=' -f2`
                $SF -A INPUT -p tcp -m multiport -i $DEV_EXT -s $NET_EXT --sport 80,445,587 -j ACCEPT
                $SF -A OUTPUT -p tcp -m multiport -o $DEV_EXT -d $NET_EXT --dport 80,445,587 -j ACCEPT

                $SF -A INPUT -p udp -m multiport -i $DEV_EXT -s $NET_EXT --sport 53,123 -j ACCEPT
                $SF -A OUTPUT -p udp -m multiport -o $DEV_EXT -d $NET_EXT --dport 53,123 -j ACCEPT
        done

        [ $PROXYTRANS = "ativo" ] && echo -e "Proxy transparente"
        [ $PROXYTRANS = "ativo" ] && $SF -t nat -A PREROUTING -m multiport -p tcp -s $PROXYREDE -d ! 200.201.174.0/24 --dport 80 -j REDIRECT --to-port 3128
	
	echo -e "Bloqueando Forward de redes internas ja cadastradas"
	echo -n > /opt/hsistema/config/redes_int
        for inum in $(seq -f '%02g' 1 $QLINKINT); do
                DEV_INT=`cat /opt/hsistema/config/cliente.txt | grep int_interna$inum  | cut -d '=' -f2`
		NET_INT=`cat /opt/hsistema/config/$DEV_INT.conf | grep range | cut -d ' ' -f2`
		echo -e $NET_INT >> /opt/hsistema/config/redes_int
        done

	echo -e "MONITOR - ZABBIX"
	# monitor h2info
	$SF -A OUTPUT -d $IPMONITOR -p all -j ACCEPT
	$SF -A INPUT -s $IPGTWVPNMONITOR -p all -j ACCEPT
	$SF -A OUTPUT -d $IPGTWVPNMONITOR -p all -j ACCEPT
}


exec_dns_interno () {
        # 7 - liberar acesso ao DNS interno

        echo -e "DNS interno"
        for inum in $(seq -f '%02g' 1 $QLINKINT); do
                DEV_INT=`cat /opt/hsistema/config/cliente.txt | grep int_interna$inum  | cut -d '=' -f2`
                $SF -A INPUT -p udp -i $DEV_INT --dport 53 -j ACCEPT
                $SF -A OUTPUT -p udp -o $DEV_INT  --sport 53 -j ACCEPT
        done
}
exec_dns_rede () {
        # 8 - liberar acesso ao DNS externos para rede

        echo -e "DNS externo para rede"
        for inum in $(seq -f '%02g' 1 $QLINKINT); do
                DEV_INT=`cat /opt/hsistema/config/cliente.txt | grep int_interna$inum  | cut -d '=' -f2`
                NET_INT=`cat /opt/hsistema/config/$DEV_INT.conf | grep range | cut -d ' ' -f2`
                $SF -A FORWARD -p udp -s $NET_INT -d $NET_EXT --dport 53 -j ACCEPT
                $SF -A FORWARD -p udp -d $NET_INT -s $NET_EXT --sport 53 -mstate --state ESTABLISHED,RELATED -j ACCEPT
		echo -e $NET_INT >> /opt/hsistema/config/redes_int
        done
}

exec_rede_interna () {
        # 9 - libera rede interna

        echo -e "Liberacao das redes internas"
        for inum in $(seq -f '%02g' 1 $QLINKINT); do
                DEV_INT=`cat /opt/hsistema/config/cliente.txt | grep int_interna$inum  | cut -d '=' -f2`
       		$SF -A INPUT -m multiport -p udp -i $DEV_INT --dport 20:1024,5222 -j ACCEPT
                $SF -A OUTPUT -m multiport -p udp -o $DEV_INT --sport 20:1024,5222 -j ACCEPT
                $SF -A INPUT -m multiport -p udp -i $DEV_INT --sport 20:1024,5222 -j ACCEPT
                $SF -A OUTPUT -m multiport -p udp -o $DEV_INT --dport 20:1024,5222 -j ACCEPT
                $SF -A INPUT -m multiport -p tcp -i $DEV_INT --dport 20:1024,5222,5322,5555,8090,8091,8092,8093,10050,10051 -j ACCEPT
                $SF -A OUTPUT -m multiport -p tcp -o $DEV_INT --sport 20:1024,5222,5322,5555,8090,8091,8092,8093,10050,10051 -j ACCEPT
                $SF -A INPUT -m multiport -p tcp -i $DEV_INT --sport 20:1024,5222,5322,5555,8090,8091,8092,8093,10050,10051 -j ACCEPT
                $SF -A OUTPUT -m multiport -p tcp -o $DEV_INT --dport 20:1024,5222,5322,5555,8090,8091,8092,8093,10050,10051 -j ACCEPT
	done
}

exec_bloq_fwd_interno () {
	# 10 - Bloqueia FORWARD entre redes internas
	echo -e "Bloqueando FORWARD interno"
n=0
for inum in $(seq -f '%02g' 1 $QLINKINT); do
        DEV_INT=`cat /opt/hsistema/config/cliente.txt | grep int_interna$inum  | cut -d '=' -f2`
        NET_INT=`cat /opt/hsistema/config/$DEV_INT.conf | grep range | cut -d ' ' -f2`
        INFO_INT=`wc -l /opt/hsistema/config/redes_int | cut -d ' ' -f1`
	while [ $n -lt $INFO_INT ]; do
		n=$(($n+1))
		GET_INFO=`sed -n $n'p' /opt/hsistema/config/redes_int`
		if [ $GET_INFO != $NET_INT ]; then
			$SF -A FORWARD -s $NET_INT -d $GET_INFO -j DROP
		fi
	done
	n=0
done
}



exec_lib_fwd_interno () {

        # 11 - liberar pulo de redes internas
        echo -e "Liberando FORWARD interno"

        i=0
        SFMT="-m multiport"
        SFST="-m state --state NEW,ESTABLISHED,RELATED"

        for i in `cat /opt/hsistema/regras/libera_fwd_int`; do

                FLUXO=`echo $i | cut -d ';' -f 2`
                PROTOCOLO=`echo $i | cut -d ';' -f 3`
                INT_ORIGEM=`echo $i | cut -d ';' -f 4`
                NET_ORIGEM=`echo $i | cut -d ';' -f 5`
                INT_DESTINO=`echo $i | cut -d ';' -f 6`
                NET_DESTINO=`echo $i | cut -d ';' -f 7`
                PORTA=`echo $i | cut -d ';' -f 8`
		VER_INT_VR=`echo $INT_ORIGEM | grep ':' || echo $INT_DESTINO | grep ':'`

                function regra_iptables(){

                        if [ ! -z `echo $FLUXO | grep u` ]; then

                                if [ $1 == "icmp" ]; then
                                        $SF -I FORWARD -p icmp -s $NET_ORIGEM -d $NET_DESTINO -j ACCEPT
                                else

                                    if [ -n "$VER_INT_VR" ]; then
                                        $SF -I FORWARD -p $1 -s $NET_ORIGEM -d $NET_DESTINO --sport $PORTA -j ACCEPT
                                    else
                                        $SF -I FORWARD -p $1 -i $INT_ORIGEM -s $NET_ORIGEM -o $INT_DESTINO -d $NET_DESTINO --sport $PORTA -j ACCEPT
                                    fi
                                        
                                fi

                        else
                                if [ $1 == "icmp" ]; then
                                        $SF -I FORWARD -p icmp -s $NET_ORIGEM -d $NET_DESTINO -j ACCEPT
                                        $SF -I FORWARD -p icmp -s $NET_DESTINO -d $NET_ORIGEM -j ACCEPT
                                else
                                    if [ -n "$VER_INT_VR" ]; then 
                                        $SF -I FORWARD -p $1 -s $NET_ORIGEM -d $NET_DESTINO $SFMT --sport $PORTA -j ACCEPT
                                        $SF -I FORWARD -p $1 -s $NET_DESTINO -d $NET_ORIGEM $SFMT --sport $PORTA $SFST -j ACCEPT
                                    else
                                        $SF -I FORWARD -p $1 -i $INT_ORIGEM -s $NET_ORIGEM -o $INT_DESTINO -d $NET_DESTINO $SFMT --sport $PORTA -j ACCEPT
                                        $SF -I FORWARD -p $1 -i $INT_DESTINO -s $NET_DESTINO -o $INT_ORIGEM -d $NET_ORIGEM $SFMT --sport $PORTA $SFST -j ACCEPT
                                        $SF -I FORWARD -p $1 -i $INT_ORIGEM -s $NET_ORIGEM -o $INT_DESTINO -d $NET_DESTINO $SFMT --dport $PORTA -j ACCEPT
                                        $SF -I FORWARD -p $1 -i $INT_DESTINO -s $NET_DESTINO -o $INT_ORIGEM -d $NET_ORIGEM $SFMT --dport $PORTA $SFST -j ACCEPT
                                    fi
                                fi
                        fi


                }


                if [ ! -z `echo $PROTOCOLO | grep tcp` ]; then regra_iptables "tcp"; fi
                if [ ! -z `echo $PROTOCOLO | grep udp` ]; then regra_iptables "udp"; fi
                if [ ! -z `echo $PROTOCOLO | grep udplite` ]; then regra_iptables "udplite";fi
                if [ ! -z `echo $PROTOCOLO | grep icmp` ]; then regra_iptables "icmp";fi
                if [ ! -z `echo $PROTOCOLO | grep esp` ]; then regra_iptables "esp";fi
                if [ ! -z `echo $PROTOCOLO | grep ah` ]; then regra_iptables "ah";fi
                if [ ! -z `echo $PROTOCOLO | grep sctp` ]; then regra_iptables "sctp";fi
                if [ ! -z `echo $PROTOCOLO | grep dccp` ]; then regra_iptables "dccp";fi
                if [ -z $PROTOCOLO ] || [ ! -z `echo $PROTOCOLO | grep all` ]; then
                        regra_iptables "tcp"
                        regra_iptables "udp"
                        regra_iptables "udplite"
                        regra_iptables "sctp"
                        regra_iptables "dccp"
                        regra_iptables "icmp"
                fi
        done

}





exec_ping () {
        # 12 - liberando ping

        echo -e "ping"
        for IPSOURCE in `cat /opt/hsistema/regras/lib_ping_ex`
           do
                enum=0
                while [ $enum -lt $QLINKEXT ]; do
                        enum=$(($enum+1))
                        DEV_EXT=`cat /opt/hsistema/config/cliente.txt | grep int_prov$enum | cut -d '=' -f2`
                        $SF -A INPUT -s $IPSOURCE -p icmp -i $DEV_EXT -j ACCEPT
                        $SF -A OUTPUT -d $IPSOURCE -p icmp -o $DEV_EXT -j ACCEPT
                done
           done

        # abrindo ping soh para rede interna
        for inum in $(seq -f '%02g' 1 $QLINKINT); do
                DEV_INT=`cat /opt/hsistema/config/cliente.txt | grep int_interna$inum  | cut -d '=' -f2`
                $SF -A INPUT -i $DEV_INT -p icmp -j ACCEPT
                $SF -A OUTPUT -o $DEV_INT -p icmp -j ACCEPT
        done
}

exec_suporte () {
        # 13 - Libera acesso ao ssh no firewall

        echo -e "ssh firewall"
        $SF -A INPUT -m multiport -p tcp -s suporte.h2info.com.br --dport 873,5322,8090,8091,8092,8093 -j ACCEPT
        $SF -A OUTPUT -m multiport -p tcp -d suporte.h2info.com.br --sport 873,5322,8090,8091,8092,8093 -j ACCEPT
        $SF -A INPUT -m multiport -p tcp -s suporte.h2info.com.br --sport 873,5322,8090,8091,8092,8093 -j ACCEPT
        $SF -A OUTPUT -m multiport -p tcp -d suporte.h2info.com.br --dport 873,5322,8090,8091,8092,8093 -j ACCEPT

        $SF -A INPUT -m multiport -p tcp -s suporte01h2.ddns.com.br --dport 873,5322,8090,8091,8092,8093 -j ACCEPT
        $SF -A OUTPUT -m multiport -p tcp -d suporte01h2.ddns.com.br --sport 873,5322,8090,8091,8092,8093 -j ACCEPT
        $SF -A INPUT -m multiport -p tcp -s suporte01h2.ddns.com.br --sport 873,5322,8090,8091,8092,8093 -j ACCEPT
        $SF -A OUTPUT -m multiport -p tcp -d suporte01h2.ddns.com.br --dport 873,5322,8090,8091,8092,8093 -j ACCEPT

        for IPSOURCE in `cat /opt/hsistema/regras/ip_ssh_ex`
           do
                enum=0
                while [ $enum -lt $QLINKEXT ]; do
                        enum=$(($enum+1))
                        DEV_EXT=`cat /opt/hsistema/config/cliente.txt | grep int_prov$enum | cut -d '=' -f2`
                        $SF -A INPUT -m multiport -p tcp -i $DEV_EXT -s $IPSOURCE --dport 873,5322,8090,8091,8092,8093 -j ACCEPT
                        $SF -A OUTPUT -m multiport -p tcp -o $DEV_EXT -d $IPSOURCE --sport 873,5322,8090,8091,8092,8093 -j ACCEPT
                        $SF -A INPUT -m multiport -p tcp -i $DEV_EXT -s $IPSOURCE --sport 873,5322,8090,8091,8092,8093 -j ACCEPT
                        $SF -A OUTPUT -m multiport -p tcp -o $DEV_EXT -d $IPSOURCE --dport 873,5322,8090,8091,8092,8093 -j ACCEPT
                done
           done
}

exec_serv_ip () {
        # 14 - servicos internos por ip

        echo -e "servicos internos por ip"
        for i in `cat /opt/hsistema/regras/libera_ip_fire_in`; do
                STATUS=`echo $i | cut -d ';' -f 2`
                IP_INTERNO=`echo $i | cut -d ';' -f 3`
                PROTOCOLO=`echo $i | cut -d ';' -f 4`
                ORIGEM=`echo $i | cut -d ';' -f 5`
                PORT=`echo $i | cut -d ';' -f 6`
                if [ $STATUS = "a" ]; then
                        $SF -A INPUT -p $PROTOCOLO -d $IP_INTERNO -s $ORIGEM --dport $PORT -j ACCEPT
                        $SF -A OUTPUT -p $PROTOCOLO -s $IP_INTERNO -d $ORIGEM --sport $PORT -j ACCEPT
                fi
        done
}

exec_serv_int () {
        # 15 - servicos internos por interface

        echo -e "servicos internos por interface"
        for i in `cat /opt/hsistema/regras/libera_sfire_in`; do
                STATUS=`echo $i | cut -d ';' -f 2`
                PROTOCOLO=`echo $i | cut -d ';' -f 3`
                ORIGEM=`echo $i | cut -d ';' -f 4`
                PORT=`echo $i | cut -d ';' -f 5`
                if [ $STATUS = "a" ]; then
                        for inum in $(seq -f '%02g' 1 $QLINKINT); do
                                DEV_INT=`cat /opt/hsistema/config/cliente.txt | grep int_interna$inum  | cut -d '=' -f2`
                                $SF -A INPUT -p $PROTOCOLO -i $DEV_INT -s $ORIGEM --dport $PORT -j ACCEPT
                                $SF -A OUTPUT -p $PROTOCOLO -o $DEV_INT -d $ORIGEM --sport $PORT -j ACCEPT
				$SF -A INPUT -p $PROTOCOLO -i $DEV_INT -d $ORIGEM --sport $PORT -j ACCEPT
                                $SF -A OUTPUT -p $PROTOCOLO -o $DEV_INT -s $ORIGEM --dport $PORT -j ACCEPT
                        done
                fi
           done
}

exec_serv_ext () {
        # 16 - servicos externos por interface

        echo -e "servicos externos por interface"
        for i in `cat /opt/hsistema/regras/libera_sfire_ex`; do
                STATUS=`echo $i | cut -d ';' -f 2`
                PROTOCOLO=`echo $i | cut -d ';' -f 3`
                ORIGEM=`echo $i | cut -d ';' -f 4`
                PORT=`echo $i | cut -d ';' -f 5`
                if [ $STATUS = "a" ]; then
                        enum=0
                        while [ $enum -lt $QLINKEXT ]; do
                                enum=$(($enum+1))
                                DEV_EXT=`cat /opt/hsistema/config/cliente.txt | grep int_prov$enum | cut -d '=' -f2`
                                $SF -A INPUT -p $PROTOCOLO -i $DEV_EXT -s $ORIGEM --dport $PORT -j ACCEPT
                                $SF -A OUTPUT -p $PROTOCOLO -o $DEV_EXT -d $ORIGEM --sport $PORT -j ACCEPT
                                $SF -A INPUT -p $PROTOCOLO -i $DEV_EXT -d $ORIGEM --sport $PORT -j ACCEPT
                                $SF -A OUTPUT -p $PROTOCOLO -o $DEV_EXT -s $ORIGEM --dport $PORT -j ACCEPT
                        done
                fi
           done
}

exec_sup_remoto () {
        # 17 - suporte remoto

        echo -e "suporte remoto"
        for inum in $(seq -f '%02g' 1 $QLINKINT); do
                DEV_INT=`cat /opt/hsistema/config/cliente.txt | grep int_interna$inum  | cut -d '=' -f2`
                NET_INT=`cat /opt/hsistema/config/$DEV_INT.conf | grep range | cut -d ' ' -f2`
                $SF -A FORWARD -p tcp -s $NET_INT -d $NET_EXT --dport 5500:5510 -j ACCEPT
                $SF -A FORWARD -p tcp -d $NET_INT -s $NET_EXT --sport 5500:5510 -mstate --state ESTABLISHED,RELATED -j ACCEPT
        done
}

exec_redirec_port () {
        # 18 - Redirecionamento de portas

        echo -e "redirecionamento 1"
        for i in `cat /opt/hsistema/regras/redirec_pri`; do
                STATUS=`echo $i | cut -d ';' -f 2`
                PROTOCOLO=`echo $i | cut -d ';' -f 3`
                IPORIGEM=`echo $i | cut -d ';' -f 4`
                PORTORI=`echo $i | cut -d ';' -f 5`
                IPDEST=`echo $i | cut -d ';' -f 6`
                PORTDEST=`echo $i | cut -d ';' -f 7`
                IPSERV=`echo $i | cut -d ';' -f 8`
                if [ $STATUS = "a" ]; then
                        if [ "" != "$IPSERV" ]; then
                                LKEXT="-d $IPSERV"
                        else
                                LKEXT=""
                        fi

                        if [ $PORTDEST = $PORTORI ]; then
                                $SF -A FORWARD -p $PROTOCOLO -s $NET_EXT -d $IPDEST --dport $PORTDEST -j ACCEPT
                                $SF -A FORWARD -p $PROTOCOLO -d $NET_EXT -s $IPDEST --sport $PORTDEST -j ACCEPT
                                enum=0
                                while [ $enum -lt $QLINKEXT ]; do
                                        enum=$(($enum+1))
                                        DEV_EXT=`cat /opt/hsistema/config/cliente.txt | grep int_prov$enum | cut -d '=' -f2`
                                        $SF -t nat -A PREROUTING -p $PROTOCOLO -s $IPORIGEM -i $DEV_EXT $LKEXT --dport $PORTDEST -j DNAT --to $IPDEST
                                done
                        else
                                $SF -A FORWARD -p $PROTOCOLO -s $NET_EXT -d $IPDEST --dport $PORTDEST -j ACCEPT
                                $SF -A FORWARD -p $PROTOCOLO -d $NET_EXT -s $IPDEST --sport $PORTDEST -j ACCEPT
                                enum=0
                                while [ $enum -lt $QLINKEXT ]; do
                                        enum=$(($enum+1))
                                        DEV_EXT=`cat /opt/hsistema/config/cliente.txt | grep int_prov$enum | cut -d '=' -f2`
                                        $SF -t nat -A PREROUTING -p $PROTOCOLO -m $PROTOCOLO -s $IPORIGEM -i $DEV_EXT $LKEXT --dport $PORTORI -j DNAT --to-destination $IPDEST:$PORTDEST
                                        $SF -t nat -A POSTROUTING -o $DEV_EXT -p $PROTOCOLO -m $PROTOCOLO -d $IPDEST --dport $PORTDEST -j MASQUERADE
                                done
                        fi
                fi
           done

        echo -e "redirecionamento 2"
        for i in `cat /opt/hsistema/regras/redirec_cli`; do
                STATUS=`echo $i | cut -d ';' -f 2`
                PROTOCOLO=`echo $i | cut -d ';' -f 3`
                IPORIGEM=`echo $i | cut -d ';' -f 4`
                PORTORI=`echo $i | cut -d ';' -f 5`
                IPDEST=`echo $i | cut -d ';' -f 6`
                PORTDEST=`echo $i | cut -d ';' -f 7`
                IPSERV=`echo $i | cut -d ';' -f 8`
                if [ $STATUS = "a" ]; then
                        if [ "" != "$IPSERV" ]; then
                                LKEXT="-d $IPSERV"
                        else
                                LKEXT=""
                        fi

                        if [ $PORTDEST = $PORTORI ]; then
                                $SF -A FORWARD -p $PROTOCOLO -s $NET_EXT -d $IPDEST --dport $PORTDEST -j ACCEPT
                                $SF -A FORWARD -p $PROTOCOLO -d $NET_EXT -s $IPDEST --sport $PORTDEST -j ACCEPT
                                enum=0
                                while [ $enum -lt $QLINKEXT ]; do
                                        enum=$(($enum+1))
                                        DEV_EXT=`cat /opt/hsistema/config/cliente.txt | grep int_prov$enum | cut -d '=' -f2`
                                        $SF -t nat -A PREROUTING -p $PROTOCOLO -s $IPORIGEM -i $DEV_EXT $LKEXT --dport $PORTDEST -j DNAT --to $IPDEST
                                done
                        else
                                $SF -A FORWARD -p tcp -s $NET_EXT -d $IPDEST --dport $PORTDEST -j ACCEPT
                                $SF -A FORWARD -p tcp -d $NET_EXT -s $IPDEST --sport $PORTDEST -j ACCEPT
                                enum=0
                                while [ $enum -lt $QLINKEXT ]; do
                                        enum=$(($enum+1))
                                        DEV_EXT=`cat /opt/hsistema/config/cliente.txt | grep int_prov$enum | cut -d '=' -f2`
                                        $SF -t nat -A PREROUTING -p $PROTOCOLO -m $PROTOCOLO -s $IPORIGEM -i $DEV_EXT $LKEXT --dport $PORTORI -j DNAT --to-destination $IPDEST:$PORTDEST
                                        $SF -t nat -A POSTROUTING -o $DEV_EXT -p $PROTOCOLO -m $PROTOCOLO -d $IPDEST --dport $PORTDEST -j MASQUERADE
                                done
                        fi
                fi
           done
}

exec_regra_lib_rede () {
        # 19 - liberar acesso definido pelo cliente

        echo -e "libera cliente"
        for i in `cat /opt/hsistema/regras/libera_rede`; do
                STATUS=`echo $i | cut -d ';' -f 2`
                PROTOCOLO=`echo $i | cut -d ';' -f 3`
                IPSOURCE=`echo $i | cut -d ';' -f 4`
                NET_DESTINO=`echo $i | cut -d ';' -f 5`
                PORT_DESTINO=`echo $i | cut -d ';' -f 6`
                if [ $STATUS = "a" ]; then
                  $SF -A FORWARD -m multiport -p $PROTOCOLO -s $IPSOURCE -d $NET_DESTINO --dport $PORT_DESTINO -j ACCEPT
                  $SF -A FORWARD -m multiport -p $PROTOCOLO -s $NET_DESTINO -d $IPSOURCE --sport $PORT_DESTINO -mstate --state ESTABLISHED,RELATED -j ACCEPT
                fi
          done
}

exec_regra_addmac  () {
        # 20 - ADDMAC
	
        echo "Libera navegacao"
        for inum in $(seq -f '%02g' 1 $QLINKINT); do
                DEV_INT=`cat /opt/hsistema/config/cliente.txt | grep int_interna$inum  | cut -d '=' -f2`
                NET_INT=`cat /opt/hsistema/config/$DEV_INT.conf | grep range | cut -d ' ' -f2`
                FIRELIVRE=`cat /opt/hsistema/config/cliente.txt | grep lvr_fire$inum | cut -d '=' -f2`
		[ $FIRELIVRE = "ativo" ] && $SF -A FORWARD -s $NET_INT -d $NET_EXT -j ACCEPT 
                [ $FIRELIVRE = "ativo" ] && $SF -A FORWARD -d $NET_INT -mstate --state ESTABLISHED,RELATED -j ACCEPT
        done


        for i in `cat /opt/hsistema/regras/addmac`; do
                STATUS=`echo $i | cut -d ';' -f 17`
                IPSOURCE=`echo $i | cut -d ';' -f 28`
                MACSOURCE=`echo $i | cut -d ';' -f 27`
                exec_mac
                if [ $STATUS = "a" ]; then
                  $SF -A FORWARD -s $IPSOURCE $CMACADD -d $NET_EXT -j ACCEPT
                  $SF -A FORWARD -d $IPSOURCE -mstate --state ESTABLISHED,RELATED -j ACCEPT
                fi
           done

        ## liberar acesso ao WWW
        echo -e "Libera acesso a sites"

        for i in `cat /opt/hsistema/regras/addmac`; do
                STATUS=`echo $i | cut -d ';' -f 18`
                IPSOURCE=`echo $i | cut -d ';' -f 28`
                MACSOURCE=`echo $i | cut -d ';' -f 27`
                exec_mac
                if [ $STATUS = "n" ]; then
                  $SF -A FORWARD -m multiport -p tcp -s $IPSOURCE $CMACADD -d $NET_EXT  --dport 80,443,809,8080 -j ACCEPT
                  $SF -A FORWARD -m multiport -p tcp -d $IPSOURCE -s $NET_EXT --sport 80,443,809,8080 -mstate --state ESTABLISHED,RELATED -j ACCEPT
                fi
           done

        ## liberar acesso ao Email
        echo -e "Libera Email"

        ## Libera com pop3scan

        #$SF -A FORWARD -p tcp -s $NET_INT1 -d $NET_EXT --dport 25 -j ACCEPT
        #$SF -A FORWARD -p tcp -d $NET_INT1 -s $NET_EXT --sport 25 -mstate --state ESTABLISHED,RELATED -j ACCEPT
        #$SF -A INPUT -m multiport -p tcp -d $INT1 -s $NET_INT1 --dport 110,995,8110 -j ACCEPT
        #$SF -A OUTPUT -m multiport -p tcp -s $INT1 -d $NET_INT1 --sport 110,995,8110 -j ACCEPT

        for i in `cat /opt/hsistema/regras/addmac`; do
                STATUS=`echo $i | cut -d ';' -f 19`
                IPSOURCE=`echo $i | cut -d ';' -f 28`
                MACSOURCE=`echo $i | cut -d ';' -f 27`
                exec_mac
                if [ $STATUS = "e" ]; then
                  #sem pop3scan
                  $SF -A FORWARD -m multiport -p tcp -s $IPSOURCE $CMACADD -d $NET_EXT  --dport 25,465,587,110,995 -j ACCEPT
                  $SF -A FORWARD -m multiport -p tcp -d $IPSOURCE -s $NET_EXT --sport 25,465,587,110,995 -mstate --state ESTABLISHED,RELATED -j ACCEPT
                  #com pop3scan
                  #$SF -A FORWARD -p tcp -s $IPSOURCE $CMACADD -d $NET_EXT  --dport 25,465 -j ACCEPT
                  #$SF -A FORWARD -p tcp -d $IPSOURCE -s $NET_EXT --sport 25 -mstate --state ESTABLISHED,RELATED -j ACCEPT
                  #$SF -A INPUT -m multiport -p tcp -d $INT1 -s $IPSOURCE $CMACADD --dport 110,995,8110 -j ACCEPT
                  #$SF -A OUTPUT -m multiport -p tcp -s $INT1 -d $IPSOURCE --sport 110,995,8110 -j ACCEPT
                fi
          done

        ## liberar acesso ao proxy
        echo -e "Libera 3128"

        for i in `cat /opt/hsistema/regras/addmac`; do
                STATUS=`echo $i | cut -d ';' -f 20`
                IPSOURCE=`echo $i | cut -d ';' -f 28`
                MACSOURCE=`echo $i | cut -d ';' -f 27`
                exec_mac
                if [ $STATUS = "p" ]; then
                        for inum in $(seq -f '%02g' 1 $QLINKINT); do
                                DEV_INT=`cat /opt/hsistema/config/cliente.txt | grep int_interna$inum  | cut -d '=' -f2`
                                $SF -A INPUT -p tcp -i $DEV_INT -s $IPSOURCE $CMACADD --dport 3128 -j ACCEPT
                                $SF -A OUTPUT -p tcp -o $DEV_INT -d $IPSOURCE --sport 3128 -j ACCEPT
                        done
                fi
          done

        ## liberar acesso ao ts
        echo -e "Libera TS"
        for i in `cat /opt/hsistema/regras/addmac`; do
                STATUS=`echo $i | cut -d ';' -f 21`
                IPSOURCE=`echo $i | cut -d ';' -f 28`
                MACSOURCE=`echo $i | cut -d ';' -f 27`
                exec_mac
                if [ $STATUS = "t" ]; then
                  $SF -A FORWARD -m multiport -p tcp -s $IPSOURCE $CMACADD -d $NET_EXT  --dport 3389,5900 -j ACCEPT
                  $SF -A FORWARD -m multiport -p tcp -d $IPSOURCE -s $NET_EXT --sport 3389,5900 -mstate --state ESTABLISHED,RELATED -j ACCEPT
                fi
          done

        ## liberar acesso ftp
        echo -e "Libera FTP"
        for i in `cat /opt/hsistema/regras/addmac`; do
                STATUS=`echo $i | cut -d ';' -f 22`
                IPSOURCE=`echo $i | cut -d ';' -f 28`
                MACSOURCE=`echo $i | cut -d ';' -f 27`
                exec_mac
                if [ $STATUS = "f" ]; then
                  $SF -A FORWARD -m multiport -p tcp -s $IPSOURCE $CMACADD -d $NET_EXT  --dport 20,21,10060 -j ACCEPT
                  $SF -A FORWARD -m multiport -p tcp -s $NET_EXT -d $IPSOURCE --sport 20,21,10060 -mstate --state ESTABLISHED,RELATED -j ACCEPT
                  $SF -A FORWARD -m multiport -p udp -s $IPSOURCE -d $NET_EXT  --dport 20,21 -j ACCEPT
                  $SF -A FORWARD -m multiport -p udp -s $NET_EXT -d $IPSOURCE --sport 20,21 -mstate --state ESTABLISHED,RELATED -j ACCEPT
                fi
          done

        ## liberar acesso msn
        echo -e "Libera MSN"
        for i in `cat /opt/hsistema/regras/addmac`; do
                STATUS=`echo $i | cut -d ';' -f 23`
                IPSOURCE=`echo $i | cut -d ';' -f 28`
                MACSOURCE=`echo $i | cut -d ';' -f 27`
                exec_mac
                if [ $STATUS = "m" ]; then
                  $SF -A FORWARD -m multiport -p tcp -s $IPSOURCE $CMACADD -d $NET_EXT  --dport 1863,6901,5190,4000 -j ACCEPT
                  $SF -A FORWARD -m multiport -p tcp -s $NET_EXT -d $IPSOURCE --sport 1863,6891,5190,4000 -mstate --state ESTABLISHED,RELATED -j ACCEPT
                  $SF -A FORWARD -m multiport -p udp -s $IPSOURCE -d $NET_EXT  --dport 1863,6901,5190,4000 -j ACCEPT
                  $SF -A FORWARD -m multiport -p udp -s $NET_EXT -d $IPSOURCE --sport 1863,6901,5190,4000 -mstate --state ESTABLISHED,RELATED -j ACCEPT
                fi
          done

        # liberar acesso ao recnet
        echo -e "Libera Recnet"
        for i in `cat /opt/hsistema/regras/addmac`; do
                STATUS=`echo $i | cut -d ';' -f 24`
                IPSOURCE=`echo $i | cut -d ';' -f 28`
                MACSOURCE=`echo $i | cut -d ';' -f 27`
                exec_mac
                if [ $STATUS = "s" ]; then
                  $SF -A FORWARD -m multiport -p tcp -s $IPSOURCE $CMACADD -d $NET_EXT  --dport 1157,3456 -j ACCEPT
                  $SF -A FORWARD -m multiport -p tcp -d $IPSOURCE -s $NET_EXT --sport 1157,3456 -mstate --state ESTABLISHED,RELATED -j ACCEPT
                fi
          done
}

exec_nat () {
        # 21 - nat

        echo -e "NAT"
        for inum in $(seq -f '%02g' 1 $QLINKINT); do
                DEV_INT=`cat /opt/hsistema/config/cliente.txt | grep int_interna$inum  | cut -d '=' -f2`
                NET_INT=`cat /opt/hsistema/config/$DEV_INT.conf | grep range | cut -d ' ' -f2`
                enum=0
                while [ $enum -lt $QLINKEXT ]; do
                        enum=$(($enum+1))
                        DEV_EXT=`cat /opt/hsistema/config/cliente.txt | grep int_prov$enum | cut -d '=' -f2`
                        $SF -t nat -A POSTROUTING -s $NET_INT -o $DEV_EXT -j MASQUERADE
                done
        done
}




#------------------------------------------------- iniciando firewall -----------------------------------------------------------

exec_completo () {

        # 1 - limpando regras
        exec_limpa_regras

        # politicas
        echo -e "fechando"
        $SF -P INPUT DROP
        $SF -P OUTPUT DROP
        $SF -P FORWARD DROP

        # 2 - modulos
        exec_ambiente

        # 3 - Criando uma chain para "bad tcp packets"
        exec_bad_packets

        # LoopBack
        echo -e "loopback"
        $SF -A INPUT -i lo -j ACCEPT
        $SF -A OUTPUT -o lo -j ACCEPT

        # 4 - Lista Negra
        exec_lista_negra

        # 6 - regra basica
        exec_regra_basica
	
	# 10 - Bloqueia Forward Interno
        #exec_bloq_fwd_interno

        # 25 - liberar acesso ao DNS interno
        exec_dns_interno

        # 24 - liberar acesso ao DNS externos para rede
        exec_dns_rede

        # 9 - libera rede interna
        exec_rede_interna
	
        # 11 - libera FORWARD entre redes internas
        exec_lib_fwd_interno

	# 12 - liberando ping
        exec_ping

        # 5 - Libera teste link
        exec_lib_teste

        # 13 - Libera acesso ao ssh no firewall
        exec_suporte

        # 15 - servicos internos por ip
        exec_serv_ip

        # 23 - servicos internos por interface
        exec_serv_int

        # 16 - servicos externos por interface
        exec_serv_ext

        # 17 - suporte remoto
        exec_sup_remoto

        # 18 - Redirecionamento de portas
        exec_redirec_port

        # 20 - liberar acesso definido pelo cliente
        exec_regra_lib_rede

        # 21 - liberar acesso definido pelo cliente
        exec_regra_addmac

        # 22 - nat
        exec_nat
}

exec_basico () {

        # 1 - limpando regras
        exec_limpa_regras
        
	# politicas
        echo -e "fechando"
        $SF -P INPUT DROP
        $SF -P OUTPUT DROP
	$SF -P FORWARD ACCEPT
	
	# 2 - modulos
        exec_ambiente

        # 3 - Criando uma chain para "bad tcp packets"
        exec_bad_packets

        # LoopBack
        echo -e "loopback"
        $SF -A INPUT -i lo -j ACCEPT
        $SF -A OUTPUT -o lo -j ACCEPT

        # 4 - Lista Negra
        exec_lista_negra

        # 6 - regra basica
        exec_regra_basica
	
	# 10 - Bloqueia Forward Interno
        exec_bloq_fwd_interno
	
        # 9 - libera rede interna
        exec_rede_interna
	
	# 11 - libera FORWARD entre redes internas
        exec_lib_fwd_interno

        # 12 - liberando ping
        exec_ping
        
	# 5 - Libera teste link
        exec_lib_teste

        # 13 - Libera acesso ao ssh no firewall
        exec_suporte
	
	exec_serv_ip

        # 23 - servicos internos por interface
        exec_serv_int

        # 16 - servicos externos por interface
        exec_serv_ext

        # 17 - suporte remoto
        exec_sup_remoto

        # 18 - Redirecionamento de portas
        exec_redirec_port

        # 21 - nat
        exec_nat
}

exec_limpa () {

        # 1 - limpando regras
        exec_limpa_regras

        # 2 - politicas
        echo -e "liberando"
        $SF -P INPUT ACCEPT
        $SF -P OUTPUT ACCEPT
        $SF -P FORWARD ACCEPT

        # 3 - modulos
        exec_ambiente

        # 5 - LoopBack
        echo -e "loopback"
        $SF -A INPUT -i lo -j ACCEPT
        $SF -A OUTPUT -o lo -j ACCEPT

        # 18 - Redirecionamento de portas
        exec_redirec_port

        # 21 - nat
        exec_nat
}

exec_stop () {

        ## limpando regras
        exec_limpa_regras

        ## politicas
        echo -e "fechando"
        $SF -P INPUT DROP
        $SF -P OUTPUT DROP
        $SF -P FORWARD DROP

        # modulos
        exec_ambiente

        # Criando uma chain para "bad tcp packets"
        exec_bad_packets

        ## LoopBack
        echo -e "loopback"
        $SF -A INPUT -i lo -j ACCEPT
        $SF -A OUTPUT -o lo -j ACCEPT

        # Lista Negra
        exec_lista_negra

        # regra basica
        exec_regra_basica

        ## liberar acesso ao DNS interno
        exec_dns_interno
	
	# 10 - Bloqueia Forward Interno
        exec_bloq_fwd_interno

        ## libera da rede interna
        exec_rede_interna
	
	# 11 - libera FORWARD entre redes internas
        exec_lib_fwd_interno

        ## liberando ping
        exec_ping

        # Libera teste link
        exec_lib_teste

        # Libera acesso ao ssh no firewall
        exec_suporte
	
}

case "$1" in
    start) exec_padrao ;;
    completo) exec_completo ;;
    basico) exec_basico ;;
    limpa) exec_limpa ;;
    stop) exec_stop ;;
    status) iptables-save ;;
    *) exec_padrao ;;
esac


DIAHORA=`date +%d"/"%m"/"%Y"-"%H":"%M":"%S`
echo -e "\n$DIAHORA - Firewall Reiniciado \n"

exit 0

