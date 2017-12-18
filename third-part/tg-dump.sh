#!/bin/bash

if [ `/usr/bin/screen -list tg-cli | grep tg-cli | wc -l` -eq 0 ]
then
	/usr/bin/screen -S tg-cli -d -m /usr/local/src/tg/bin/telegram-cli --json -P 9009
fi

/usr/bin/ruby2.0 /usr/local/src/telegram-history-dump/telegram-history-dump.rb
/usr/bin/php7.1 /data/www/pinguem-bot/public_html/yii history/parse
/usr/bin/indexer -c /etc/sphinxsearch/sphinx.conf pinguem --buildstops /data/www/pinguem-bot/wordfreq.txt 1000
/usr/bin/php7.1 /data/www/pinguem-bot/public_html/yii history/sphinx-analyze

exit 0
