#!/bin/sh
wget http://kjm.198tc.com/index.php/home/collectresult/from163 -O /dev/null
dd=`date "+%Y-%m-%d" --date="1 day ago"`
wget http://kjm.198tc.com/index.php/home/collectresult/from163?date=$dd -O /dev/null&
echo "---------------------from163 finished--------------------------"
wget http://kjm.198tc.com/index.php/home/collectresult/from500 -O /dev/null
dd=`date "+%Y-%m-%d" --date="1 day ago"`
wget http://kjm.198tc.com/index.php/home/collectresult/from500?date=$dd -O /dev/null&
echo "---------------------from500 finished--------------------------"
wget http://kjm.198tc.com/index.php/home/collectresult/fromOkooonet -O /dev/null
dd=`date "+%Y-%m-%d" --date="1 day ago"`
wget http://kjm.198tc.com/index.php/home/collectresult/fromOkooonet?date=$dd -O /dev/null&
echo "---------------------fromOkooonet finished--------------------------"
echo "---------------------------------------------------"
wget http://kjm.198tc.com/index.php/home/collectresultlan/from163 -O /dev/null
dd=`date "+%Y-%m-%d" --date="1 day ago"`
wget http://kjm.198tc.com/index.php/home/collectresultlan/from163?date=$dd -O /dev/null&
echo "---------------------fromOkooonet finished--------------------------"
wget http://kjm.198tc.com/index.php/home/collectresultlan/from500 -O /dev/null
dd=`date "+%Y-%m-%d" --date="1 day ago"`
wget http://kjm.198tc.com/index.php/home/collectresultlan/from500?date=$dd -O /dev/null&
echo "---------------------fromOkooonet finished--------------------------"
wget http://kjm.198tc.com/index.php/home/collectresultlan/fromOkooonet -O /dev/null
dd=`date "+%Y-%m-%d" --date="1 day ago"`
wget http://kjm.198tc.com/index.php/home/collectresultlan/fromOkooo?date=$dd -O /dev/null&
echo "---------------------fromOkooonet finished--------------------------"
