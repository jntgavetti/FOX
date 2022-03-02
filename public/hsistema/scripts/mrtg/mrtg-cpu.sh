#!/bin/sh
unset LANG
mem=$(/usr/bin/free|grep ^-)
load=$(cat /proc/loadavg)
/usr/bin/awk -v load="$load" -v mem="$mem" '
BEGIN {
split(load,loadstats)
print int(100*loadstats[2])
split(mem,memstats);
print int(100*memstats[3]/(memstats[3]+\
memstats[4]));
}'
