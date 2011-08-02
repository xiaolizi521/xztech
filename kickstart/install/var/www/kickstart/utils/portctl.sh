#!/bin/bash

switch=$1
port=$2

cat <<POST | lynx -auth=kickstart:l33tNix -source - -post_data http://admin.serverbeach.com/ocean/port.php
command=PORT_CONTROL&switch=${switch}&port=${port}&action=on
---
POST

