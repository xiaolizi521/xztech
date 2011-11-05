#!/bin/bash

# Debian Nic Seletion Fix
# initrd update script
#
# The purpose of this script is to facilitiate easy repackaging
# Of an initrd file ass required by the fix determined for 
# DCO having to hit enter at the beginning of a Debian install
# in order to select hte primary NIC to be used for provisioning.
#
# Dependency: S31nicsel - Script that facilitates the automatic
# nic selection of this fix. Must be executable, is a shell sccript.
#
# Warning: Always remember to back up any files to be modified.
#
# USAGE: updateinird.sh /full/path/to/initrd.gz
#
# Produces: New initrd.gz package to replace the old one.
#
#

workdir="/tmp/initrd"
packdir="${workdir}/unpacked"
dest="${packdir}/lib/debian-installer-startup.d/S31nicsel"
packed="${workdir}/initrd.gz.new"

if [ -z $1 ]
then
    echo -e "USAGE: updateinitrd.sh /full/path/to/initrd.gz"
    exit 1;
fi

if [ -n $1 -a -f $1 ]; then
    file=$1
    oldmd5=$( md5sum $file )
fi

echo -e "Rebuilding ${file} with updated nic selection script.\n"
echo -e "Original md5sum: ${oldmd5}\n"

mkdir -p "${packdir}"
cp "${file}" "${workdir}"  
cd "${packdir}"

gunzip -c ../initrd.gz | cpio -id

(
cat <<EOF
#!/bin/sh -e
#
# S31nicsel - select the interface via pxe-injected kernel parameter

. /usr/share/debconf/confmodule

for i in $(cat /proc/cmdline); do
    case "$i" in
        BOOTIF=*)
            bootif_mac="$(echo ${i#BOOTIF=??-} | tr -s '-' ':')"
            for j in /sys/class/net/*; do
                if grep -q "${bootif_mac}" "${j}/address"; then
                    bootif_name="${j#/sys/class/net/}"
                    db_set netcfg/choose_interface "${bootif_name}"
                    exit 0
                fi
            done
        ;;
    esac
done
EOF
) > $dest

chmod 755 "${dest}"

find . | cpio --create --format='newc' | gzip > "${packed}"

md5=$( md5sum $packed )

echo -e "New initrd creation complete.\n\n"
echo -e "New md5sum: ${md5}\n"

echo -e "The new file is located at ${packed}\n"
echo -e "Be sure to back up ${file} before replacing it with ${packed}.\n"
echo -e "Note: A backup can be found at ${workdir}/initrd.gz\n"

echo -e "Cleaning up temporary files.."

rm -rf "${packdir}"

exit 0;

