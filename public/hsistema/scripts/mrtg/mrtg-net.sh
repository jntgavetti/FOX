#!/bin/sh
awk '
/'$1':/ {
$0=substr($0,index($0,":")+1);
print $1;print $9
}
' /proc/net/dev
