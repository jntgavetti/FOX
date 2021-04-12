#!/bin/bash
PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"
export LANG=pt_BR.UTF-8
ONTEM=`date +%Y"-"%m"-"%d -d "1 days ago"`
DIA=`date +%Y"-"%m"-"%d`
DIAHORA=`date +%d"/"%m"/"%Y"-"%H":"%M":"%S`


#------------------------------------------------------------- variaveis ----------------------------------------------------------------

echo -e "variaveis"


#interfaces internas

QLINKINT=9

for i in $(seq -f '%02g' 1 $QLINKINT); do 
	echo "$i" 
done
