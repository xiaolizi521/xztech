#!/bin/bash

# File: build-debian.sh
# Package: p1post
# Install Location: (none)
# Name: Debian Package Build
#
# Supported Platforms: 
# Debian (all)
# Ubuntu (all)
#
# Description:
# This script will automate the building of a p1post debian package (.deb).
#
# Usage:
#
# Author: Adam Hubscher <ahubscher AT peer1 DOT com>
# Version: 1.0
# Last Updated: N/A
# Revision: 1

## Variables ##

# Current Directory
ROOT=$( pwd )

# Import Variables and Version Information
. ${ROOT}/VERSION
. ${ROOT}/BUILD_ENV

# Directories
ROOT_DIR="/usr/local/p1post"
SUB_DIRS=( lib script.d logs state tmp files keys )

# Source Root
SRC_DIR="${ROOT}/src/p1post-${VERSION}${ROOT_DIR}"

# Create the directory tree if it is not already there
# Note: Not all of these folders exist in the source tree.
# We want them explicitly created. So we make all of them.

mkdir -p "debian/${ROOT_DIR}"

# Directory Tree and File Copy Loop
for i in "${SUB_DIRS[@]}"
do
        mkdir -p "debian/${ROOT_DIR}/${i}";

        # Let's copy all the files we need
        for x in `ls "${SRC_DIR}/${i}"`
        do
            cp -f "${SRC_DIR}/${i}/${x}" "debian/${ROOT_DIR}/${i}"
        done
done

# We also need to cover the init script

mkdir -p "debian/etc/init.d"

# Copy it into place

cp -f "${ROOT}/src/p1post-${VERSION}/etc/init.d/p1post" "debian/etc/init.d/"