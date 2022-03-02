#!/bin/bash
PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"
export LANG=pt_BR.UTF-8
ONTEM=`date +%Y"-"%m"-"%d -d "1 days ago"`
DIA=`date +%Y"-"%m"-"%d`
DIAHORA=`date +%d"/"%m"/"%Y"-"%H":"%M":"%S`
SF="/sbin/iptables"
SFS="/sbin/iptables-save"
QLINKEXT=`cat /opt/hsistema/config/cliente.txt | grep links_externos  | cut -d '=' -f2`

check=`$SFS | grep :INPUT | grep DROP`
        if [ -z "$check" ]; then
          FIREATIVA=inativo
        else
          FIREATIVA=ativo
        fi

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
            exec 1>> /opt/hsistema/log/$DIA-atualizadns.log 2>&1
        fi
fi

echo -e "\n\t$DIAHORA - Atualizar DNS \n"

exec_lib_teste () {
	enum=0
	while [ $enum -lt $QLINKEXT ]; do
	        enum=$(($enum+1))
	        DEV_EXT=`cat /opt/hsistema/config/cliente.txt | grep int_prov$enum | cut -d '=' -f2`
             	$SF -A INPUT -s $IP2 -p icmp -i $DEV_EXT -j ACCEPT
             	$SF -A OUTPUT -d $IP2 -p icmp -o $DEV_EXT -j ACCEPT
             	$SF -A INPUT -p tcp -m multiport -i $DEV_EXT -s $IP2 --sport 80,443,587 -j ACCEPT
             	$SF -A OUTPUT -p tcp -m multiport -o $DEV_EXT -d $IP2 --dport 80,443,587 -j ACCEPT
	done
}

HOSTSUP=`cat /etc/hosts | grep suporte.h2info | cut -d ' ' -f1`
supexte=`iptables-save | grep $HOSTSUP/32 | grep 5322`
if [ -z "$supexte" ]; then
        echo liberando suporte.h2info.com.br
        $SF -A INPUT -m multiport -p tcp -s suporte.h2info.com.br --dport 5322,8090,8091,8092,8093 -j ACCEPT
        $SF -A OUTPUT -m multiport -p tcp -d suporte.h2info.com.br --sport 5322,8090,8091,8092,8093 -j ACCEPT
        $SF -A INPUT -m multiport -p tcp -s suporte.h2info.com.br --sport 5322,8090,8091,8092,8093 -j ACCEPT
        $SF -A OUTPUT -m multiport -p tcp -d suporte.h2info.com.br --dport 5322,8090,8091,8092,8093 -j ACCEPT
fi

HOSTSUP=`cat /etc/hosts | grep suporte01h2.dd | cut -d ' ' -f1`
supexte=`iptables-save | grep $HOSTSUP/32 | grep 5322`
if [ -z "$supexte" ]; then
        echo liberando suporte01h2.ddns.com.br
        $SF -A INPUT -m multiport -p tcp -s suporte01h2.ddns.com.br --dport 5322,8090,8091,8092,8093 -j ACCEPT
        $SF -A OUTPUT -m multiport -p tcp -d suporte01h2.ddns.com.br --sport 5322,8090,8091,8092,8093 -j ACCEPT
        $SF -A INPUT -m multiport -p tcp -s suporte01h2.ddns.com.br --sport 5322,8090,8091,8092,8093 -j ACCEPT
        $SF -A OUTPUT -m multiport -p tcp -d suporte01h2.ddns.com.br --dport 5322,8090,8091,8092,8093 -j ACCEPT
fi

rm /opt/hsistema/regras/lista_dns

QDNS=`cat /opt/hsistema/config/cliente.txt | grep quant_dns | cut -d '=' -f2`
QDNS=$(($QDNS+10))

  num=10
  while true ; do
        num=$(($num+1))
        hostn=host_$num
        host=`cat /opt/hsistema/config/cliente.txt | grep $hostn | cut -d ' ' -f 2`
        sed -i.old /$host/d  /etc/hosts
        IP1=`cat /opt/hsistema/config/cliente.txt | grep $hostn | cut -d ' ' -f 3`
        IP2=`ping -q -n -w 1 -W 1 $host | grep "PING" | cut -d  '(' -f 2 | cut -d  ')' -f 1`
        echo -e $hostn
        echo -e $host
        echo -e $IP1
        echo -e $IP2
        if [ "" != "$IP2" ]
                then
                 if [ "$IP1" != "$IP2" ]
                     then
                     sed -i "s/$hostn.*/$hostn $host $IP2/g" /opt/hsistema/config/cliente.txt
                     echo $IP2 $host >> /etc/hosts
                     echo $IP2 >> /opt/hsistema/regras/lista_dns
                     [ $FIREATIVA = "ativo" ] && exec_lib_teste
                     else
                     echo $IP1 $host >> /etc/hosts
                     echo $IP1 >> /opt/hsistema/regras/lista_dns
                 fi
                else
                     echo $IP1 $host >> /etc/hosts
                     echo $IP1 >> /opt/hsistema/regras/lista_dns
        fi
    if [ $num = $QDNS ]; then
        DIAHORA=`date +%d"/"%m"/"%Y"-"%H":"%M":"%S`
        echo -e "\n\t$DIAHORA - Fim da Rotina Atualizar DNS \n"
        exit 1
  fi
done
