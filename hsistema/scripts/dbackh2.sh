#!/bin/bash
PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"
export LANG=pt_BR.UTF-8
ONTEM=`date +%Y"-"%m"-"%d -d "1 days ago"`
DIA=`date +%Y"-"%m"-"%d`
DIAHORA=`date +%d"/"%m"/"%Y"-"%H":"%M":"%S`
BDIA=`date +%d%H`
SERVHOST=`cat /etc/hostname`
CAMINHO_BACKUP="/home/admbackup/firewalls/$SERVHOST/$DIA"
PORTA="`cat /usr/local/bin/.dados_backup | cut -d ',' -f3`"
USER="`cat /usr/local/bin/.dados_backup | cut -d ',' -f1`"
SENHA="`cat /usr/local/bin/.dados_backup | cut -d ',' -f2`"
RS="/usr/bin/rsync -aruvz --copy-links  --exclude squid-reports --exclude squid-prov --exclude log"

exec 1>> /opt/hsistema/log/$DIA-backuph2.log 2>&1

echo -e "\n\t$DIAHORA - inicio \n"

 sshpass -p $SENHA $RS -avz --rsync-path="mkdir -p $CAMINHO_BACKUP && $RS" -e "ssh -o 'StrictHostKeyChecking=no' -p $PORTA -l$USER" /opt/hsistema suporteh2.ddns.com.br:$CAMINHO_BACKUP/

 sshpass -p $SENHA $RS -avz --rsync-path="mkdir -p $CAMINHO_BACKUP && $RS" -e "ssh -p $PORTA -l$USER" /var/www suporteh2.ddns.com.br:$CAMINHO_BACKUP/
 sshpass -p $SENHA $RS -avz --rsync-path="mkdir -p $CAMINHO_BACKUP && $RS" -e "ssh -p $PORTA -l$USER" /var/wwws suporteh2.ddns.com.br:$CAMINHO_BACKUP/

 sshpass -p $SENHA $RS -avz --rsync-path="mkdir -p $CAMINHO_BACKUP && $RS" -e "ssh -p $PORTA -l$USER" /etc/openvpn suporteh2.ddns.com.br:$CAMINHO_BACKUP/

 sshpass -p $SENHA $RS -avz --rsync-path="mkdir -p $CAMINHO_BACKUP && $RS" -e "ssh -p $PORTA -l$USER" /etc/softether/vpnclient/vpn_client.config suporteh2.ddns.com.br:$CAMINHO_BACKUP/softether/
 sshpass -p $SENHA $RS -avz --rsync-path="mkdir -p $CAMINHO_BACKUP && $RS" -e "ssh -p $PORTA -l$USER" /etc/softether/vpnserver/vpn_server.config suporteh2.ddns.com.br:$CAMINHO_BACKUP/softether/
 sshpass -p $SENHA $RS -avz --rsync-path="mkdir -p $CAMINHO_BACKUP && $RS" -e "ssh -p $PORTA -l$USER" /etc/softether/vpnbridge/vpn_bridge.config suporteh2.ddns.com.br:$CAMINHO_BACKUP/softether/
 sshpass -p $SENHA $RS -avz --rsync-path="mkdir -p $CAMINHO_BACKUP && $RS" -e "ssh -p $PORTA -l$USER" /etc/softether/vpnserver/adminip.txt suporteh2.ddns.com.br:$CAMINHO_BACKUP/softether/

 sshpass -p $SENHA $RS -avz --rsync-path="mkdir -p $CAMINHO_BACKUP/ipsec && $RS" -e "ssh -p $PORTA -l$USER" /etc/ipsec.conf suporteh2.ddns.com.br:$CAMINHO_BACKUP/ipsec/
 sshpass -p $SENHA $RS -avz --rsync-path="mkdir -p $CAMINHO_BACKUP/ipsec && $RS" -e "ssh -p $PORTA -l$USER" /etc/ipsec.secrets suporteh2.ddns.com.br:$CAMINHO_BACKUP/ipsec/
 sshpass -p $SENHA $RS -avz --rsync-path="mkdir -p $CAMINHO_BACKUP/ipsec && $RS" -e "ssh -p $PORTA -l$USER" /etc/xl2tpd/xl2tpd.conf suporteh2.ddns.com.br:$CAMINHO_BACKUP/ipsec/
 sshpass -p $SENHA $RS -avz --rsync-path="mkdir -p $CAMINHO_BACKUP/ipsec && $RS" -e "ssh -p $PORTA -l$USER" /etc/ppp/options.xl2tpd suporteh2.ddns.com.br:$CAMINHO_BACKUP/ipsec/
 sshpass -p $SENHA $RS -avz --rsync-path="mkdir -p $CAMINHO_BACKUP/ipsec && $RS" -e "ssh -p $PORTA -l$USER" /etc/ppp/chap-secrets suporteh2.ddns.com.br:$CAMINHO_BACKUP/ipsec/

DIAHORA=`date +%d"/"%m"/"%Y"-"%H":"%M":"%S`
echo -e "\n\t$DIAHORA - Fim  \n"

