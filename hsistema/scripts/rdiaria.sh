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

NTP_SRV=`cat /opt/hsistema/config/cliente.txt | grep ntp_srv | cut -d '=' -f 2`
H_PSBLOCK=`cat /opt/hsistema/config/cliente.txt | grep h_psblock | cut -d '=' -f 2`
H_BAK=`cat /opt/hsistema/config/cliente.txt | grep hbackup_diario | cut -d '=' -f 2`
H_LOG=`cat /opt/hsistema/config/cliente.txt | grep hsistema_log | cut -d '=' -f 2`

exec 1>> /opt/hsistema/log/$ONTEM-rdiaria.log 2>&1

echo -e "\n$DIAHORA - Rotina Diaria \n"

echo -e "\n\t# Atualiza o relogio #\n"
date
ntpdate $NTP_SRV
date

echo -e "\n\t# Verificar espacos no HD #\n"
df -h

echo -e "\n\t# Backup Dairio #\n"

tar -cjpf /opt/hbackup/diario/"$ONTEM"-fire.tar.gz /opt/hsistema/config /opt/hsistema/regras  /opt/hsistema/scripts  /opt/hsistema/senhas /opt/hsistema/links
tar -cjpf /opt/hbackup/diario/"$ONTEM"-www.tar.gz  /var/www/html/suporte /var/www/html/_js
tar -cjpf /opt/hbackup/diario/"$ONTEM"-wwws.tar.gz /var/wwws/html/acessos /var/wwws/html/ctr_user /var/wwws/html/ctr_adm /var/wwws/html/_js

echo -e "\n\t# Limpando dados antigos \n"
find /opt/hbackup/diario/ -maxdepth 1 -mtime +$H_BAK -exec rm -r {} \;
find /opt/hsistema/log/ -maxdepth 1 -mtime +$H_LOG -exec rm -r {} \;
rm /root/+Sent

MES=`date +"%b" -d "$H_PSBLOCK"`
sed -i.old /$MES/d  /opt/hsistema/regras/psblock

sh /opt/hsistema/scripts/firewall.sh start

DIAHORA=`date +%d"/"%m"/"%Y"-"%H":"%M":"%S`
echo -e "\n$DIAHORA - Fim da Rotina Diaria \n"
