#!/bin/bash


N=0
for DEST in $(printf "%d.%d.%d.%d\n" `cat /proc/net/route | grep "$1" | cut -f2 | sed 's/../ 0x&/g'`)
do
         N=$(($N+1))
         IP=$(echo $DEST | sed 's/\./ /g')
         existing=($IP);
         count=${#existing[@]}
         REAL=""
         qnt=$(($count-1))
         for (( i=$count-1;i>=0;i--));
         do
                if [ $i -eq $qnt ]; then
                REAL=${existing[${i}]};
                  else
		REAL=$REAL.${existing[${i}]};
		fi
         done
         if [ $N -eq 1 ]; then
                echo $REAL> /tmp/ip.txt
         else
                echo $REAL>> /tmp/ip.txt
         fi
done

N=0
for DEST in $(printf "%d.%d.%d.%d\n" `cat /proc/net/route | grep "$1" | cut -f8 | sed 's/../ 0x&/g'`)
do
         N=$(($N+1))
	 IP=$(echo $DEST | sed 's/\./ /g')
         existing=($IP);
         count=${#existing[@]}
         REAL=""
         qnt=$(($count-1))
         for (( i=$count-1;i>=0;i--));
         do
              if [ $i -eq $qnt ]; then
                  REAL=${existing[${i}]};
              else
                  REAL=$REAL.${existing[${i}]};
              fi
         done
         BINARIO=""
         for(( i=1;i<=4;i++  ));
         do
             CLASSE=$(echo $REAL | cut -d"." -f$i)
             BINARIO=$BINARIO$(echo "obase=2;$CLASSE" | bc -l)
         done
        REDE=$( echo $BINARIO | cut -d"0" -f1 | wc -m)
         REDE=$(($REDE-1))
         if [ $N -eq 1 ]; then
               echo $REAL > /tmp/mascara.txt
               echo $REDE > /tmp/rede.txt
         else
               echo $REAL >> /tmp/mascara.txt
               echo $REDE >> /tmp/rede.txt
         fi
done

paste -d"/" /tmp/ip.txt /tmp/rede.txt > /tmp/network.txt

NETWORK=`tail -n1 /tmp/ip.txt`
RANGE=`tail -n1 /tmp/network.txt`
echo network "$NETWORK" >> /opt/hsistema/config/$1.conf
echo range $RANGE >> /opt/hsistema/config/$1.conf

