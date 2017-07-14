<?php
  	require_once(dirname(_FILE_).'../cgi-bin/CommonFunction.php');
	require_once(dirname(_FILE_).'../cgi-bin/CommonFunctionMusicTest.php');
	header("Content-type: text/html; charset=utf-8");
	ini_set('date.timezone','PRC'); //ÉèÖÃÊ±Çø
	$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
	$part=json_decode($postStr,TRUE);
	
	
	$typefile=$_POST["type"];
	$type=$part['type'];
//	echo $typefile."+".$type;
	if($type==0)
	{	$sql="select ME_MusicKnowledgePoint_ID,ChineseName from ME_MusicKnowledgePoint";
		$ret=runSelectSql($sql);
		$replyStr=json_encode($ret);
	
	}
	//上传学生名单
	if($type==1)	
	{	
		$Teacher_ID=$part['Teacher_ID'];$School_ID=$part['School_ID'];$term=$part['term'];$coursename=$part['coursename'];$studentlist=$part['studentlist'];
		$testdate=$part['testdate'];$testStartTime=$part['testStartTime'];$testEndTime=$part['testEndTime'];$mytime=$part['timestamp'];
		$studentlist = nl2br($studentlist);
		$studentArr = explode("<br />",$studentlist); 
		$studentNoPwd=array();
		$txt ="学年学期".$term."\r\n课程名称".$coursename."\r\n教师用户名".$Teacher_ID."\r\n---------------------\r\n";
		$txt =$txt."学号     密码\r\n";
		for($index=0;$index<count($studentArr);$index++) 
		{ 	
			$studentNoName=explode(' ',$studentArr[$index]); 
			$studentNoPwd[$index]['id']=str_replace("\n","",$studentNoName[0]);
			$studentNoPwd[$index]['pwd']=getRandNumberStr('',6,'');
			$oneNo=str_replace("\n","",$studentNoName[0]);
			$oneName=str_replace($oneNo,"",$studentArr[$index]);$oneName=str_replace("\n","",$oneName);$oneName=str_replace(" ","",$oneName);
			$onePwd=md5($studentNoPwd[$index]['pwd']);
			$mytime=time();
			$starttime=strtotime($testdate.' '.$testStartTime.':00:00');$endtime=strtotime($testdate.' '.$testEndTime.':00:00');
			$sql="DELETE FROM `ME_User` WHERE `School_ID` ='$School_ID' and `User_Type` ='s' and `User_ID` ='$oneNo'";
			$ret=runInsertUpdateDeleteSql($sql);
			$sql="insert into ME_User(School_ID,User_Type,User_ID,User_PWD,User_Name,User_ExpTimeStart,User_ExpTimeEnd,User_RegTime) values('$School_ID','s','$oneNo','$onePwd','$oneName','$starttime','$endtime','$mytime')";
			$ret=runInsertUpdateDeleteSql($sql);
			
			$sql="DELETE FROM `ME_STAssociate` WHERE `School_ID` ='$School_ID' and `Teacher_ID` ='$Teacher_ID' and `Student_ID` ='$oneNo' and Term='$term'";
			$ret=runInsertUpdateDeleteSql($sql);
			$sql="insert into ME_STAssociate(School_ID,Teacher_ID,Student_ID,Term,CourseName) values('$School_ID','$Teacher_ID','$oneNo','$term','$coursename')";
			$ret=runInsertUpdateDeleteSql($sql);
			
			$txt =$txt.$oneNo.'  '.$studentNoPwd[$index]['pwd']."\r\n";
		} 
		$myfilename='./student/'.$term.'_'.$School_ID.'_'.$Teacher_ID.'_'.$coursename.'_StudentPassWords.txt';
		
		
		// logger('me_teacher','log/','Log',"StudentPasswords=".$myfilename));
		$myfile = fopen($myfilename, "w");
		$txt =$txt."\r\n---------------------\r\n\r\n\r\n\r\n请复制粘贴，妥善保存信息！";
		// logger('me_teacher','log/','Log',"StudentPasswords=".$myfilename));		
		fwrite($myfile, $txt);
		fclose($myfile);
		//downloadFile($myfilename);
		$replyStr=$myfilename;
	}
	//上传标准文件
	if($typefile==2)
	{						
		logger('me_teacher','log/','Log',"postStr=".json_encode($_POST));
		$term2=$_POST["term2"];		$teacherid=$_POST["teacherid"];	$schoolid=$_POST["schoolid"];$checkbox=$_POST["KnowledgePointList"];
		$sql="select Term from ME_STAssociate where Teacher_ID='$teacherid' and School_ID='$schoolid' and Term='$term2'";
		$ret=runSelectSql($sql);
		$TermInTable=$ret[0]['Term']; 
		// echo json_encode($TermInTable);
		if(count($TermInTable)>0)
		{
			
			$sql="select XMLFileInfo_Times from ME_XMLFileInfo where User_ID='$teacherid' and School_ID='$schoolid' and Term='$term2' order by XMLFileInfo_Times desc limit 1";
			$ret=runSelectSql($sql);
			$XMLFileInfo_Times=$ret[0]['XMLFileInfo_Times']; 
			if(count($XMLFileInfo_Times)) $XMLFileInfo_Times=$XMLFileInfo_Times+1;
			else $XMLFileInfo_Times=1;

			if ($_FILES["file"]["error"]>0) 	{	echo "Error: " . $_FILES["file"]["error"] . "<br />"; 		}
			else
			{   $uploadFilename=$_FILES["file"]["name"];
				// echo $uploadFilename;
				$mytime=time();
				$tempname=(string)$XMLFileInfo_Times;
				if(strlen($tempname)==1) $tempname='00'.$tempname;
				if(strlen($tempname)==2) $tempname='0'.$tempname;
				$mypath='./xml/';
				$saveFileName=$term2.'_'.$schoolid.'_t_'.$teacherid.'_'.$tempname.'.xml';
				if(isMusicXMLFile($_FILES["file"]["tmp_name"])<1) {
				   echo "<script language=javascript>alert('不是XML/MusicXML¸文件!');history.back();</script>";	
				} 
				else
				{
					if(move_uploaded_file($_FILES["file"]["tmp_name"],$mypath.$saveFileName))
					{ 			
						if(file_exists($mypath.$saveFileName))
						{   
							//$xmlStr = file_get_contents($mypath.$saveFileName);//½«Õû¸öÎÄ¼þÄÚÈÝ¶ÁÈëµ½Ò»¸ö×Ö·û´®ÖÐ
							$KMPoint=json_encode($checkbox);
							$ret=runInsertUpdateDeleteSql("insert into ME_XMLFileInfo(School_ID,User_ID,Term ,XMLFileInfo_SaveName,XMLFileInfo_UploadName,XMLFileInfo_UploadTime,XMLFileInfo_Times,ME_MusicKnowledgePointList) values('$schoolid','$teacherid','$term2','$saveFileName','$uploadFilename','$mytime','$XMLFileInfo_Times','$KMPoint')");
											logger('me_teacher','log/','Log',"=".count($checkbox));logger('me_teacher','log/','Log',"=".json_encode($checkbox));
							$replyStr='';
							for($i=0;$i<count($checkbox);$i++)
							{	$ctag=$checkbox[$i];
								$sql="select TagName  from ME_MusicKnowledgePoint where ME_MusicKnowledgePoint_ID='$ctag'";
								$ret=runSelectSql($sql);$TagName=$ret[0]['TagName']; $childTagName= explode(',',$ret[0]['childTagName']);
													logger('me_teacher','log/','Log',"TagName=".$TagName.', child TagName='.json_encode($childTagName));
								$replyStr=$replyStr."\r\n".$TagName.'='.getTagInfoFromXMLFile($mypath.$saveFileName,$TagName);
								
												
							}
							
							 	logger('me_teacher','log/','Log','replyStr='.$replyStr);
							 	echo "<script language=javascript>alert('上传成功！');history.back();</script>";
						}
					}
					else 	{ echo "<script language=javascript>alert('ÎÄ¼þÉÏ´«Ê§°Ü!ÇëÁªÏµ¹ÜÀíÔ±£¡');history.back();</script>";} 
				}	
			}
		}
		else echo "<script language=javascript>alert('ÎÄ¼þÉÏ´«Ê§°Ü!ÇëÈ·¶¨¡°Ñ§ÄêÑ§ÆÚ¡±ÊÇ·ñÕýÈ·£¿');history.back();</script>";
	}
	
	if($type==5)	
	{	
		$Teacher_ID=$part['Teacher_ID'];$School_ID=$part['School_ID'];$term=$part['term'];		
		$xmlFileNameArray=array();
		$sql="select XMLFileInfo_SaveName,ME_MusicKnowledgePointList from ME_XMLFileInfo where  User_ID='$Teacher_ID' and School_ID='$School_ID' and Term='$term' order by XMLFileInfo_Times desc limit 1";
		$ret=runSelectSql($sql);$xmlFileNameArray[0]='./xml/'.$ret[0]['XMLFileInfo_SaveName'];$KMPoint=json_decode($ret[0]['ME_MusicKnowledgePointList']);
			logger('me_teacher','log/','Log','xmlFileNameArray='.$xmlFileNameArray[0]);
		$sql="select Student_ID from ME_STAssociate where  Teacher_ID='$Teacher_ID' and School_ID='$School_ID' and Term='$term'";
		$ret=runSelectSql($sql);$k=1;
		$havePaperStudentList=array();
		$havePaperStudentList[0][0]="No.";$havePaperStudentList[0][1]="Name";
		$havePaperStudentList[1][0]=$Teacher_ID;$havePaperStudentList[1][1]="Teacher";
		for($i=0;$i<count($ret);$i++)
		{	$oneStudentID=$ret[$i]['Student_ID'];
			$sql2="select XMLFileInfo_SaveName from ME_XMLFileInfo where  User_ID='$oneStudentID' and School_ID='$School_ID' and Term='$term' order by XMLFileInfo_Times desc limit 1";
			$ret2=runSelectSql($sql2);
			if(count($ret2[0]['XMLFileInfo_SaveName'])>0) 
			{ 	$xmlFileNameArray[$k]='./xml/'.$ret2[0]['XMLFileInfo_SaveName'];
				$sql3="select User_Name from ME_User where  User_ID='$oneStudentID' and School_ID='$School_ID' and User_Type='s'";
				$ret3=runSelectSql($sql3);
				$havePaperStudentList[$k+1][0]=$oneStudentID;$havePaperStudentList[$k+1][1]=$ret3[0]['User_Name'];
				$k++;
			}
		}
				logger('me_teacher','log/','Log','xmlFileNameArray='.json_encode($xmlFileNameArray));
				logger('me_teacher','log/','Log','havePaperStudentList='.json_encode($havePaperStudentList));
		$temp=json_decode(getAllTagStatInfoForXMLFileArray($xmlFileNameArray,$KMPoint));
		//start:Òô·ûÐýÂÉ±È½Ï
		$teacherXMLFile=$xmlFileNameArray[0];
		$childTagArr[0]='pitch';$childTagArr[1]='duration';
		$note=getValueArryFromXMLFileByTag($teacherXMLFile,'note',$childTagArr,2);
		$teacherNote=str_replace(" ","",json_encode($note));$tmpNote=json_decode($teacherNote);
		$tmpTeacherDuration='';
		for($i=0;$i<count($tmpNote);$i++)
		{
			$tmpTeacherDuration=$tmpTeacherDuration.$tmpNote[$i][1];
		}
						logger('me_teacher','log/','Log','teacherNote='.count(json_decode($teacherNote)));
						logger('me_teacher','log/','Log','teacherNote='.$teacherNote);
						logger('me_teacher','log/','Log','tmpTeacherDuration='.$tmpTeacherDuration);
		$pitch=getValueArryFromXMLFileByOneTag($teacherXMLFile,'pitch');
		$teacherPitch=str_replace(" ","",json_encode($pitch));
						logger('me_teacher','log/','Log','teacherPitch='.count($pitch));
						logger('me_teacher','log/','Log','TeacherPitch='.$teacherPitch);
						
		$durationCompareResult=array();		$durationCompareResult[0]="Duration";$durationCompareResult[1]=count(json_decode($teacherNote));
		$pitchCompareResult=array();		$pitchCompareResult[0]="Pitch";$pitchCompareResult[1]=count(json_decode($teacherPitch));
		for($i=1;$i<count($xmlFileNameArray);$i++)
		{
			$studentXMLFile=$xmlFileNameArray[0];
			$childTagArr[0]='pitch';$childTagArr[1]='duration';
			$pitch=getValueArryFromXMLFileByTag($studentXMLFile,'note',$childTagArr,2);
			$studentPitch=str_replace(" ","",json_encode($pitch));$tmpPitch=json_decode($studentPitch);
			$tmpStudentDuration='';
			for($j=0;$j<count($tmpPitch);$j++)
			{
				$tmpStudentDuration=$tmpStudentDuration.$tmpPitch[$j][1];
			}
			$durationCompareResult[$i+1]=LengthOfLCS($tmpTeacherDuration,$tmpStudentDuration);
			
			$pitch=getValueArryFromXMLFileByOneTag($studentXMLFile,'pitch');
			$studentPitch=str_replace(" ","",json_encode($pitch));
			$pitchCompareResult[$i+1]=LengthOfLCSforTwoArray($teacherPitch,$studentPitch);
								logger('me_teacher','log/','Log','tmpStudentPitch='.$studentPitch);
								logger('me_teacher','log/','Log','tmpStudentPitch='.strlen($studentPitch));
		}
								logger('me_teacher','log/','Log','durationCompareResult='.json_encode($durationCompareResult));
								logger('me_teacher','log/','Log','pitchCompareResult='.json_encode($pitchCompareResult));
		
		
		//end:Òô·ûË³Ðò±È½Ï
		$allInfo=array();$ttemp=array();
		for($i=0;$i<count($temp);$i++)
		{	$allInfo[$i][0]=$havePaperStudentList[$i][0];$allInfo[$i][1]=$havePaperStudentList[$i][1];
			for($j=0;$j<count($temp[$i]);$j++)	{	$allInfo[$i][$j+2]=$temp[$i][$j];	$ttemp[$i][$j]=$temp[$i][$j];}
			$allInfo[$i][count($temp[$i])+2]=$durationCompareResult[$i];$allInfo[$i][count($temp[$i])+3]=$pitchCompareResult[$i];
			$ttemp[$i][count($temp[$i])]=$durationCompareResult[$i];$ttemp[$i][count($temp[$i])+1]=$pitchCompareResult[$i];
		}
								logger('me_teacher','log/','Log','allInfo='.json_encode($allInfo));
		for($i=2;$i<count($temp);$i++) //¸üÐÂÑ§ÉúXMLÎÄ¼þÐÅÏ¢±íµÄME_MusicKnowledgePointList×Ö¶Î£¬´æÈëÍ³¼ÆÐÅÏ¢
		{
			$oneStudentID=$allInfo[$i][0];$updateContent=json_encode($ttemp[$i]);$XMLFileInfo_SaveName=substr($xmlFileNameArray[$i-1],6);
						logger('me_teacher','log/','Log','oneStudentID='.$oneStudentID.$updateContent.$XMLFileInfo_SaveName);	
			$sql="update ME_XMLFileInfo set ME_MusicKnowledgePointList='$updateContent' where  User_ID='$oneStudentID' and School_ID='$School_ID' and Term='$term'  and  XMLFileInfo_SaveName='$XMLFileInfo_SaveName'";
			$ret=runInsertUpdateDeleteSql($sql);
		}
		$replyStr=json_encode($allInfo);
		$myfilename='./student/'.$term.'_'.$School_ID.'_'.$Teacher_ID.'_StudentPaperCompareResult.txt';
		$myfile = fopen($myfilename, "w");
		fwrite($myfile, $replyStr);
		fclose($myfile);
		//$replyStr=json_encode($xmlFileNameArray);
	}
	
	if($type==8)	
	{
		$Teacher_ID=$part['Teacher_ID'];$School_ID=$part['School_ID'];$term=$part['term'];		
		$myfilename='./student/'.$term.'_'.$School_ID.'_'.$Teacher_ID.'_StudentPaperCompareResult.txt';
		$replyStr = file_get_contents($myfilename);
		
		
	}
	//保存成绩
	if($type==9)	
	{
		$StudentScore=json_decode($part['StudentScore']);
		for($i=0;$i<count($StudentScore);$i++)
		{	$Student_ID=$StudentScore[$i][1];$term=$StudentScore[$i][2];$School_ID=$StudentScore[$i][0];$Score=$StudentScore[$i][3];
			$sql="select Score from ME_Score where  Student_ID='$Student_ID' and School_ID='$School_ID' and Term='$term'";
			$ret=runSelectSql($sql);
			if(count($ret)>0) 
			{	$sql2="update ME_Score set Score='$Score' where Student_ID='$Student_ID' and School_ID='$School_ID' and Term='$term'";				
			}
			else
			{
			$sql2="insert into ME_Score(School_ID,Student_ID,Term,Score) values('$School_ID','$Student_ID','$term','$Score')";
			}
			$ret2=runInsertUpdateDeleteSql($sql2);
		}
		if($ret2)	$replyStr = 1;
		else $replyStr =0;
	}
	//上传个别同学试卷
	if($typefile==10){
		$studentid=$_POST["studentid"];
		if($studentid==''){
			echo "<script language=javascript>alert('请输入学生学号');history.back();</script>";	
		}
		$schoolid=$_POST["schoolid"];
		echo $schoolid;
		$sql="select Term from ME_STAssociate where School_ID='$schoolid' and Student_ID='$studentid'";
		$ret=runSelectSql($sql);
		$term2=$ret[0]['Term'];
		echo $term2;
	 	$sql="select XMLFileInfo_Times from ME_XMLFileInfo where User_ID='$studentid' and School_ID='$schoolid' and Term='$term2' order by XMLFileInfo_Times desc limit 1";
		$ret=runSelectSql($sql);
		$XMLFileInfo_Times=$ret[0]['XMLFileInfo_Times']; 
		if(count($XMLFileInfo_Times)) 
			$XMLFileInfo_Times=$XMLFileInfo_Times+1;
		else 
			$XMLFileInfo_Times=1;
			
		if ($_FILES["file"]["error"]>0) 	{	
			echo "Error: " . $_FILES["file"]["error"] . "<br />"; 		
		}
		else
		{   $uploadFilename=$_FILES["file"]["name"];
			echo $uploadFilename;
			$mytime=time();
			$tempname=(string)$XMLFileInfo_Times;
			if(strlen($tempname)==1) $tempname='00'.$tempname;
			if(strlen($tempname)==2) $tempname='0'.$tempname;
			$mypath='./xml/';
			$saveFileName=$term2.'_'.$schoolid.'_s_'.$studentid.'_'.$tempname.'.xml';
 			if(isMusicXMLFile($_FILES["file"]["tmp_name"])<1) {
 				// $compareResult=LengthOfATagCompareInTwoMusicXMLFiles('note','./xml/sn2.xml','./xml/sn21.xml');
 				echo "<script language=javascript>alert('$compareResult');</script>";
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
						echo "<script language=javascript>alert('�ļ��ϴ��ɹ�!');history.back();</script>";	
					}
				}
				else 	{ echo "<script language=javascript>alert('�ļ��ϴ�ʧ��!����ϵ����Ա��');history.back();</script>";} 
			}	
		}
	}
	//成绩查看
	if($type == 11){
		$Teacher_ID=$part['Teacher_ID'];$School_ID=$part['School_ID'];$term=$part['term'];
		$sql = "select Student_ID,Score,User_Name from (ME_Score join ME_User on ME_Score.School_ID = ME_User.School_ID) where ME_User.User_ID = ME_Score.Student_ID and ME_Score.School_ID='$School_ID' and ME_Score.Term='$term'";
		//$sql = "select Student_ID,Score from ME_Score where School_ID='$School_ID' and Term='$term'";
		$ret = runSelectSql($sql);
		$replyStr = json_encode($ret);
	}
	echo $replyStr;
?>
