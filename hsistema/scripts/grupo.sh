#!/bin/bash

AL='ABCDEFGHIJKLMNOPQRSTUVWXYZ'

if [ "$1" != " " ]; then
        if [ "$1" = "versao" ]; then
            echo -e v1.00 32
            exit
          else
            echo
        fi
fi

## variaveis
echo -e "Variaveis"

CH="/bin/chown"
GRUPUSER="www-data.www-data"
RDIR="/opt/hsistema/regras"
ADDMAC="$RDIR/addmac"

## Limpa arquivos de grupos
echo -e "Limpa arquivos"

rm -f $RDIR/grupo*
rm -f $RDIR/egrupo*
rm -f $RDIR/ngrupo*
rm -f $RDIR/sgrupo*
rm -f $RDIR/extensao*

## Gera arquivo de grupos
echo -e "Gera grupos"

num=0
vletra=
letra=
pnum=0
while [ $num -lt 9 ]; do
        num=$(($num+1))
        pnum=$(($pnum+1))
        index=${vletra[l]}
        letra=${AL:index:1}
        G="$RDIR/grupo$num"
        EG="$RDIR/egrupo$num"
        NG="$RDIR/ngrupo$num"
        SG="$RDIR/sgrupo$num"
        for i in `cat $ADDMAC`; do
                STATUS=`echo $i | cut -d ';' -f $pnum`
                NOME=`echo $i | cut -d ';' -f 29`
                DEP=`echo $i | cut -d ';' -f 30`
                MACSOURCE=`echo $i | cut -d ';' -f 27`
                ADDSOURCE=`echo $i | cut -d ';' -f 28`
                if [ "$STATUS" == "$letra" ]; then
                  echo $MACSOURCE >> $G
                  echo $ADDSOURCE >> $EG
                  echo $NOME >> $NG
                  echo $NOME - $DEP >> $SG
                fi
        done
        let vletra[0]++
done

num=0
vletra=9
letra=
pnum=9
while [ $num -lt 5 ]; do
        num=$(($num+1))
        pnum=$(($pnum+1))
        index=${vletra[l]}
        letra=${AL:index:1}
        G="$RDIR/grupo"$num"ex"
        EG="$RDIR/egrupo"$num"ex"
        NG="$RDIR/ngrupo"$num"ex"
        SG="$RDIR/sgrupo"$num"ex"
        for i in `cat $ADDMAC`; do
                STATUS=`echo $i | cut -d ';' -f $pnum`
                NOME=`echo $i | cut -d ';' -f 29`
                DEP=`echo $i | cut -d ';' -f 30`
                MACSOURCE=`echo $i | cut -d ';' -f 27`
                ADDSOURCE=`echo $i | cut -d ';' -f 28`
                if [ "$STATUS" == "$letra" ]; then
                  echo $MACSOURCE >> $G
                  echo $ADDSOURCE >> $EG
                  echo $NOME >> $NG
                  echo $NOME - $DEP >> $SG
                fi
        done
        let vletra[0]++
done

for i in `cat $ADDMAC`; do
        STATUS=`echo $i | cut -d ';' -f 15`
        NOME=`echo $i | cut -d ';' -f 29`
        DEP=`echo $i | cut -d ';' -f 30`
        MACSOURCE=`echo $i | cut -d ';' -f 27`
        ADDSOURCE=`echo $i | cut -d ';' -f 28`
        if [ $STATUS = "O" ]; then
          echo $MACSOURCE >> $RDIR/grupobloq
          echo $ADDSOURCE >> $RDIR/egrupobloq
          echo $NOME >> $RDIR/ngrupobloq
          echo $NOME - $DEP >> $RDIR/sgrupobloq
        fi
  done

for i in `cat $ADDMAC`; do
        STATUS=`echo $i | cut -d ';' -f 16`
        NOME=`echo $i | cut -d ';' -f 29`
        DEP=`echo $i | cut -d ';' -f 30`
        MACSOURCE=`echo $i | cut -d ';' -f 27`
        ADDSOURCE=`echo $i | cut -d ';' -f 28`
        if [ $STATUS = "P" ]; then
          echo $MACSOURCE >> $RDIR/grupom
          echo $ADDSOURCE >> $RDIR/egrupom
          echo $NOME >> $RDIR/ngrupom
          echo $NOME - $DEP >> $RDIR/sgrupom
        fi
  done

num=0
while [ $num -lt 3 ]; do
        num=$(($num+1))
        FEXT="$RDIR/extensao$num"
        SEXT="$RDIR/sextensao$num"
        for EXT in `cat $SEXT`; do
                echo "\."$EXT$  >> $FEXT
        done
done

$CH $GRUPUSER $RDIR/grupo*
$CH $GRUPUSER $RDIR/egrupo*
$CH $GRUPUSER $RDIR/ngrupo*
$CH $GRUPUSER $RDIR/sgrupo*
$CH $GRUPUSER $RDIR/extensao*

## executa regras
echo -e "exec squid"
/opt/hsistema/scripts/regras-squid.sh

echo -e "exec firewall"
/opt/hsistema/scripts/regras-fire.sh
