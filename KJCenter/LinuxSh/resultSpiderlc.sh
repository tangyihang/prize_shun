#!/bin/sh
echo "---------------------------------------------------"
wget http://kj.800cai.com/index.php/home/collectresultlan/from163 -O /dev/null
dd=`date "+%Y-%m-%d" --date="1 day ago"`
wget http://kj.800cai.com/index.php/home/collectresultlan/from163?date=$dd -O /dev/null&
echo "---------------------fromOkooonet finished--------------------------"
wget http://kj.800cai.com/index.php/home/collectresultlan/from500 -O /dev/null
dd=`date "+%Y-%m-%d" --date="1 day ago"`
wget http://kj.800cai.com/index.php/home/collectresultlan/from500?date=$dd -O /dev/null&
