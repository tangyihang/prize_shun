<?php
namespace Org\Net;
class HttpClient{
	
public $url='';
public $xml='';
public $datakey='';
public $datatype='xml';
public $charset='utf-8';

function __construct($iarr){
  $this->url=$iarr['lotterycenter'];
  $this->datakey=$iarr['agenterkey'];
  $this->charset=$iarr['charset'];
}
//对数据进行des加密
function des($data){
;
}
function getMd5text($timestamp,$strbody){
   return md5($timestamp.$this->datakey.$strbody);
}
function arrtoXml($header,$ielement,$username='',$userip='',$source='web'){
	 $header['timestamp']=time('YmdHis',time());
	if($ielement != null)
	{
		$strbody = '<body><elements><element>'.$this->easy_array_xml_to_string($ielement).'</element></elements></body>';
	}else
	{
		$strbody = '';
	}

    $header['digest']=$this->getMd5text($header['timestamp'],$strbody);
    $header['ipaddress'] = $userip;
    $header['username']  = $username;
    $header['source']    = $source;
    $strheader = '<header>'.$this->easy_array_xml_to_string($header).'</header>';
    $resultstr = '<?xml version="1.0" encoding="UTF-8" ?><message version="1.0">'.$strheader.$strbody.'</message>';
    return $resultstr;
}

//发送到服务器
function send($header,$ielement,$username='',$userip='',$source='web')
{
  	  $requestXml=$this->arrtoXml($header, $ielement,$username,$userip,$source);
  	  
      $curl = curl_init();
	  curl_setopt($curl, CURLOPT_URL, $this->url);
	  curl_setopt($curl, CURLOPT_POST, 1);
	  curl_setopt($curl, CURLOPT_POSTFIELDS, $requestXml);
	  curl_setopt($curl, CURLOPT_TIMEOUT, 30); 
	  curl_setopt($curl, CURLOPT_HEADER, 0); 
	  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	  
	  $responsexml = curl_exec($curl); 
	  if (curl_errno($curl)) {
	     echo 'Errno'.curl_error($curl);
	  }
	  curl_close($curl);
     if($responsexml == true)
		{
			$this->setxml($responsexml);
		}else
		{
			$this->xml['body']['oelement']['errorcode'] = '10018';
			$this->xml['body']['oelement']['errormsg'] = iconv('gbk','utf-8','网络通讯异常');
		}
  }

function easy_array_xml_to_string($ielement)
{
		$resultstr = '';
		
		if(is_string($ielement)){
			return $ielement;
		}
		
		foreach($ielement as $key =>$value)
		{
			if($this->charset == 'utf-8')
				$resultstr .= '<'.$key.'>'.$value.'</'.$key.'>';
			else
				$resultstr .= '<'.$key.'>'.iconv($this->charset,'utf-8',$value).'</'.$key.'>';
		}
		return $resultstr;
}

function setxml($easy_xmlstr)
	{
		if($easy_xmlstr == '')
		return ;
		
		$xmlobj = simplexml_load_string($easy_xmlstr);
		if(!xmlobj)
		  return ;
		$this->xml = $this->easy_xml_to_array($xmlobj); 
	}
	//
    function getoelementvalue($keyvalue)
	{
	    return $this->xml['body']['oelement'][$keyvalue];
	}
	//
    function getelements()
	{
		if(is_array($this->xml['body']['elements']['element'][0]))
		   return $this->xml['body']['elements']['element'];
	    else
		   return $this->xml['body']['elements'];
	}
	//
	function  easy_xml_to_array($obj) 
	{
		
	 if( count($obj) >= 1 )
	    {
	        $result = $keys = array();
	        
	        foreach( $obj as $key=>$value)
	        {   
	            isset($keys[$key]) ? ($keys[$key] += 1) : ($keys[$key] = 1);
	            
	            if( $keys[$key] == 1 )
	            {
	                $result[$key] = $this->easy_xml_to_array($value);
	            }
	            elseif( $keys[$key] == 2 )
	            {
	                $result[$key] = array($result[$key], $this->easy_xml_to_array($value));
	            }
	            else if( $keys[$key] > 2 )
	            {
	                $result[$key][] = $this->easy_xml_to_array($value);
	            }
	        }
	        return $result;
	    }
	    else if( count($obj) == 0 )
	    {
	    	if($this->charset=='utf-8')
	        	return (string)$obj;    
	    	else
	            return iconv('utf-8',$this->charset,(string)$obj);
	    }
	}
}
