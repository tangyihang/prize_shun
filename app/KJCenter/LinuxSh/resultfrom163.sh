!/bin/sh
wget http://newm.198tc.com/index.php/home/collectresult/from163 -O /dev/null
dd=`date "+%Y-%m-%d" --date="1 day ago"`
wget http://newm.198tc.com/index.php/home/collectresult/from163?date=$dd -O /dev/null&
echo "---------------------from163 finished--------------------------"
wget http://newm.198tc.com/index.php/home/collectresult/from500 -O /dev/null
dd=`date "+%Y-%m-%d" --date="1 day ago"`
wget http://newm.198tc.com/index.php/home/collectresult/from500?date=$dd -O /dev/null&
echo "---------------------from500 finished--------------------------"
wget http://newm.198tc.com/index.php/home/collectresult/fromOkooonet -O /dev/null
dd=`date "+%Y-%m-%d" --date="1 day ago"`
wget http://newm.198tc.com/index.php/home/collectresult/fromOkooonet?date=$dd -O /dev/null&
echo "---------------------fromOkooonet finished--------------------------"
