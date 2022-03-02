#!/bin/bash
PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"
export LANG=pt_BR.UTF-8
ONTEM=`date +%Y"-"%m"-"%d -d "1 days ago"`
DIA=`date +%Y"-"%m"-"%d`
DIAHORA=`date +%d"-"%m"-"%Y"-"%H":"%M":"%S`

versao(){
        echo "   Rotas - Versão Stable - HSistema-v1.1 - Jessie"
        DIAHORA=`date +%d"/"%m"/"%Y"-"%H":"%M":"%S`
        echo "  -Verificação de versão" >> /var/opt/log/$DIA-trocalink.log
        echo "######## $DIAHORA - Final #########" >> /var/opt/log/$DIA-trocalink.log
        echo " " >> /var/opt/log/$DIA-trocalink.log
        exit
}


echo "######## $DIAHORA - Inicio - Rotas - Hsistema Stable v1.1 #########"
echo " " >> /var/opt/log/$DIA-trocalink.log
echo "######## $DIAHORA - Inicio - Rotas - Hsistema Stable v1.1 #########" >> /var/opt/log/$DIA-rotas.log

QUANTIDADE=`cat /opt/hsistema/links/wanQuantidade`
enum=0
while [ $enum -lt $QUANTIDADE ]; do
        enum=$(($enum+1))
        export Link"$enum"GATEWAY=`cat /opt/hsistema/links/w"$enum"Gateway`
        export Link"$enum"STATUS=`cat /opt/hsistema/links/w"$enum"status`
done

