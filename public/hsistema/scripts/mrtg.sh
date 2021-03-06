#!/bin/sh

TYPE=$1
PARAM=$2

if [ "$TYPE" = "load" ]; then
	INDATA=`cat /proc/loadavg | cut -d ' ' -f2 | sed 's/\.//g' | sed 's/^0//g'`
	OUTDATA=`cat /proc/loadavg | cut -d ' ' -f3 | sed 's/\.//g' | sed 's/^0//g'`
fi

if [ "$TYPE" = "processes" ]; then
	INDATA=`cat /proc/loadavg | cut -d ' ' -f4 | cut -d '/' -f 2`
	OUTDATA=`cat /proc/loadavg | cut -d ' ' -f4 | cut -d '/' -f 1`
fi

if [ "$TYPE" = "network" ]; then
	LINE=`cat /proc/net/dev | grep $PARAM | sed s/$PARAM://`
	INDATA=`echo $LINE | awk '{print $1}' `
	OUTDATA=`echo $LINE | awk '{print $9}' `
fi

if [ "$TYPE" = "swap" ]; then
	SWAPFREE=`cat /proc/meminfo | grep "SwapFree" | sed 's/ //g' | cut -d ':' -f2 | cut -d 'k' -f1`
	SWAPTOTAL=`cat /proc/meminfo | grep "SwapTotal" | sed 's/ //g' | cut -d ':' -f2 | cut -d 'k' -f1`
        SWAPUSED=`expr $SWAPTOTAL - $SWAPFREE`
	INDATA=$SWAPFREE
	OUTDATA=$SWAPUSED
fi

if [ "$TYPE" = "cpu" ]; then
	INDATA=`/usr/bin/awk '/cpu /{print $2+$3}'</proc/stat`
	OUTDATA=`/usr/bin/awk '/cpu /{print $2+$3+$4}'</proc/stat`
fi

if [ "$TYPE" = "uptime" ]; then
	INDATA=`cat /proc/uptime | cut -d ' ' -f2 | cut -d '.' -f1`
	OUTDATA=`cat /proc/uptime | cut -d '.' -f1`
fi

if [ "$TYPE" = "tcp" ]; then
	INDATA=`netstat -an | grep -c ESTABLISHED`
	#LINE=`/usr/bin/ftpcount | grep Service | cut -d '-' -f2 | cut -d ' ' -f4`
	#if [ "$LINE" = "" ]; then
	#OUTDATA="0"
	#else
	#OUTDATA=$LINE
	#fi
	OUTDATA=$INDATA
fi

echo $INDATA
echo $OUTDATA
echo `uptime | cut -d"," -f1,2`
echo $TYPE
