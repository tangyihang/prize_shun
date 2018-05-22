#!/bin/bash  
for(( i = 0; i < 60; i++ ))  
do  
{ 
  /local/server/php/bin/php -q /local/server/html/newm.198tc.com/KJCenter/spider_jc.php
  sleep(12);
  echo 1;
}&  
done  
wait
/local/server/php/bin/php -q /local/server/html/newm.198tc.com/KJCenter/spider_jc.php 
