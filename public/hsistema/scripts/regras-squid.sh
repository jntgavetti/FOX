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

exec 1> /opt/hsistema/log/squid.log 2>&1

echo -e "\n$DIAHORA - Reinicio do Squid  \n"

 /usr/sbin/squid3 -N -k reconfigure
if [ $? -eq 1 ]; then
  DIAHORA=`date +%d"/"%m"/"%Y"-"%H":"%M":"%S`
  echo -e "\n$DIAHORA - Erro no Squid \n"
else
  DIAHORA=`date +%d"/"%m"/"%Y"-"%H":"%M":"%S`
  echo -e "\n$DIAHORA - Squid Reiniciado \n"
fi

