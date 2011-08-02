#!/bin/bash

updates="/mirrors/redhat/redhat/linux/updates"
installs="/exports/installs/linux/redhat"
kver="2.4.20"
krel="28"

for RHVER in 7.2 7.3 8.0 9 ; do
  for ARCH in i386 i686 athlon ; do
    find ${updates}/${RHVER} -name kernel-${kver}\*.${ARCH}.rpm -exec cp {} ${installs}/${RHVER}/RedHat/RPMS/ ';'
  done
  find ${installs}/${RHVER} -name kernel-${kver}-\*.rpm | \
    grep -v ${kver}-${krel} | xargs rm -f {}
  echo "Running genhdlist in ${installs}/${RHVER}"
  /usr/lib/anaconda-runtime/genhdlist ${installs}/${RHVER}
done
