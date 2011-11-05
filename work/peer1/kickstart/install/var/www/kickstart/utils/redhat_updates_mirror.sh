#!/bin/bash

mirror_base="/mirrors/redhat/redhat/linux"

# updates
for rhver in 7.2 7.3 8.0 9 ; do
	fmirror -e "f \.src\.rpm$" -e "f (s390|i586|ia64)" -e "f ja/os" -l "${mirror_base}/updates/${rhver}" -r /${rhver} -s updates.redhat.com -S -V1
done
