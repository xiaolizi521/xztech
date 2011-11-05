#!/bin/bash

# Installation of the InitRD update for Debian based OSes

function repack {
    
    # Backup the initrd to be modified
    cp initrd.gz ${1}.${2}.${3}.initrd.gz.old
    
    # Make a working directory
    mkdir -p tmp
    cd tmp

    currdir=$( pwd )

    echo ${currdir}
    
    # Extract the ramdisk
    gunzip -d < ../initrd.gz | cpio --extract --make-directories --no-absolute-filenames

    # Copy our startup script
    cp ${script} ${currdir}/${dir}
    chmod +x "${currdir}/${dir}/S31nicsel"

    # Rebuild our ramdisk and replace the old one.
    find . | cpio -H newc --create | gzip -9 > ../initrd.gz
    
    read -p "Press any key to continue..."

    rm -rf tmp
}

# Debian Installations to be Modified
debian=( etch lenny etchnhalf squeeze)
debroot="/tftpboot/pxe/debian"

# Ubuntu Installations to be Modified
ubuntu=( 10.04 6.06 6.10 8.04 9.04 )
uburoot="/tftpboot/pxe/ubuntu"

# Other Variables
arch=( amd64 i386 )
dir="lib/debian-installer-startup.d"

# Grab the file to be inserted for this fix
file="http://x-zen.cx/S31nicsel"
script="/tmp/S31nicsel"
curl -o $script $file

# Begin Debian Updating
if [ -d "$debroot" ]; then      
    for i in "${debian[@]}"
    do
        echo "Updating Debian ${i}"
        cd $debroot
        if [ -d "$i" ]; then
            # Distro root exists
            cd $i
            for a in "${arch[@]}"
            do
                if [ -d "$a" ]; then
                    # Architecture exists
                    cd $a
                    repack "debian" "${i}" "${a}"
                    cd ${debroot}/${i}
                fi
            done
        fi
    done
fi

# Begin Ubuntu Updating
if [ -d "$uburoot" ]; then      
    for i in "${ubuntu[@]}"
    do
        echo "Updating Ubuntu ${i}"
        cd $uburoot
        if [ -d "$i" ]; then
            # Distro root exists
            cd $i
            for a in "${arch[@]}"
            do
                if [ -d "$a" ]; then
                    # Architecture exists
                    cd $a
                    repack "ubuntu" "${i}" "${a}"
                    cd ${uburoot}/${i}
                fi
            done
        fi
    done
fi