#!/bin/bash
# relink.sh 0.01 - link a PXE configuration file to a MAC
# written by donald ray moore jr. (dmoore@serverbeach.com)


PXE_CONFIG_DIR="/tftpboot/pxe/pxelinux.cfg"
PXE_TARGET=${1}

# the given MAC must be formatted with a leading 01-
# as PXE searches for this
MAC=01-${2//:/-}

# this doesn't work 
if [ -h "${PXE_CONFIG_DIR}/${PXE_TARGET}" ]; then
        echo "${PXE_TARGET} is a symbolic link! it's unlikely it's a pxe config file!"
        exit 1
fi

# link the requested target to the given MAC
ln -sf /tftpboot/pxe/pxelinux.cfg/${PXE_TARGET} ${MAC}

# display what was done, this should be a debug 
# statement and not part of of how it actually works
ls -al "${PXE_CONFIG_DIR}/${MAC}"
