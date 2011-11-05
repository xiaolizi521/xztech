#!/bin/bash
# GPGPU Installation Script
#
# Project: GPGPU/GPU Cloud Project
# Author: Adam Hubscher <ahubscher AT peer1 DOT com>
#
# Abstract: Unified Installation of nVidia Drivers and CUDA toolkit
#
# This script is brand agnostic.
#

# Section: Initialization

# Make sure this is run as root
if [ $UID -ne 0 ]; then
    echo "This script must be run as root."
    exit 1
fi

hw=$( lspci | grep 3D | grep nVidia | awk '{print $4}' | wc -l )

if [ $hw -lt 1]; then
    echo "This script will not install unless nVidia GPU hardware is present."
    exit 1
fi

# Section: Setup

# Set up common variables
PATH='/sbin:/bin:/usr/sbin:/usr/bin:/usr/local/sbin:/usr/local/bin'

# Lets set up our environment now. If this is sbpost, we'll work out of a different directory than initboot.

if [ -d "/usr/local/sbpost" ]; then
    WORKDIR="/usr/local/sbpost/gpgpu"
else
    WORKDIR='/tmp/gpgpu'
fi

# Installatin Prefix
PREFIX='/usr/local/cuda'

# URLs for obtaining setup materials

DRIVER="http://kickstart/installs/gpgpu/driver.run"
CUDA="http://kickstart/installs/gpgpu/cuda.run"
SDK="http://kickstart/installs/gpgpu/sdk.run"

# Installer Flags - For full list/explinations of options, download the driver and use the option --advanced-options

driverFlags="-s"
cudaFlags="--target ${WORKDIR}/cuda -- --prefix=${PREFIX}"
sdkFlags="--target ${WORKDIR}/sdk -- --prefix=${PREFIX}/sdk --cudaprefix=${PREFIX}"

# Section: Installation

# Create temporary working directory
mkdir -p "${WORKDIR}"

# Installer does not create prefix directory??
mkdir -p "${PREFIX}"

cd "${WORKDIR}"

# Before we begin, we need to install some packages. These packages are required for a great number of things related to CUDA.

yum install -y freeglut-devel libXi-devel libXmu-devel

# Let's download the files now

curl -o "${WORKDIR}/driver.run" "${DRIVER}"
curl -o "${WORKDIR}/cuda.run" "${CUDA}"
curl -o "${WORKDIR}/sdk.run" "${SDK}"

# First, let's install the driver.

chmod +x driver.run
./driver.run $driverFlags

# Now, let's install the toolkit

chmod +x cuda.run
./cuda.run $cudaFlags

# Now, we need to update the login items, to specify locations of cuda libs and binaries.

echo "export PATH=\$PATH:/usr/local/cuda/lib:/usr/local/cuda/lib64:/usr/local/cuda/bin" >> /etc/profile
echo "export LD_LIBRARY_PATH=\$LD_LIBRARY_PATH:/usr/local/cuda/lib:/usr/local/cuda/lib64" >> /etc/profile

# We also want to run these commands oursefl, in order to run tests with the SDK.

export PATH=$PATH:/usr/local/cuda/lib:/usr/local/cuda/lib64:/usr/local/cuda/bin
export LD_LIBRARY_PATH=/usr/local/cuda/lib:/usr/local/cuda/lib64

# And finally, add in the SDK. This is for both customer reference and for tests. This enables QA to be able to perform tests on the driver.

chmod +x sdk.run
./sdk.run $sdkFlags

# Once installed, we want to build all the utilities for later use in testing.

cd /usr/local/cuda/sdk/C
make

# Correct permissions on CUDA library folder - This is to allow regular user access to libs and utilities.

cd /usr/local/cuda
find . -perm 700 -exec chmod 755 {} \;
find . -perm 600 -exec chmod 644 {} \;

# Done.