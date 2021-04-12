#!/bin/bash
PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"
export LANG=pt_BR.UTF-8
ONTEM=`date +%Y"-"%m"-"%d -d "1 days ago"`
DIA=`date +%Y"-"%m"-"%d`
DIAHORA=`date +%d"-"%m"-"%Y"-"%H":"%M":"%S`

versao(){
        echo "   Versão Stable - HSistema-v1.1 - Jessie"
	DIAHORA=`date +%d"/"%m"/"%Y"-"%H":"%M":"%S`
	echo "	-Verificação de versão" >> /var/opt/log/$DIA-trocalink.log
	echo "######## $DIAHORA - Final #########" >> /var/opt/log/$DIA-trocalink.log
	echo " " >> /var/opt/log/$DIA-trocalink.log
	exit
}


echo "######## $DIAHORA - Inicio - Hsistema Stable v1.1 #########" 
echo " " >> /var/opt/log/$DIA-trocalink.log
echo "######## $DIAHORA - Inicio - Hsistema Stable v1.1 #########" >> /var/opt/log/$DIA-trocalink.log

QUANTIDADE=`cat /opt/hsistema/links/wanQuantidade`
GWATUAL=`ip route show | grep ^default | head -n 1 | cut -d " " -f 3`

#Verifica qual a prioridade do link "Principal, Backup, Backup2 ou Backup 3"
verificaPrioridade(){
	if [ $1 == "principal" ]; then
		echo "$2 é principal"
		WPrincipalDispositivo=`cat /opt/hsistema/links/"$2"dispositivo`
		WPrincipalGateway=`cat /opt/hsistema/links/"$2"Gateway`
		WPrincipalStatus=`cat /opt/hsistema/links/"$2"status`
		WPrincipalPrioridade=`cat /opt/hsistema/links/"$2"Prioridade`
		WPrincipalNome=`cat /opt/hsistema/links/"$2"nome`

	elif [ $1 == "backup" ]; then
		echo "$2 é Backup"
		WBackupDispositivo=`cat /opt/hsistema/links/"$2"dispositivo`
		WBackupGateway=`cat /opt/hsistema/links/"$2"Gateway`
		WBackupStatus=`cat /opt/hsistema/links/"$2"status`
		WBackupPrioridade=`cat /opt/hsistema/links/"$2"Prioridade`
		WBackupNome=`cat /opt/hsistema/links/"$2"nome`

	elif [ $1 == "backup2" ]; then
		WBackup2Dispositivo=`cat /opt/hsistema/links/"$2"dispositivo`
		WBackup2Gateway=`cat /opt/hsistema/links/"$2"Gateway`
		WBackup2Status=`cat /opt/hsistema/links/"$2"status`
		WBackup2Prioridade=`cat /opt/hsistema/links/"$2"Prioridade`
		WBackup2Nome=`cat /opt/hsistema/links/"$2"nome`

	elif  [ $1 == "backup3" ]; then
		WBackup3Dispositivo=`cat /opt/hsistema/links/"$2"dispositivo`
                WBackup3Gateway=`cat /opt/hsistema/links/"$2"Gateway`
                WBackup3Status=`cat /opt/hsistema/links/"$2"status`
                WBackup3Prioridade=`cat /opt/hsistema/links/"$2"Prioridade`
		WBackup3Nome=`cat /opt/hsistema/links/"$2"nome`

	else
		echo "Link não é nada"
	fi
}


trocaParaPrincipal(){

	if [ $GWATUAL == $WPrincipalGateway ]; then
        	echo "	-Link Principal Já em Uso" >> /var/opt/log/$DIA-trocalink.log
        else
		echo "	-Trocando para Link Principal:" >> /var/opt/log/$DIA-trocalink.log
        	route del default gw $GWATUAL
                echo "	-Deletando rota padrao" >> /var/opt/log/$DIA-trocalink.log
                route del default gw $WPrincipalGateway
                echo "	-Deletando possivel rota Principal" >> /var/opt/log/$DIA-trocalink.log
                route add default gw $WPrincipalGateway dev $WPrincipalDispositivo
                echo "	-Adicionando link Principal como gateway" >> /var/opt/log/$DIA-trocalink.log
	fi

}

trocaParaBackup(){

	if [ $GWATUAL == $WBackupGateway ]; then
        	echo "  -Link Backup Já em Uso" >> /var/opt/log/$DIA-trocalink.log
        else
        	echo "  -Trocando para Link Backup:" >> /var/opt/log/$DIA-trocalink.log
		route del default gw $GWATUAL
                echo "  -Deletando rota padrao" >> /var/opt/log/$DIA-trocalink.log
		route del default gw $WBackupGateway
                echo "  -Deletando possivel rota Backup" >> /var/opt/log/$DIA-trocalink.log
                route add default gw $WBackupGateway dev $WBackupDispositivo
                echo "  -Adicionando link Backup como gateway" >> /var/opt/log/$DIA-trocalink.log
	fi


}

trocaParaBackup2(){

        if [ $GWATUAL == $WBackup2Gateway ]; then
                echo "  -Link Backup2 Já em Uso" >> /var/opt/log/$DIA-trocalink.log
        else
                echo "  -Trocando para Link Backup2:" >> /var/opt/log/$DIA-trocalink.log
                route del default gw $GWATUAL
                echo "  -Deletando rota padrao" >> /var/opt/log/$DIA-trocalink.log
                route del default gw $WBackup2Gateway
                echo "  -Deletando possivel rota Backup2" >> /var/opt/log/$DIA-trocalink.log
                route add default gw $WBackup2Gateway dev $WBackup2Dispositivo
                echo "  -Adicionando link Backup2 como gateway" >> /var/opt/log/$DIA-trocalink.log
        fi

}

trocaParaBackup3(){

        if [ $GWATUAL == $WBackup3Gateway ]; then
                echo "  -Link Backup3 Já em Uso" >> /var/opt/log/$DIA-trocalink.log
        else
                echo "  -Trocando para Link Backup3:" >> /var/opt/log/$DIA-trocalink.log
                route del default gw $GWATUAL
                echo "  -Deletando rota padrao" >> /var/opt/log/$DIA-trocalink.log
                route del default gw $WBackup3Gateway
                echo "  -Deletando possivel rota Backup3" >> /var/opt/log/$DIA-trocalink.log
                route add default gw $WBackup3Gateway dev $WBackup3Dispositivo
                echo "  -Adicionando link Backup3 como gateway" >> /var/opt/log/$DIA-trocalink.log
        fi

}


#Cria as variaveis com o respectivos atributos mediante a quantidade de links
enum=0
while [ $enum -lt $QUANTIDADE ]; do
        enum=$(($enum+1))
	export W"$enum"DISPOSITIVO=`cat /opt/hsistema/links/w"$enum"dispositivo`
	export W"$enum"GATEWAY=`cat /opt/hsistema/links/w"$enum"Gateway`
	export W"$enum"STATUS=`cat /opt/hsistema/links/w"$enum"status`
	export W"$enum"PRIORIDADE=`cat /opt/hsistema/links/w"$enum"Prioridade`
	export W"$enum"NOME=`cat /opt/hsistema/links/w"$enum"nome`		

done

# Limpa rotas das tabelas de roteamento avancado
enum=0
while [ $enum -lt $QUANTIDADE ]; do
        enum=$(($enum+1))
        ip route del table wan$enum
	
done

#Grava rotas padroes para as tabelas de roteamento

ip route add default via $W1GATEWAY table wan1
ip route add default via $W2GATEWAY table wan2
ip route add default via $W3GATEWAY table wan3
ip route add default via $W4GATEWAY table wan4


trocaPrioridade(){
	QUANTIDADE=`cat /opt/hsistema/links/wanQuantidade`
        echo "----Iniciando Troca de Prioridade----" >> /var/opt/log/$DIA-trocalink.log

        if [ $1 == "w1" ]; then
	        echo "-Troca de Prioridade para link $W1NOME $W1DISPOSITIVO" >> /var/opt/log/$DIA-trocalink.log
        	sed -i "/def_prov1/s/$W1PRIORIDADE/principal/g" /opt/hsistema/config/cliente.txt
       		sed -i "/def_prov2/s/$W2PRIORIDADE/backup/g" /opt/hsistema/config/cliente.txt
       		sed -i "/def_prov3/s/$W3PRIORIDADE/backup2/g" /opt/hsistema/config/cliente.txt

        elif [ $1 == "w2" ]; then
		if [ $QUANTIDADE == "2" ]; then
        		echo "-Troca de Prioridade para link $W2NOME $W2DISPOSITIVO" >> /var/opt/log/$DIA-trocalink.log
        		sed -i "/def_prov1/s/$W1PRIORIDADE/backup/g" /opt/hsistema/config/cliente.txt
        		sed -i "/def_prov2/s/$W2PRIORIDADE/principal/g" /opt/hsistema/config/cliente.txt
        		sed -i "/def_prov3/s/$W3PRIORIDADE/backup2/g" /opt/hsistema/config/cliente.txt
		else
			echo "-Não á dois links" >> /var/opt/log/$DIA-trocalink.log
		fi

        elif [ $1 == "w3" ]; then
        
		if [ $QUANTIDADE == "3" ]; then
			echo "-Troca de Prioridade para link $W3NOME $W3DISPOSITIVO" >> /var/opt/log/$DIA-trocalink.log
        		sed -i "/def_prov1/s/$W1PRIORIDADE/backup/g" /opt/hsistema/config/cliente.txt
        		sed -i "/def_prov2/s/$W2PRIORIDADE/backup2/g" /opt/hsistema/config/cliente.txt
        		sed -i "/def_prov3/s/$W3PRIORIDADE/principal/g" /opt/hsistema/config/cliente.txt
		else
			
			echo "-Não á Três links" >> /var/opt/log/$DIA-trocalink.log	

		fi
        else

        echo "-Argumento para troca de prioridade errado"

        fi

        echo "----Finalizando Troca de Prioridade----" >> /var/opt/log/$DIA-trocalink.log
	/opt/hsistema/scripts/linktest.sh show
}



quant1(){
	
	echo "$QUANTIDADE Link de Internet" >> /var/opt/log/$DIA-trocalink.log
	
	verificaPrioridade $W1PRIORIDADE w1
	
	echo "Link Principal: $WPrincipalNome $WPrincipalDispositivo" >> /var/opt/log/$DIA-trocalink.log	
	
       	 
	if [ $WPrincipalStatus = "up" ]; then
		echo "Link Principal Up" >> /var/opt/log/$DIA-trocalink.log
                trocaParaPrincipal

        else
                echo "Link Principal Down" >> /var/opt/log/$DIA-trocalink.log
        fi


}

quant2(){

	echo "$QUANTIDADE Link de Internet" >> /var/opt/log/$DIA-trocalink.log

	verificaPrioridade $W1PRIORIDADE w1
	verificaPrioridade $W2PRIORIDADE w2

	echo "Link Principal: $WPrincipalNome $WPrincipalDispositivo" >> /var/opt/log/$DIA-trocalink.log
	echo "Link Backup: $WBackupNome $WBackupDispositivo" >> /var/opt/log/$DIA-trocalink.log

	if [ $WPrincipalStatus = "up" ]; then
		echo "Link Principal Up" >> /var/opt/log/$DIA-trocalink.log
        	trocaParaPrincipal
		
	elif [ $WBackupStatus = "up" ]; then
			
		echo "Link Principal Down" >> /var/opt/log/$DIA-trocalink.log
		echo "Link Backup Up" >> /var/opt/log/$DIA-trocalink.log
		trocaParaBackup
	else
        	echo "Link Principal Down" >> /var/opt/log/$DIA-trocalink.log
                echo "Link Backup Down" >> /var/opt/log/$DIA-trocalink.log
	fi


}

quant3(){

	echo "$QUANTIDADE Link de Internet" >> /var/opt/log/$DIA-trocalink.log        

	verificaPrioridade $W1PRIORIDADE w1
        verificaPrioridade $W2PRIORIDADE w2
	verificaPrioridade $W3PRIORIDADE w3
        
	echo "Link Principal: $WPrincipalNome $WPrincipalDispositivo" >> /var/opt/log/$DIA-trocalink.log
        echo "Link Backup: $WBackupNome $WBackupDispositivo" >> /var/opt/log/$DIA-trocalink.log
	echo "Link Backup2: $WBackup2Nome $WBackup2Dispositivo" >> /var/opt/log/$DIA-trocalink.log


	if [ $WPrincipalStatus = "up" ]; then

                echo "Link Principal Up" >> /var/opt/log/$DIA-trocalink.log
                trocaParaPrincipal

        elif [ $WBackupStatus = "up" ]; then

                echo "Link Principal Down" >> /var/opt/log/$DIA-trocalink.log
                echo "Link Backup Up" >> /var/opt/log/$DIA-trocalink.log
                trocaParaBackup

        elif [ $WBackup2Status = "up" ]; then
	
		echo "Link Principal Down" >> /var/opt/log/$DIA-trocalink.log
                echo "Link Backup Down" >> /var/opt/log/$DIA-trocalink.log
		echo  "Link Backup2 Up" >> /var/opt/log/$DIA-trocalink.log
                trocaParaBackup2

	else
		echo "Link Principal Down" >> /var/opt/log/$DIA-trocalink.log
                echo "Link Backup Down" >> /var/opt/log/$DIA-trocalink.log
                echo  "Link Backup2 Down" >> /var/opt/log/$DIA-trocalink.log

		
	fi
}

exec_padrao (){
quant$QUANTIDADE
}

case "$1" in
    w1) trocaPrioridade w1 ;;
    w2) trocaPrioridade w2 ;;
    w3) trocaPrioridade w3 ;;
    w4) trocaPrioridade w4 ;;
    -v) versao ;;
    --versao) versao ;;
    *) exec_padrao ;;
esac

DIAHORA=`date +%d"/"%m"/"%Y"-"%H":"%M":"%S`
echo "######## $DIAHORA - Final #########" >> /var/opt/log/$DIA-trocalink.log
echo " " >> /var/opt/log/$DIA-trocalink.log
