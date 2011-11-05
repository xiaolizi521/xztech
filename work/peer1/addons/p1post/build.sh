#!/bin/bash
# Simple script to build this package

SPEC="p1post.spec"

# For safety's sake, store where we are at.
TOPDIR=$( pwd )

# Move into the source directory, and build our archive
cd src
tar cvzf ../SOURCES/p1post-1.0.tar.gz .

# Return to root build directory
cd "${TOPDIR}"

mkdir {BUILD,RPMS,SRPMS};

# Create the local RPM macros file
cat > "$HOME/.rpmmacros" << EOT
%_topdir $PWD
%debug_package %{nil}
%vendor PEER 1 Hosting <http://www.peer1.com/>
EOT

# Execute the build using the specfile
rpmbuild -ba "SPECS/${SPEC}"
