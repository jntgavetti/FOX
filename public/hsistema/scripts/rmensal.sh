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

HBACK=`cat /opt/hsistema/config/cliente.txt | grep hbackup_mensal | cut -d '=' -f 2`

exec 1>> /opt/hsistema/log/$ONTEM-rmensal.log 2>&1

echo -e "\n$DIAHORA - Rotina Mensal \n"

echo -e "\n\t# Verificar espacos no HD #\n"
df -h

echo -e "\n\t# Backup Dairio #\n"

tar -cjpf /opt/hbackup/mensal/"$ONTEM"-fire.tar.gz /opt/hsistema/config /opt/hsistema/regras  /opt/hsistema/scripts  /opt/hsistema/senhas /opt/hsistema/links
tar -cjpf /opt/hbackup/mensal/"$ONTEM"-portsentry.history.tar.gz /etc/portsentry/portsentry.history /etc/portsentry/portsentry.blocked.tcp /etc/portsentry/portsentry.blocked.udp
tar -cjpf /opt/hbackup/mensal/"$ONTEM"-www.tar.gz  /var/www/suporte /var/www/_js
tar -cjpf /opt/hbackup/mensal/"$ONTEM"-wwws.tar.gz /var/wwws/html/acessos /var/wwws/html/ctr_user /var/wwws/html/ctr_adm /var/wwws/html/_js

echo "" > /etc/portsentry/portsentry.history
echo "" > /etc/portsentry/portsentry.blocked.tcp
echo "" > /etc/portsentry/portsentry.blocked.udp

echo -e "\n\t# Limpando dados antigos \n"
find /opt/hbackup/mensal/ -maxdepth 1 -mtime +$HBACK  -exec rm -r {} \;
rm /root/+Sent

DIAHORA=`date +%d"/"%m"/"%Y"-"%H":"%M":"%S`
echo -e "\n$DIAHORA - Fim da Rotina Mensal \n"
