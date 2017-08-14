<?php
  	require_once(dirname(_FILE_).'../cgi-bin/CommonFunction.php');
	require_once(dirname(_FILE_).'../cgi-bin/CommonFunctionMusicTest.php');
	header("Content-type: text/html; charset=utf-8");
	
	if ($_FILES["file"]["error"]>0){	
		echo "<script language=javascript>alert('Error: " . $_FILES["file"]["error"] . "');history.back();</script>"; 		
	}
	else{   
		$uploadFilename=$_FILES["file1"]["name"];
		$uploadFilename2=$_FILES["file2"]["name"];
		$tag = $_POST['tag'];
		$mypath='./xml/';
		$saveFileName = time()."_".$tag."_file";
		$saveFileName2 = time()."_".$tag."_file2";
		if(isMusicXMLFile($_FILES["file1"]["tmp_name"])<1 || isMusicXMLFile($_FILES["file2"]["tmp_name"])<1) {
			echo "<script language=javascript>alert('上传文件有不是XML/MusicXML的文件!');history.back();</script>";	
		} 
		else{
			if(move_uploaded_file($_FILES["file1"]["tmp_name"],$mypath.$saveFileName) && move_uploaded_file($_FILES["file2"]["tmp_name"],$mypath.$saveFileName2)){ 			
				if(file_exists($mypath.$saveFileName) && file_exists($mypath.$saveFileName2)){
					$compareResult1=LengthOfATagCompareInTwoMusicXMLFiles($tag,$mypath.$saveFileName,$mypath.$saveFileName2);
//					$compareResult=getTagStatInfoFromXMLFile($mypath.$saveFileName,$tag);
					$replyStr = "相同标签个数为：".$compareResult1;
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