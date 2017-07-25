<?php
  	require_once(dirname(_FILE_).'../cgi-bin/CommonFunction.php');
	require_once(dirname(_FILE_).'../cgi-bin/CommonFunctionMusicTest.php');
	header("Content-type: text/html; charset=utf-8");
	
	if ($_FILES["file"]["error"]>0){	
		echo "<script language=javascript>alert('Error: " . $_FILES["file"]["error"] . "');history.back();</script>"; 		
	}
	else{   
		$uploadFilename=$_FILES["file"]["name"];
		$tag = $_POST['tag'];
		$mypath='./xml/';
		$saveFileName = time()."_".$tag."_file";
		if(isMusicXMLFile($_FILES["file"]["tmp_name"])<1) {
			echo "<script language=javascript>alert('不是XML/MusicXML¸文件!');history.back();</script>";	
		} 
		else{
			if(move_uploaded_file($_FILES["file"]["tmp_name"],$mypath.$saveFileName)){ 			
				if(file_exists($mypath.$saveFileName)){
//					$compareResult1=LengthOfATagCompareInTwoMusicXMLFiles('note','./xml/sn2.xml','./xml/sn21.xml');
					$compareResult=getTagStatInfoFromXMLFile($mypath.$saveFileName,$tag);
					$replyStr = "标签出现次数为：".$compareResult;
   					echo "<script language=javascript>alert('$replyStr');history.back();</script>";
				}
				else{
					echo "<script language=javascript>alert('文件不存在！');history.back();</script>";
				}
			} 
			else{
				echo "<script language=javascript>alert('文件保存失败！');history.back();</script>";
			} 
		}
	}
?>