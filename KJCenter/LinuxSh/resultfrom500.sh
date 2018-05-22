!/bin/sh
wget http://sp.198tc.com/index.php/home/collectresult/from500 -O /dev/null
dd=`date "+%Y-%m-%d" --date="1 day ago"`
wget http://sp.198tc.com/index.php/home/collectresult/from500?date=$dd -O /dev/null
