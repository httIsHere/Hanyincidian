<?php
  	require_once(dirname(_FILE_).'../cgi-bin/CommonFunction.php');
	require_once(dirname(_FILE_).'../cgi-bin/CommonFunctionMusicTest.php');
	header("Content-type: text/html; charset=utf-8"); 
	ini_set('date.timezone','PRC'); //����ʱ��
	$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
	$part=json_decode($postStr,TRUE);
	$typeUploadFile=$_POST["type"];
	$type=$part['type'];
		// echo $_FILES['file']['error'];
	//get Mac Address
class GetMacAddr{
    public $returnArray = array();
    public $macAddr;

    function GetMacAddr($os_type=null){
        if(is_null($os_type)) 
            $os_type = PHP_OS;
        switch (strtolower($os_type)){
        case "linux":
            $this->forLinux();
            break;
        case "solaris":
            break;
        case "unix":
            break;
        case "aix":
            break;
        default:
            $this->forWindows();
            break;
        }
        $temp_array = array();
        foreach($this->returnArray as $value ){
            if(preg_match("/[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f]/i", $value, $temp_array)){
                $this->macAddr = $temp_array[0];
                break;
            }
        }
        unset($temp_array);
        return $this->macAddr;
    }

    function forWindows(){
        @exec("ipconfig /all", $this->returnArray);
        if($this->returnArray)
            return $this->returnArray;
        else{
            $ipconfig = $_SERVER["WINDIR"]."system32ipconfig.exe";
            if (is_file($ipconfig))
                @exec($ipconfig." /all", $this->returnArray);
            else
                @exec($_SERVER["WINDIR"]."systemipconfig.exe /all", $this->returnArray);
            return $this->returnArray;
        }
    }

    function forLinux(){
        @exec("ifconfig -a", $this->returnArray);
        return $this->returnArray;
    }
}	
	
	
	if($typeUploadFile==1)
	{
		$studentid=$_POST["studentid"];
		$schoolid=$_POST["schoolid"];
		// echo $studentid.$schoolid;
		$sql="select Term from ME_STAssociate where School_ID='$schoolid' and Student_ID='$studentid'";
		$ret=runSelectSql($sql);
		$term2=$ret[0]['Term'];
		// echo $term2;
	 	$sql="select XMLFileInfo_Times from ME_XMLFileInfo where User_ID='$studentid' and School_ID='$schoolid' and Term='$term2' order by XMLFileInfo_Times desc limit 1";
		$ret=runSelectSql($sql);
		$XMLFileInfo_Times=$ret[0]['XMLFileInfo_Times']; 
		// echo count($XMLFileInfo_Times);
		if(count($XMLFileInfo_Times)) 
			$XMLFileInfo_Times=$XMLFileInfo_Times+1;
		else 
			$XMLFileInfo_Times=1;
			
		if ($_FILES["file"]["error"]>0) 	{	
			echo "Error: " . $_FILES["file"]["error"] . "<br />"; 		
		}
		else
		{   
			$uploadFilename=$_FILES["file"]["name"];
//			echo $uploadFilename;
			$mytime=time();
			$tempname=(string)$XMLFileInfo_Times;
			if(strlen($tempname)==1) $tempname='00'.$tempname;
			if(strlen($tempname)==2) $tempname='0'.$tempname;
			$mypath='./xml/';
			$saveFileName=$term2.'_'.$schoolid.'_s_'.$studentid.'_'.$tempname.'.xml';
//			echo $_FILES["file"];
 			if(isMusicXMLFile($_FILES["file"]["tmp_name"])<1) {
 				// $compareResult=LengthOfATagCompareInTwoMusicXMLFiles('note','./xml/sn2.xml','./xml/sn21.xml');
 				// echo "<script language=javascript>alert('$compareResult');</script>";
// 				echo $_FILES["file"]["tmp_name"];
			   	echo "<script language=javascript>alert('文件不是XML/MusicXML格式!');history.back();</script>";	
			} 
			else
			{
				if(move_uploaded_file($_FILES["file"]["tmp_name"],$mypath.$saveFileName))
				{ 			
					if(file_exists($mypath.$saveFileName))
					{   
						$compareResult=LengthOfATagCompareInTwoMusicXMLFiles('note','./xml/sn2.xml','./xml/sn2.xml');
 						$ret=runInsertUpdateDeleteSql("insert into ME_XMLFileInfo(School_ID,User_ID,Term ,XMLFileInfo_SaveName,XMLFileInfo_UploadName,XMLFileInfo_UploadTime,XMLFileInfo_Times) values('$schoolid','$studentid','$term2','$saveFileName','$uploadFilename','$mytime','$XMLFileInfo_Times')");
						echo "<script language=javascript>alert('上传成功!');history.back();</script>";	
					}
				}
				else 	{ echo "<script language=javascript>alert('�ļ��ϴ�ʧ��!����ϵ����Ա��');history.back();</script>";} 
			}	
		}
 	}
	//记录登录机器(而且只记录一次，先比较然后再保存)
	if($_POST['type'] == 2){
		//方法使用
		$mac = new GetMacAddr(PHP_OS);
		echo 'my mac address: '.$mac->macAddr;
	}
	
/*	
	if($type==2) //��ѯ�ɼ�
	{
	
	}
	if($type==3) //�޸�����
	{
		$School_ID=$part["School_ID"];$Student_ID=$part["Student_ID"];$oldPwd=$part["oldPwd"];$newPwd1=$part["newPwd1"];$newPwd2=$part["newPwd2"];
		if(empty($newPwd1)||empty($newPwd2)||empty($oldPwd)) {	$replyStr=0;}
		else
		{    $replyStr=11;
			//$sql="select User_ID,User_PWD from ME_User where School_ID='$School_ID' and User_Type='$persontype' and User_ID='$username' and User_PWD='$pwd' ";
			//$ret=runSelectSql($sql);
			//$replyStr=count($ret);
			//$replyStr=md5($ret);
		}
	}
	
	 */
	echo $replyStr;
?>
