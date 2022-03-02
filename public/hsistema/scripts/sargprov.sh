#!/bin/bash

rm -r /var/wwws/html/squid-prov/*

## Inicia o Sarg
/usr/bin/sarg -f /opt/hsistema/config/sargprov.conf

