#!/bin/bash

DB='psql -d kickstart -U kickstart -c'

#$DB 'SELECT * FROM VLANS;'
$DB "INSERT INTO VLANS (id, public_network, private_network) VALUES($VLAN_ID,'$PUBLIC_NETWORK','$PRIVATE_NETWORK');"

#$DB "DELETE FROM VLANS WHERE ID=$1;"
