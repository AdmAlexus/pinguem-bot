#1/bin/bash

if [ `service sphinxsearch status | grep running | wc -l` -gt 0 ]
then
	/usr/bin/indexer --rotate --all
else
	/usr/bin/indexer --all
	/usr/bin/service sphinxsearch start
fi

exit 0
