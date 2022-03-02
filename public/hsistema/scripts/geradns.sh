#!/bin/bash

DNSNUM=1

for i in `cat /etc/resolv.conf.$1| grep nameserver| cut -d ' ' -f 2`; do
        echo  "dns$DNSNUM" "$i" >> /opt/hsistema/config/$1.conf
        DNSNUM=$((DNSNUM+1))
  done

