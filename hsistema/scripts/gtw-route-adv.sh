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
        export W"$enum"GATEWAY=`cat /opt/hsistema/links/w"$enum"Gateway`
        export W"$enum"STATUS=`cat /opt/hsistema/links/w"$enum"status`
done

ip rule flush
ip rule add from all lookup main pref 32766
ip rule add from all lookup default pref 32767

verificaStatus(){
	statusDestino="erro"
	statusDestinoBkp="erro"
	if [ $1 == "wan1" ]; then
		statusDestino=$W1STATUS
	elif [ $1 == "wan2" ]; then
                statusDestino=$W2STATUS
	elif [ $1 == "wan3" ]; then
                statusDestino=$W3STATUS
	elif [ $1 == "wan4" ]; then
                statusDestino=$W4STATUS

	fi	

	if [ $2 == "wan1" ]; then
                statusDestinoBkp=$W1STATUS
        elif [ $2 == "wan2" ]; then
                statusDestinoBkp=$W2STATUS
        elif [ $2 == "wan3" ]; then
                statusDestinoBkp=$W3STATUS
        elif [ $2 == "wan4" ]; then
                statusDestinoBkp=$W4STATUS

        fi

}


aplicaRota(){
	ip rule add from $1 table $2
	echo "Aplica rota"
}

file="/opt/hsistema/regras/gtw-route-adv"    
cat $file | while read linha 
do
	arr=(${linha//;/ })                # split por ";"

	if [ ${arr[0]} == "ativo" ]; then
		origem=${arr[1]}
		destino=${arr[2]}
		destinoBkp=${arr[3]}
		descricao=${arr[4]}

		verificaStatus $destino $destinoBkp

		echo "status: $statusDestino $statusDestinoBkp , Origem: $origem , Destino: $destino "	

		if [ $statusDestino == "up"  ]; then
			echo "Chama funcao aplica rota"
			aplicaRota $origem $destino
		elif [ $statusDestinoBkp == "up" ]; then
			aplicaRota $origem $destinoBkp
		else
			echo "links off"
		fi
	else
		echo "comentatio"
	fi


done
