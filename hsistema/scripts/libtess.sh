        # Libera teste de VPN

        QVPN=`cat /opt/hsistema/config/vpn.txt | grep quant_vpn | cut -d '=' -f2`

        echo -e "teste de vpn"
                enum=0
                while [ $enum -lt $QVPN ]; do
                        enum=$(($enum+1))
			IPSOURCE=`cat /opt/hsistema/config/vpn.txt | grep vpn"$enum"_apps_ip | cut -d '=' -f2`
			if [ $IPSOURCE != "sem" ]; then
				$SF -A OUTPUT -d $IPSOURCE -j ACCEPT
			fi	
			TYPE=`cat /opt/hsistema/config/vpn.txt | grep vpn"$enum"_type | cut -d '=' -f2`
			if [ $TYPE = "ipsec" ]; then
				IPSOURCE=`cat /opt/hsistema/config/vpn.txt | grep vpn"$enum"_test | cut -d '=' -f2`
				if [ $IPSOURCE != "sem" ]; then
					$SF -A OUTPUT -d $IPSOURCE -j ACCEPT
				fi	
			fi
                done


