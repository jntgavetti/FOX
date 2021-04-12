#!/bin/bash

export LANG=pt_BR.UTF-8
ONTEM=`date +%Y"-"%m"-"%d -d "1 days ago"`
ONTEMSG=`date +%d"/"%m"/"%Y -d "1 days ago"`
DIA=`date +%Y"-"%m"-"%d`
DIAHORA=`date +%d"/"%m"/"%Y"-"%H":"%M":"%S`
SARG_B=`cat /opt/hsistema/config/cliente.txt | grep sarg_backup | cut -d '=' -f2`
SARG_R=`cat /opt/hsistema/config/cliente.txt | grep sarg_relatorio | cut -d '=' -f2`

exec 1>> /opt/hsistema/log/$ONTEM-sarg.log 2>&1

/bin/echo -e "\n$DIAHORA - Rotina Diaria do Sarg \n"

/bin/echo -e "\n\t# Copia dos arquivos #\n"
cp /var/log/squid3/access.log /var/log/squid3/backup/access.log
cp /var/log/squid3/cache.log /var/log/squid3/backup/cache.log
cp /var/log/squid3/store.log /var/log/squid3/backup/store.log
cp /var/log/squid3/squidGuard.log /var/log/squid3/backup/squidGuard.log

/bin/echo -e "\n\t# Limpando Log #\n"
echo "" > /var/log/squid3/access.log
echo "" > /var/log/squid3/cache.log
echo "" > /var/log/squid3/store.log
echo "" > /var/log/squid3/squidGuard.log

/bin/echo -e "\n\t# Limpa arquivos antigos #\n"
/usr/bin/find /var/log/squid3/backup/ -maxdepth 1 -mtime +"$SARG_B" -exec rm -r {} \;
/usr/bin/find /var/www/html/squid-reports/ -maxdepth 1 -mtime +"SARG_R" -exec rm -r {} \;

/bin/echo -e "\n\t# Gerando Relatorio #\n"
/usr/bin/sarg -f /etc/sarg/sarg.conf

/bin/echo -e "\n\t# Movendo para backup #\n"
mkdir -p /var/log/squid3/backup/"$ONTEM"
mv /var/log/squid3/backup/access.log /var/log/squid3/backup/"$ONTEM"/access.log
mv /var/log/squid3/backup/cache.log /var/log/squid3/backup/"$ONTEM"/cache.log
mv /var/log/squid3/backup/store.log /var/log/squid3/backup/"$ONTEM"/store.log
mv /var/log/squid3/backup/squidGuard.log /var/log/squid3/"$ONTEM"/squidGuard.log

DIAHORA=`date +%d"/"%m"/"%Y"-"%H":"%M":"%S`
/bin/echo -e "\n$DIAHORA - Fim da Rotina \n"

