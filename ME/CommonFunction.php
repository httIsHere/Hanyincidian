<?php


//require_once(dirname(_FILE_).'../cgi-bin/CommonFunction.php');
 
  
define("yqAccount","ItsMusic");
define("yqAppId","wx3baa5a278d207f5e");
define("yqAppsecret","072b4d7cf04dd25660f577d0017c6f62");
//发生被动回复
    function ReplyByText($WXAccount,$toUsername,$fromUsername,$contentStr)
   {      			 $thisFunction="The function is ReplyByText";$postStr=$WXAccount.",".$toUsername.",".$fromUsername.",".$contentStr;			
		$msgType='text';		
		$time = time();
		$textTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<Content><![CDATA[%s]]></Content>
						<FuncFlag>0</FuncFlag>
					</xml>";             
		if(!empty($contentStr))
		{              		
			$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
			
		}
		else
		{
			$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, 'Empty string.');
		}
						$replyStr=$resultStr; 
						logger('CommonFunction','log/','Log',$thisFunction." postStr=".$postStr);
						logger('CommonFunction','log/','Log',$thisFunction." replyStr=".$replyStr);
		return $resultStr;
}
//信息发送
	function sendTxtMsg($WXAccount,$appid,$appsecret,$fromUserName,$toUserName,$msg)
	{					$thisFunction="The function is sendTxtMsg";$postStr=$WXAccount.",".$appid.",".$appsecret.",".$fromUserName.",".$toUserName.",".$msg;
		if(gettype($toUserName)=="object") { $toUserName=(string)$toUserName; }
		$txttmp=array('touser'=>$toUserName,'msgtype'=>'text','text'=>array('content'=>$msg));		
		$txt=ch_json_encode($txttmp);  			
		send($WXAccount,$appid,$appsecret,$txt);
						$replyStr=""; 
						logger('CommonFunction','log/','Log',$thisFunction." postStr=".$postStr);
						logger('CommonFunction','log/','Log',$thisFunction." replyStr=".$replyStr);
	}
	function send($WXAccount,$appid,$appsecret,$txt)
	{					$thisFunction="The function is send";$postStr=$WXAccount.",".$appid.",".$appsecret.",".$txt;
		$access_token =getAccess_Token($WXAccount,$appid,$appsecret);   
		$url="https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$access_token;
		$output =wxHttpsRequest($url,$txt);
 		$replyStr=$output; // json_encode($array);
						logger('CommonFunction','log/','Log',$thisFunction." postStr=".$postStr);
						logger('CommonFunction','log/','Log',$thisFunction." replyStr=".$replyStr);
	}

	function wxHttpsRequest($url,$data=null){
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
			if (!empty($data)){
					curl_setopt($curl, CURLOPT_POST, 1);
					curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			}
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($curl);
			curl_close($curl);
			return $output;
	}


//获取二维码
	function getTicketOfQrcode($WXAccount,$appid,$appsecret,$sceneName)
	{					logger('CommonFunction','log/','Log',"getTicketOfQrcode postStr=".$WXAccount.$appid.$appsecret.$sceneName);	
 		$access_token =getAccess_Token($WXAccount,$appid,$appsecret); 
		$url="https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
		$data='{"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "'.$sceneName.'"}}}';
		$output =wxHttpsRequest($url,$data);
		$jsoninfo = json_decode($output, true);
 						logger('CommonFunction','log/','Log',"getTicketOfQrcode replyStr=".$output);
				
		return 	$jsoninfo;		
	}

	function getQRCodeUrlFromTicket($ticket,$filename)
	{	
		$ticket=urlencode($ticket);
		$url="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$ticket;
		$imageInfo=downloadImageFromWeiXin($url);	
		
		$local_file=fopen($filename,'w');
		if(false!==$local_file)
		{	if(false!==fwrite($local_file,$imageInfo["body"])) 
			{ fclose($local_file);}
		}
		return $filename;
	}
	function downloadImageFromWeiXin($url)
	{
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER,0);
		curl_setopt($curl, CURLOPT_NOBODY,0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);		
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$package = curl_exec($curl);
		$httpinfo=curl_getinfo($curl);
		curl_close($curl);
		return array_merge(array('body'=>$package),array('header'=>$httpinfo));
	}


//关注的用户信息处理	
	
	function getOneWXUserInfo($WXAccount,$appid,$appsecret,$openID)//根据OpenID，获取一个关注用户信息
	{					$thisFunction="The function is getOneWXUserInfo";$postStr=$WXAccount.",".$appid.",".$appsecret.",".$openID;
		$access_token =getAccess_Token($WXAccount,$appid,$appsecret); //$jsoninfo["access_token"];
		
		//获取用户信息
		$url ="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$openID;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		$oneWXUserInfo = json_decode($output, true);
 		$replyStr=$output; // json_encode($array);
						logger('CommonFunction','log/','Log',$thisFunction." postStr=".$postStr);
						logger('CommonFunction','log/','Log',$thisFunction." replyStr=".$replyStr);
		return $oneWXUserInfo;
	}

	
	function insertOrUpdateOneUserInfo($WXAccount,$appid,$appsecret,$openID,$lx=1000,$ly=1000)
	{	$ret=runSelectSql("select OpenID from GlobalUser where OpenID='$openID'");
		if(count($ret)>0) {$ret=runInsertUpdateDeleteSql("update GlobalUser set Location_X='$lx',Location_Y='$ly' where  OpenID='$openID'");}
		else 
		{
			$oneUserInfo=getOneWXUserInfo($WXAccount,$appid,$appsecret,$openID);
			$tOpenID=$openID;						$nickname=$oneUserInfo['nickname'];				
			$sex=$oneUserInfo['sex'];				$language=$oneUserInfo['language'];
			$city=$oneUserInfo['city'];				$country=$oneUserInfo['country'];
			$province=$oneUserInfo['province'];		$headimgurl=$oneUserInfo['headimgurl'];				
			$subscribe_time=$oneUserInfo['subscribe_time'];
			$unionid=$oneUserInfo['unionid'];		$remark=$oneUserInfo['remark'];
			$groupid=$oneUserInfo['groupid'];
			
			$ret=runInsertUpdateDeleteSql("insert into GlobalUser(OurWeChatAccount,OpenID,nickname, subscribe,subscribe_time,sex,city,country,province,language,headimgurl,unionid,remark,groupid,Location_X,Location_Y) values('$WXAccount','$tOpenID','$nickname', 1,'$subscribe_time','$sex','$city','$country','$province','$language','$headimgurl','$unionid','$remark','$groupid','$lx','$ly')");
		}
		
	}
//通用一般函数
  	function runSelectSql($sql)
	{	logger('CommonFunction','log/','Log'," sql=".$sql);
		$selectPos=stripos($sql,"select");		$fromPos=stripos($sql,"from");
		$fieldStr=str_replace(" ","",substr($sql,$selectPos+strlen("select"),$fromPos-$selectPos-strlen("select")));
		if(strpos($fieldStr,","))
		{	$field= explode(",",$fieldStr);	$fieldNumber=count($field);		}
		else
		{	$field[0]=$fieldStr;		$fieldNumber=1;}
		$replyStr="";		$recodeList=array();
		$link=openDB();
		if($link)
		{	$res=mysql_query($sql);		
			if(!$res) {logger('CommonFunction','log/','Error',"runSelectSql Mysql-query error:".$sql.mysql_error());}
			else 
			{	$sqlNum =  mysql_num_rows($res); 
				for($i=0;$i<$sqlNum;$i++)
				{	$row=mysql_fetch_array($res);  
					if($row) { 	for($j=0;$j<$fieldNumber;$j++)	$recodeList[$i]["$field[$j]"]=$row["$field[$j]"];}
				}
			}
			$replyStr=json_encode($recodeList);	
			mysql_close($link);	
		}
		else {logger('CommonFunction','log/','Error',"runSelectSql Mysql-query error:".$link.mysql_error()); }
				logger('CommonFunction','log/','Log'," replyStr=".$replyStr);
		return $recodeList;
	}
	function runInsertUpdateDeleteSql($sql)
	{	$replyStr=0;
		$link=openDB();
		if($link)
		{	if(!mysql_query($sql)) {logger('CommonFunction','log/','Error',"runSelectSql Mysql-query error:".$sql.mysql_error());}
			else $replyStr=1;
			mysql_close($link);	
		}
		else {logger('CommonFunction','log/','Error',"runSelectSql Mysql-query error:".$link.mysql_error()); }
		return $replyStr;
	}


//得到Access_Token，

	function getAccess_Token($WXAccount,$appid,$appsecret)
	{
						$thisFunction="The function is getAccess_Token";$postStr=$WXAccount.",".$appid.",".$appsecret;
						
		$link=openDB();
		if($link)
		{   $nowtime=time();
			$sql = "SELECT * FROM `AccessToken` WHERE WXAccount='$WXAccount' order by getTime desc limit 1";
			$res = mysql_query($sql);
			if($res)
			 {	
				$row=mysql_fetch_array($res);
				$Token=$row['Token'];			
				$gettime=$row['getTime'];		
				$expire=$row['expire'];				
			 }
			 else
			 {  $Token="";
													logger($WXAccount.'_getAccess_Token','log/','error'," Mysql-query error:".$sql.mysql_error());
			 }					   			 
			mysql_close($link);	
			$expiretime=$gettime+$expire-600;
			if($expiretime<$nowtime) 
			{ $Token=""; 	
			}				
		  }
		  else {  $Token=""; }

		if($Token=="")
		{   $nowtime=time();
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch);
			curl_close($ch);
			$jsoninfo = json_decode($output, true);	
			$Token = $jsoninfo["access_token"];
			$expire= $jsoninfo["expires_in"];
			$link=openDB();
			if($link)
			{	$nowtime=time();
				$sql = "insert into AccessToken(WXAccount,Token,getTime,expire) values('$WXAccount','$Token','$nowtime','$expire')";			
									
				$res = mysql_query($sql);
				if($res)
				 {									$a=0;//logger($WXAccount.'_getAccess_Token','log/','Debug'," insert:OK!");
				 }
				 else
				 { 									logger($WXAccount.'_getAccess_Token','log/','error',"Mysql-query error:".$sql.mysql_error());
				 }
				 mysql_close($link);	
			}
		}
		$mytime=time();
 		$replyStr=$Token; // json_encode($array);
						logger('CommonFunction','log/','Log',$thisFunction." postStr=".$postStr);
						logger('CommonFunction','log/','Log',$thisFunction." replyStr=".$replyStr);

		return  $Token;
	}



//数据库连接函数
	function openDB()
	{
		$mysql_servername = "qdm-012.hichina.com"; //主机地址
		$mysql_username = "qdm0120433"; //数据库用户名
		$mysql_password ="chengenfang19681010"; //数据库密码
		$mysql_database ="qdm0120433_db"; //数据库
		$link =mysql_connect($mysql_servername , $mysql_username , $mysql_password) or die(mysql_error());
		mysql_query("SET NAMES 'utf8'");
		if(mysql_select_db($mysql_database,$link))
		{    				
			   $retOpenDB=$link;
		 }
		 else
		 {	 
			  die(mysql_error());
			  $retOpenDB=$link;
		 }
		 return $retOpenDB;
	}

//日志函数
	function logger($ObjectName,$ObjectLogDir,$logType,$logMessage)
	{   //$ObjectName调用此函数的对象名名称
		//$logType，log文件类型（ERROR,DEBUG，INFO）
		//$logMessage，要写入的信息。 
		$nowTime=time();
		$logRearName=date('Ymd',$nowTime);
		$logFileName=$ObjectLogDir.$ObjectName."_".$logType."_".$logRearName.".log";
		
		file_put_contents($logFileName,date('Y-m-d H:i:s',$nowTime).',',FILE_APPEND);
		file_put_contents($logFileName,$logMessage,FILE_APPEND);
		file_put_contents($logFileName,"\r\n",FILE_APPEND);
	}
  
//编码转换
	function wphp_urlencode($data)
	{
		 if(is_array($data) || is_object($data))  
		 {
			 foreach ($data as $k => $v )
			 {
					if (is_scalar($v)) 
					{
						if (is_array($data))
						{
							$data[$k]=urlencode($v);
						}
						else if (is_object($data)) 
						{
							$data->$k=urlencode($v);			
						}
					}
					else if (is_array($data)) 
					{
						$data[$k]=wphp_urlencode($v);
					}
					else if (is_object($data)) 
					{
						$data->$k=wphp_urlencode($v);
					}
			}
		}
		return $data;
	}
	
	function ch_json_encode($data)
	{
		$ret=wphp_urlencode($data);
		$ret=json_encode($ret);
		return urldecode($ret);
	
	}	

//系统信息函数
	function getSERVERallMsg()
	{
		$allMsg='';
		$allMsg=$allMsg.','.$_SERVER['REMOTE_ADDR'];
		$allMsg=$allMsg.','.$_SERVER['QUERY_STRING'];
		$allMsg=$allMsg.$_SERVER['DOCUMENT_ROOT'];
		$allMsg=$allMsg.','.$_SERVER['GATEWAY_INTERFACE'];
		$allMsg=$allMsg.','.$_SERVER['HTTP_ACCEPT'];
		$allMsg=$allMsg.','.$_SERVER['HTTP_ACCEPT_CHARSET'];
		$allMsg=$allMsg.','.$_SERVER['HTTP_ACCEPT_ENCODING'];
		$allMsg=$allMsg.','.$_SERVER['HTTP_ACCEPT_LANGUAGE'];
		$allMsg=$allMsg.','.$_SERVER['HTTP_CONNECTION'];
		$allMsg=$allMsg.','.$_SERVER['HTTP_HOST'];
		$allMsg=$allMsg.','.$_SERVER['HTTP_REFERER'];
		$allMsg=$allMsg.','.$_SERVER['HTTP_USER_AGENT'];
		$allMsg=$allMsg.','.$_SERVER['PATH_TRANSLATED'];
		$allMsg=$allMsg.','.$_SERVER['PHP_SELF'];
		$allMsg=$allMsg.','.$_SERVER['REMOTE_PORT'];	
		$allMsg=$allMsg.','.$_SERVER['REQUEST_METHOD'];
		$allMsg=$allMsg.','.$_SERVER['REQUEST_URI'];
		$allMsg=$allMsg.','.$_SERVER['SCRIPT_FILENAME'];	
		$allMsg=$allMsg.','.$_SERVER['SCRIPT_NAME'];
		$allMsg=$allMsg.','.$_SERVER['SERVER_ADMIN'];
		$allMsg=$allMsg.','.$_SERVER['SERVER_NAME'];
		$allMsg=$allMsg.','.$_SERVER['SERVER_PORT'];
		$allMsg=$allMsg.','.$_SERVER['SERVER_PROTOCOL'];
		$allMsg=$allMsg.','.$_SERVER['SERVER_SIGNATURE'];
		$allMsg=$allMsg.','.$_SERVER['SERVER_SOFTWARE'];
		return $allMsg;
	}

?>