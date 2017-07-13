// JavaScript Document

/*document.write(”<script language=javascript src=’/js/import.js’></script>”);*/
/*var arr = new Array('1', '2', '3');  
if (!Array.indexOf) {  
    Array.prototype.indexOf = function (obj) {  
        for (var i = 0; i < this.length; i++) {  
            if (this[i] == obj) {  
                return i;  
            }  
        }  
        return -1;  
    }  
}  
var index = arr.indexOf('1');//为index赋值为0
可以用数组的indexOf函数,方法arr.indexOf(find,start);find:要找的内容,必须;start:查找开始下标,可选;返回:查找数据所在的下标,如果没找到,返回-1
function contains(arr, obj) {  
    var i = arr.length;  
    while (i--) {  
        if (arr[i] === obj) {  
            return true;  
        }  
    }  
    return false;  
}  
contains(arr, 2);//返回true  
contains(arr, 4);//返回false  
*/
//arr.sort(function(a,b){return a>b?1:-1});

	//beMusic留言信息插入，全部读取，按用户删除
	function insertMsgBeMusic(xmlDoc,callbackFunction)
	{	
		try {
			var db = window.openDatabase('beMusicMsg','1.0', 'beMusicMsg DB', 2 * 1024 * 1024); 
		}catch(e) {callbackFunction(0);}		
		db.transaction(function (tx) { 
			try{
				tx.executeSql('CREATE TABLE IF NOT EXISTS beMusicMsg(FromUserID,ToUserID,Message,SendTime)'); 
			}catch(e) { callbackFunction(0);}
			var field=eval(xmlDoc);
			for(var i=0;i<field.length;i++)
			{
				try{
					tx.executeSql('INSERT INTO beMusicMsg(FromUserID,ToUserID,Message,SendTime) VALUES (?,?,?,?)',[field[i].FromUserID,field[i].ToUserID,field[i].Message,field[i].SendTime]);  
				}catch(e) { callbackFunction(0);}
			} 
		   callbackFunction(1);
		});
	}
	
	function selectMsgBeMusic(callbackFunction)
	{
		try {
			var db = window.openDatabase('beMusicMsg','1.0', 'beMusicMsg DB', 2 * 1024 * 1024); 
		}catch(e) {callbackFunction("");}
		
		db.transaction(function (tx) { 
		   	tx.executeSql('SELECT * FROM beMusicMsg',[], function (tx, results){ 
				var len = results.rows.length; 
				var ret={};   var msg="[";
				for(i = 0; i < len; i++){ 
					ret['FromUserID']=results.rows.item(i).FromUserID;	ret["ToUserID"]=results.rows.item(i).ToUserID;
					ret['Message']=results.rows.item(i).Message;	ret['SendTime']=results.rows.item(i).SendTime;
					msg=msg+JSON.stringify(ret);		if( i < len-1)  msg=msg+",";
				} 
				msg=msg+"]";//alert("msg="+msg);
				callbackFunction(msg);
			  }, function(){  
					callbackFunction("");  
			}); 
	 	}); 
	}
	function deleteMsgBeMusic(toUserName,callbackFunction)
	{	 
		try {
			var db = window.openDatabase('beMusicMsg','1.0', 'beMusicMsg DB', 2 * 1024 * 1024); 
		}catch(e) {callbackFunction(0);}		
		db.transaction(function (tx) { 
				try{
					tx.executeSql('delete from beMusicMsg where FromUserID=? or ToUserID=?',[toUserName,toUserName]); 
				}catch(e) { callbackFunction(0);}
				callbackFunction(1);
		});//delete 
	}
	//beMusic搜索用户数据插入，读取，删除OpenID,nickname,headimgurl
	function insertSearchUserBeMusic(xmlDoc,callbackFunction)
	{	//alert(xmlDoc);
		try {
			var db = window.openDatabase('beMusicSearchUser','1.0', 'beMusicSearchUser DB', 2 * 1024 * 1024); 
		}catch(e) {callbackFunction(0);}		
		db.transaction(function (tx) { 
			try{
				tx.executeSql('CREATE TABLE IF NOT EXISTS beMusicSearchUser(OpenID,nickname,headimgurl)'); 
			}catch(e) { callbackFunction(0);}
			var field=eval(xmlDoc);
			for(var i=0;i<field.length;i++)
			{
				try{
					tx.executeSql('INSERT INTO beMusicSearchUser(OpenID,nickname,headimgurl) VALUES (?,?,?)',[field[i].OpenID,field[i].nickname,field[i].headimgurl]);  
				}catch(e) { callbackFunction(0);}
			} 
		   callbackFunction(1);
		});
	}
	
	function selectSearchUserBeMusic(callbackFunction)
	{
		try {
			var db = window.openDatabase('beMusicSearchUser','1.0', 'beMusicSearchUser DB', 2 * 1024 * 1024); 
		}catch(e) {callbackFunction("");}
		
		db.transaction(function (tx) { 
		   	tx.executeSql('SELECT * FROM beMusicSearchUser',[], function (tx, results){ 
				var len = results.rows.length; 
				var ret={};   var msg="[";
				for(i = 0; i < len; i++){ 
					ret['OpenID']=results.rows.item(i).OpenID;	ret["nickname"]=results.rows.item(i).nickname;	ret['headimgurl']=results.rows.item(i).headimgurl;	
					msg=msg+JSON.stringify(ret);		if( i < len-1)  msg=msg+",";
				} 
				msg=msg+"]";//alert("msg="+msg);
				callbackFunction(msg);
			  }, function(){  
					callbackFunction(""); 
			}); 
	 	}); 
	}
	function deleteSearchUserBeMusic(callbackFunction)
	{
		try {
			var db = window.openDatabase('beMusicSearchUser','1.0', 'beMusicSearchUser DB', 2 * 1024 * 1024); 
		}catch(e) {callbackFunction(0);}		
		db.transaction(function (tx) { 
				try{
					tx.executeSql('delete from beMusicSearchUser',[]); 
				}catch(e) { callbackFunction(0);}
				callbackFunction(1);
		});//delete 
	}
	//beMusic同城用户数据插入，读取，删除OpenID,nickname,headimgurl
	function insertCityUserBeMusic(xmlDoc,callbackFunction)
	{	//alert(xmlDoc);
		try {
			var db = window.openDatabase('beMusicCityUser','1.0', 'beMusicCityUser DB', 2 * 1024 * 1024); 
		}catch(e) {callbackFunction(0);}		
		db.transaction(function (tx) { 
			try{
				tx.executeSql('CREATE TABLE IF NOT EXISTS beMusicCityUser(OpenID,nickname,headimgurl)'); 
			}catch(e) { callbackFunction(0);}
			var field=eval(xmlDoc);
			for(var i=0;i<field.length;i++)
			{
				try{
					tx.executeSql('INSERT INTO beMusicCityUser(OpenID,nickname,headimgurl) VALUES (?,?,?)',[field[i].OpenID,field[i].nickname,field[i].headimgurl]);  
				}catch(e) { callbackFunction(0);}
			} 
		   callbackFunction(1);
		});
	}
	
	function selectCityUserBeMusic(callbackFunction)
	{
		try {
			var db = window.openDatabase('beMusicCityUser','1.0', 'beMusicCityUser DB', 2 * 1024 * 1024); 
		}catch(e) {callbackFunction("");}
		
		db.transaction(function (tx) { 
		   	tx.executeSql('SELECT * FROM beMusicCityUser',[], function (tx, results){ 
				var len = results.rows.length; 
				var ret={};   var msg="[";
				for(i = 0; i < len; i++){ 
					ret['OpenID']=results.rows.item(i).OpenID;	ret["nickname"]=results.rows.item(i).nickname;	ret['headimgurl']=results.rows.item(i).headimgurl;	
					msg=msg+JSON.stringify(ret);		if( i < len-1)  msg=msg+",";
				} 
				msg=msg+"]";//alert("msg="+msg);
				callbackFunction(msg);
			  }, function(){  
					callbackFunction(""); 
			}); 
	 	}); 
	}
	function deleteCityUserBeMusic(callbackFunction)
	{
		try {
			var db = window.openDatabase('beMusicCityUser','1.0', 'beMusicCityUser DB', 2 * 1024 * 1024); 
		}catch(e) {callbackFunction(0);}		
		db.transaction(function (tx) { 
				try{
					tx.executeSql('delete from beMusicCityUser',[]); 
				}catch(e) { callbackFunction(0);}
				callbackFunction(1);
		});//delete 
	}
	//计算地球两个坐标间的距离
	function getDistInTwoPoints(aX,aY,bX,bY)//X纬度，Y经度,得到两个点的距离
	{    
		OneDistLat=111166;//1个纬度差的距离：米				
		diffX=Math.abs(aX-bX);diffY=Math.abs(aY-bY);pulsX=aX+bX; 	
		cosValue=Math.cos(Math.PI*pulsX/360);			
		distX=OneDistLat*diffX;
		distY=OneDistLat*diffY*cosValue;
		dist=Math.sqrt(distX*distX+distY*distY);	
		return Math.floor(dist);
	}
//更新用户
/*
	function beMusicUserUpdate()
	{	alert("beMusicUserUpdate"+fromOpenID);
		
		byUrl="/beMusic/bM_UserUpdate.php";
		var xmlDoc;
		getxmlDoc(byUrl,fromOpenID,function(xmlDoc) {
					aaaa=1;
		});
	}
*/
	function checkAccessID(fromOpenID,accessID,callbackFunction)
	{	var pDate={};
		pDate['fromOpenID']=fromOpenID;pDate['accessID']=accessID;		
		postDate=JSON.stringify(pDate);
		getxmlDoc("/bemusic/checkAccessID.php",postDate,function(xmlDoc){
				callbackFunction(xmlDoc);
		});
		
	}
//js获取url传递参数
	 function getWebArguments() {
	   var url = window.location.search; //获取url中"?"符后的字串
	   var Arguments = new Array();//field=xmlDoc.split(';');var subfield=field;	var option=subfield[0].split("`");TotalTerms=option[0];
	   var field=url.split('&');
	   for(var i=0;i<field.length;i++)
	   {	var option=field[i].split("=");
		   	Arguments[i]=option[1];
	   }
	   return Arguments;
	}
	

	function GlobalCloseDIV(myDIV)
	{document.getElementById(myDIV).style.display="none";	}

	
	//Cookie

	
	function getCookie(objName){//获取指定名称的cookie的值
		//alert(document.cookie);
		var arrStr = document.cookie.split("; ");
		for(var i = 0;i < arrStr.length;i ++){
			var temp = arrStr[i].split("=");
			if(temp[0] == objName) return unescape(temp[1]);
		}
	}
	
	function delCookie(name){//为了删除指定名称的cookie，可以将其过期时间设定为一个过去的时间
		var date = new Date();
		date.setTime(date.getTime() - 10000);
		document.cookie = name + "=a; expires=" + date.toGMTString()+ "; path=/";
	}
	
	function allCookie(){//读取所有保存的cookie字符串
		var str = document.cookie;
		if(str == ""){
		str = "没有保存任何cookie";
		}
		//alert(str);
	}

	
	function setCookie(objName,objValue,objHours){//添加cookie
		var str = objName + "=" + escape(objValue);
		if(objHours > 0){//为0时不设定过期时间，浏览器关闭时cookie自动消失
			var date = new Date();
			var ms = objHours*3600*1000;
			date.setTime(date.getTime() + ms);
			str += "; expires=" + date.toGMTString();
		}
		str += "; path=/";  //红色标记必须加上(之前漏写就出现了问题)
		document.cookie = str;
		//alert("添加cookie成功");
	}
	function getMyID(callbackFunction)
	{
		un=getCookie('gotoMusicUserID');//alert(un);
		if(un!=null && un!="" && un.length==28) 	{ callbackFunction(un);	}
		else
		{	var xmlDoc;
			getxmlDoc("/gotoMusic/gM_GetNewUserID.php",un,function(xmlDoc){
				setCookie('gotoMusicUserID',xmlDoc,36500);
				callbackFunction(xmlDoc);
			});
		}
	}
	
	function getDateTimeString(timeStamp) //把时间戳转换为字符串，如果是今天则返回“时间”，如果是昨天则返回“昨天时间”，其他返回“日期时间”
	{
		var myDate=new Date(parseInt(timeStamp)*1000);
		var todayZero=new Date(new Date().toLocaleDateString()).getTime();
		var oneDayMiMS=24*3600*1000;
		var tStamp=parseInt(timeStamp)*1000;
		var tMin;
		if(myDate.getMinutes()>10) tMin=myDate.getMinutes().toString();
		else tMin="0"+myDate.getMinutes().toString();
		if(tStamp>=todayZero)
		{	//alert("today");
			return myDate.getHours()+":"+tMin;
		}
		else 
		{	if(tStamp>(todayZero-oneDayMiMS))
			{	//alert("lastday");
				return "昨天"+myDate.getHours()+":"+tMin;
			}
			else
			{	//alert("other day");
				mymonth=myDate.getMonth()+1;
				tDate=myDate.getDate().toString();
				if(myDate.getDate()<10) tDate="0"+tDate;
				return mymonth+"月"+tDate+"日 "+myDate.getHours()+":"+tMin;
			}
		}
	}
	function getDateTimeStringForList(timeStamp) //把时间戳转换为字符串，如果是今天则返回“时间”，如果是昨天则返回“昨天时间”，其他返回“日期时间”
	{
		var myDate=new Date(parseInt(timeStamp)*1000);
		var todayZero=new Date(new Date().toLocaleDateString()).getTime();
		var oneDayMiMS=24*3600*1000;
		var tStamp=parseInt(timeStamp)*1000;
		var tMin;
		if(myDate.getMinutes()>=10) tMin=myDate.getMinutes().toString();
		else tMin="0"+myDate.getMinutes().toString();
		if(tStamp>=todayZero)
		{	//alert("today");
			return myDate.getHours()+":"+tMin;
		}
		else 
		{	if(tStamp>(todayZero-oneDayMiMS))
			{	//alert("lastday");
				return "昨天";
			}
			else
			{	//alert("other day");
				mymonth=myDate.getMonth()+1;
				tDate=myDate.getDate().toString();
				if(myDate.getDate()<10) tDate="0"+tDate;
				return mymonth+"月"+tDate+"日 ";
			}
		}
	}
	function getxmlDoc(ByUrl,postDate,callbackFunction)
	{	
		var xmlhttp
		if (window.XMLHttpRequest)	  {  xmlhttp=new XMLHttpRequest();		  }	// code for IE7+, Firefox, Chrome, Opera, Safari
		else  {xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	  }	// code for IE6, IE5  
		xmlhttp.onreadystatechange =function () 
		{	if(xmlhttp.readyState==4)
			{								//alert("xmlhttp.readyState="+xmlhttp.readyState);
				if(xmlhttp.status==200 ||xmlhttp.status==0)//
				{ 	var xmlDoc=xmlhttp.responseText;//alert("xmlDoc="+xmlDoc);
					callbackFunction(xmlDoc);
				}
			}		
		}		
		xmlhttp.open("POST",ByUrl,true);//true 表示异步
		xmlhttp.send(postDate);
	}

	
/*	
	//循环执行，每隔3秒钟执行一次 showalert（） 
	window.setInterval(function(){ 
		showalert(“aaaaa”); 
	}, 3000); 
	function showalert(mess) 
	{ 
		alert(mess); 
	} 
	
	function getLXY()
	{
		if(window.navigator.geolocation)
		{
			var geolcation=window.navigotor.geolocation;
			geolocation.getCurrentPosition(getPositionSuccess);
			return "";
		}
		else
		{
			return "";	
		}
	}
	function getCurrentPosition(position)
	{
		var lat=position.coords.latitude;
		var lon=position.coords.longitude;
		alert(String(lat)+","+String(lon));
		//return String(lat)+","+String(lon);
	}*/
	
	
	/*	
	function setCookie(c_name,value,expiredays)
	{
		var exdate=new Date()
		exdate.setDate(exdate.getDate()+expiredays)
		document.cookie=c_name+ "=" +escape(value)+
		((expiredays==null) ? "" : ";expires="+exdate.toGMTString())
	}
	



<a href="javascript:history.back()">回退到1.html，js动态生成内容之后</a>
onclick="location.href='javascript:history.go(-1);'"//怎么返回上一个页面
1、返回按钮使用window.history.back(-1)，这样是回到浏览器的记忆堆中的上一个页面，可以保留数据
2、在原来的页面中点击链接的时候可以重新打开一个窗口，可以用window.open()，或者window.showModelDialog()，又或者使用自定义弹出层，覆盖到你的页面上，下面放个半透明层，解决方案很多，至于使用什么好看你具体情况了

	
	
	window.onbeforeunload = function() 
	{ 
		setCookie('friendsSearchTxt',"",-1);
		return confirm("你确定退出吗？？？"); 
	} 
	
	
	function getCookie(c_name)
	{
		if (document.cookie.length>0)
		{
		  c_start=document.cookie.indexOf(c_name + "=")
		  if (c_start!=-1)
		  { 
				c_start=c_start + c_name.length+1 
				c_end=document.cookie.indexOf(";",c_start)
				if (c_end==-1) c_end=document.cookie.length
				return unescape(document.cookie.substring(c_start,c_end))
		  } 
		}
		return ""
	}
	
	
	
	function checkCookie(varUser)
	{
		username=getCookie(varUser)
		if (username!=null && username!="")
		{alert('Welcome again '+username)}
		else 
		{
			ByUrl="/cgi-bin/getOneNewWebpageUserIDandPwd.php"
			 username=PostDataByURL("",ByUrl)
			 //username=prompt('Please enter your name:',"")
			  if (username!=null && username!="")
				{
					setCookie(varUser,username,36500)
				}
		}
		return username
	}
 */
 /*
 
 JavaScript手机端页面滑动到底部加载信息（移动端ajax分页）
//获取窗口可视范围的高度
function getClientHeight(){   
    var clientHeight=0;   
    if(document.body.clientHeight&&document.documentElement.clientHeight){   
         clientHeight=(document.body.clientHeight<document.documentElement.clientHeight)?document.body.clientHeight:document.documentElement.clientHeight;           
    }else{   
         clientHeight=(document.body.clientHeight>document.documentElement.clientHeight)?document.body.clientHeight:document.documentElement.clientHeight;       
    }   
    return clientHeight;   
}
function getScrollTop(){   
    var scrollTop=0;   
    scrollTop=(document.body.scrollTop>document.documentElement.scrollTop)?document.body.scrollTop:document.documentElement.scrollTop;           
    return scrollTop;   
}
//滚动加载
function scrollLoad(){
    //可视窗口的高度
    var scrollTop = 0;
    var scrollBottom = 0; 
    $(document).scroll(function(){
        var dch = getClientHeight();
        scrollTop = getScrollTop();
          scrollBottom = document.body.scrollHeight - scrollTop;
          if(scrollBottom >= dch && scrollBottom <= (dch+10)){              
              if(pageCount == (currentPage+1)){
                      $(".click-load").hide();
                      return;
                  }              
                currentPage++;
                showList(currentPage,pageSize);                
            }
    });
}
 
顺便写一下常用的高度：
Javascript:
alert(document.body.clientWidth);        //网页可见区域宽(body)
alert(document.body.clientHeight);       //网页可见区域高(body)
alert(document.body.offsetWidth);       //网页可见区域宽(body)，包括border、margin等
alert(document.body.offsetHeight);      //网页可见区域宽(body)，包括border、margin等
alert(document.body.scrollWidth);        //网页正文全文宽，包括有滚动条时的未见区域
alert(document.body.scrollHeight);       //网页正文全文高，包括有滚动条时的未见区域
alert(document.body.scrollTop);           //网页被卷去的Top(滚动条)
alert(document.body.scrollLeft);           //网页被卷去的Left(滚动条)
alert(window.screenTop);                     //浏览器距离Top
alert(window.screenLeft);                     //浏览器距离Left
alert(window.screen.height);                //屏幕分辨率的高
alert(window.screen.width);                 //屏幕分辨率的宽
alert(window.screen.availHeight);          //屏幕可用工作区的高
alert(window.screen.availWidth);           //屏幕可用工作区的宽
 
Jquery
alert($(window).height());                           //浏览器当前窗口可视区域高度
alert($(document).height());                        //浏览器当前窗口文档的高度
alert($(document.body).height());                //浏览器当前窗口文档body的高度
alert($(document.body).outerHeight(true));  //浏览器当前窗口文档body的总高度 包括border padding margin
alert($(window).width());                            //浏览器当前窗口可视区域宽度
alert($(document).width());                        //浏览器当前窗口文档对象宽度
alert($(document.body).width());                //浏览器当前窗口文档body的宽度
alert($(document.body).outerWidth(true));  //浏览器当前窗口文档body的总宽度 包括border padding margin
下一篇： JavaScript事件处理程序

 
 */
 
 
 
 /********************************  post data by url  ******************
 
 	
		var xmlhttp
		if (window.XMLHttpRequest)	  {  xmlhttp=new XMLHttpRequest();		  }	// code for IE7+, Firefox, Chrome, Opera, Safari
		else  {xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	  }	// code for IE6, IE5  
		 
		xmlhttp.onreadystatechange =function () 
		{	if(xmlhttp.readyState==4)
			{								//alert("xmlhttp.readyState="+xmlhttp.readyState);
				if(xmlhttp.status==200 ||xmlhttp.status==0)//
				{ 	var xmlDoc=xmlhttp.responseText;alert("xmlDoc="+xmlDoc);
					
					
				}
			}		
		}		
		xmlhttp.open("POST",ByUrl,true);//true 表示异步
		xmlhttp.send(PostData);
		return "";

 
 
 
 */
 
 
 
 
 
 
 
 
 
 
 //*************************************************************************
 
 
 
 
 /*
 
 	var tmp=window.location.search;
	tmp=tmp.substr(tmp.indexOf('=')+1,tmp.length-tmp.indexOf('='));
	fromUserName=tmp.substr(0,28);// alert(fromUserName);
	tmp=tmp.substr(tmp.indexOf('=')+1,tmp.length-tmp.indexOf('='));
	username=tmp.substr(tmp.length-28); alert(username);
 
 
 */
 
 
 
 
 
 
 
 
 /*××××以下为需要使用的有用的代码
 
 
 /*按住说话功能
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<button id="hhhh">按住说话</button>
	  <style type="text/css">
		  #hhhh{height:40px;line-height: 40px; text-align: center; background: #f00; color:#fff;}
	  </style>
	  <script>
	  document.getElementById('hhhh').addEventListener('touchstart', function(ev) {
			   ev.preventDefault();
			   this.style.background = '#f00';
			   //your start record code here...
			}, false);
	  document.getElementById('hhhh').addEventListener('touchend', function(ev) {
			  ev.preventDefault();
			   this.style.background  = '#0f0';
			   //your stop record code here...
			}, false);
	
	</script>
*/
 /*
 	function checkCookie()
	{
		username=getCookie('username')
		if (username!=null && username!="")
		  {alert('Welcome again '+username)}
		else 
		  {
			  username=prompt('Please enter your name:',"")
			  if (username!=null && username!="")
				{
					setCookie('username',username,365)
				}
		  }
		 return username
	}

 
 */