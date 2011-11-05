#!/bin/bash

#ncftpget -R -u ensimwpl -p ensim94089 ftp2.ensim.com . /apt/ensim/LWP/4.0.3/22.fc.1/
#ncftpget -R -u ensimwpl -p ensim94089 ftp2.ensim.com . /apt/ensim/LWP/4.0.3/22.fc.2/
#ncftpget -R -u ensimwpl -p ensim94089 ftp2.ensim.com . /apt/ensim/LWP/4.0.3/22.rhel.3ES/

fmirror -l /mirrors/ftp2.ensim.com/apt/ensim/LWP/4.0.3 -r /apt/ensim/LWP/4.0.3 -s ftp2.ensim.com -u ensimwpl -p ensim94089 -S -V1
fmirror -l /mirrors/ftp2.ensim.com/apt/ensim/LWP/4.0.4 -r /apt/ensim/LWP/4.0.4 -s ftp2.ensim.com -u ensimwpl -p ensim94089 -S -V1
