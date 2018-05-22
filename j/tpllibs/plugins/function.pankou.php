<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage PluginsFunction
 */


/**
 * Smarty {fetch} plugin
 *
 * Type:     function<br>
 * Name:     fetch<br>
 * Purpose:  fetch file, web or ftp data and display results
 * @link http://smarty.php.net/manual/en/language.function.fetch.php {fetch}
 *       (Smarty online manual)
 * @author Monte Ohrt <monte at ohrt dot com>
 * @param array $params parameters
 * @param object $smarty Smarty object
 * @param object $template template object
 * @return string|null if the assign parameter is passed, Smarty assigns the
 *                
 */
function smarty_function_pankou($params, $smarty, $template)
{   
      if($params['homescore'] === '' && $params['guestscore'] === ""){return "-";}
      $homescore = $params['homescore'] ? intval($params['homescore']) : '';
	  $guestscore = $params['guestscore'] ? intval($params['guestscore']) : '';
	  $pankou = $params['pankou'] ? floatval($params['pankou']) : '';
	  $type = $params['type'];
	  $homeid = $params['homeid'];
	  $hometeamid = $params['hometeamid'];
	  $guestteamid = $params['guestteamid'];
    if ($params['homescore'] == '' || $params['guestscore'] == '') {
        trigger_error("[plugin] fetch parameter 'file' cannot be empty",E_USER_NOTICE);
        return;
    }
    if($type == 1)
    {
    	return smarty_function_html_getpankoures($homescore,$guestscore,$pankou,$homeid,$hometeamid,$guestteamid);
    	 
    }elseif($type == 2){
	    return smarty_function_html_getBigSmallRes($homescore,$guestscore,$pankou);
	}
}
function smarty_function_html_getpankoures($homescore,$guestscore,$pankounum,$homeid,$hometeamid,$guestteamid)
{
	if(empty($pankounum)){return '';}
	if($homeid == $hometeamid){
		if($homescore + $pankounum > $guestscore){
		return '<span class="red_w">赢</span>';
		}elseif($homescore + $pankounum == $guestscore){
			return '<span class="blue_w">走</span>';
		}elseif($homescore + $pankounum < $guestscore){ 
			return '<span class="green_w">输</span>';
		}
	}else if($homeid == $guestteamid){
		if($guestscore - $pankounum > $homescore){
		return '<span class="red_w">赢</span>';
		}elseif($guestscore - $pankounum == $homescore){
			return '<span class="blue_w">走</span>';
		}elseif($guestscore - $pankounum < $homescore){ 
			return '<span class="green_w">输</span>';
		}
	}
}

function smarty_function_html_getBigSmallRes($homescore,$guestscore,$pankounum)
{
 $score = intval($homescore)+intval($guestscore);
 if($score > $pankounum)
 {
    return '<span class="red_w">大</span>';
 }elseif($score < $pankounum)
 {
    return '<span class="green_w">小</span>';
 }else{
    return '<span class="blue_w">走</span>';
 }
}
?>
