#!/bin/bash
# title:    booter 0.01
# date:     12/01/2006
# author:   donald ray moore jr. (dmoore@serverbeach.com)
# purpose:  add a new booter
# comments: this can be reusable into a more generic script

MAC_ADDRESS=${1}

OS=5;
PXE_TARGET=16;
TASK=6;

psql -d kickstart -U kickstart -c "
DELETE FROM mac_list WHERE mac_address='${MAC_ADDRESS}';
INSERT INTO mac_list (mac_address) VALUES ('${MAC_ADDRESS}');
INSERT INTO xref_macid_osload (mac_list_id, os_list_id, pxe_list_id, task_list_id) VALUES ((SELECT id FROM mac_list WHERE mac_address='${MAC_ADDRESS}'), $OS, $PXE_TARGET, $TASK);
SELECT * FROM view_mac_status WHERE mac_address='${MAC_ADDRESS}';
"
