#!/bin/bash
# GPGPU Installation Script
#
# Project: GPGPU/GPU Cloud Project
# Author: Adam Hubscher <ahubscher AT peer1 DOT com>
#
# Abstract: Unified Installation of nVidia Drivers and CUDA toolkit
#
# Last Updated: 2/7/2011 5:00 PM <ahubscher>
# 

# Make sure this is run as root
if [ $UID -ne 0 ]; then
    echo "This script must be run as root."
    exit 1
fi

# Set up common variables
PATH='/sbin:/bin:/usr/sbin:/usr/bin:/usr/local/sbin:/usr/local/bin'

# Lets set up our environment now
WORKDIR='/tmp/gpgpu'
LOGFILE='/tmp/gpgpu/install.log'
PREFIX='/usr/local/cuda'

# File Links - This will be moved local soon.

DRIVER="http://developer.download.nvidia.com/compute/cuda/3_2_prod/drivers/devdriver_3.2_linux_64_260.19.26.run"
CUDA="http://developer.download.nvidia.com/compute/cuda/3_2_prod/toolkit/cudatoolkit_3.2.16_linux_64_rhel5.5.run"

# Installer Flags - For full list/explinations of options, download the driver and use the option --advanced-options

driverFlags="-s"
cudaFlags="--target ${WORKDIR}/cuda -- --prefix=${PREFIX}"

mdkir "${WORKDIR}"

cd "${WORKDIR}"

# Let's download the files now

curl -o "driver.run" "${DRIVER}"

curl -o "cuda.run" "${CUDA}"

# First, let's install the driver.

chmod +x driver.run
driver.run $driverFlags

# Now, let's install the toolkit

chmod +x cuda.run
cuda.run $cudaFlags

# Done.