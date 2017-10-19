<?php 
/** Powerd by RebetaStudio 
 * 
 *  http://www.rebeta.cn
 * 
 * 20170403更新内容：
 * 
 */

error_reporting(0);
date_default_timezone_set('PRC');


$sfzh = $_POST["SFZH"];
if(!isCreditNo($sfzh)){
    header("Content-type: text/html; charset=utf-8");
    die("<h1>检测到非法参数,身份证号码输入不正确。请返回重试！</h1>");
}

$xm = $_POST["XM"];
if(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$xm) || ($xm == "")){
    header("Content-type: text/html; charset=utf-8");
    die("<h1>检测到非法参数,姓名输入不正确。请返回重试！</h1>");
}

define ("RebetaMySqlUSER","**数据库用户名**");
define ("RebetaMySqlPWD","**数据库密码**");
define ("RebetaMySqlDSN","mysql:host=**数据库主机IP地址**;port=**数据库端口**;dbname=**数据库名称**");

try{
    //实例化mysqlpdo，执行这里时如果出错会被catch
    $pdo = new PDO(RebetaMySqlDSN,RebetaMySqlUSER,RebetaMySqlPWD);
}catch (Exception $e){
    $err = $e->getMessage();
    die($err);
}
$time = date('Y-m-d H:i:s');

$sql = "SELECT * FROM `Score` WHERE ZJH = '$sfzh' AND XM = '$xm'";
$rs = $pdo->query($sql);
$info = $rs->fetch(PDO::FETCH_ASSOC);
$name = $info[XM];
if(empty($name)){
    header("Content-type: text/html; charset=utf-8");
    die("<h1>您输入的信息考生信息不存在。请返回重试！</h1>");
}
/*
print 'openid：'.$openid;
print '<br>展位号：'.$booth;
*/
?>

<!DOCTYPE html>
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;"/>
  <meta name="viewport" content="initial-scale=1.0,user-scalable=no"/> 
  <title>忻州师范学院计算机系</title>
  <script src="js/jquery-1.7.1.min.js"></script>
  <script src="js/easyBackground-min.js"></script>
<LINK REL=StyleSheet HREF="style/style.css" TYPE="text/css" MEDIA=screen>
<link rel="stylesheet" href="css/styles.css" media="screen">
<meta name="robots" content="noindex,follow" />
</head>
<body>
<script type="text/javascript">
      $(document).ready(function() {
    $('body').easyBackground({
        wrapNeighbours: true
    });
      });

  </script>
<div align="center" style="width=device-width;max-width:500px;margin:0 auto;"><img style="width:100%;" src="./images/title.png" /></div>
  <div class="container">
    <div class="login">
      <h1>查询结果</h1>
      <p><?php print $name;?> 同学你好，你的查询结果如下：</p>
      <!-- <p style="text-indent:4em;">报&nbsp;名&nbsp;号：<?php print $info[BMH];?></p> -->
      <p style="text-indent:4em;">准考证号：<?php print $info[ZKZH];?></p>
      <p style="text-indent:4em;">考试成绩：<?php if($info[CJ] < 60){print '<span style="color:#E53333;">'.$info[CJ].' (不及格)</span>';}elseif($info[CJ] < 90){print '<span style="color:#FF9900;">'.$info[CJ].' (及格)</span>';}else{print '<span style="color:#009900;">'.$info[CJ].' (优秀)</span>';};?></p>
      <p style="text-indent:4em;">证书编号：<?php if($info[CJ] < 60){print '无';}else{print $info[ZSBH];};?></p>
      <!-- <p style="text-indent:2em;">请通过考试的同学于X月X日后，前往主楼709领取证书。</p> -->
      <!-- <p><br></p> -->
      <p style="text-align:right;"><?php print $time;?></p>
    </div>
  </div>
  
  <footer style="clear:both;text-align:center;width:auto;margin-top:4em;">
	<a href="http://www.miibeian.gov.cn/" style="text-decoration:none;font-color=white;"><font color="white">蒙ICP备17000857号</font></a>
	<br><font color="white">忻州师范学院 · 计算机科学与技术系</font>
	<br><a href="http://www.rebeta.cn/" style="text-decoration:none;font-color=white;"><font color="white">（本系统由掌上忻师提供技术支持）</font></a>
  <!-- <br>Copyright © 2016 - 2017 Rebeta Inc. All Rights Reserved.</font> -->
</footer>
</body>
</html>


<?php 
function isCreditNo($vStr)
{
    $vCity = array(
        '11','12','13','14','15','21','22',
        '23','31','32','33','34','35','36',
        '37','41','42','43','44','45','46',
        '50','51','52','53','54','61','62',
        '63','64','65','71','81','82','91'
    );

    if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr)) return false;

    if (!in_array(substr($vStr, 0, 2), $vCity)) return false;

    $vStr = preg_replace('/[xX]$/i', 'a', $vStr);
    $vLength = strlen($vStr);

    if ($vLength == 18)
    {
        $vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);
    } else {
        $vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
    }

    if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday) return false;
    if ($vLength == 18)
    {
        $vSum = 0;

        for ($i = 17 ; $i >= 0 ; $i--)
        {
            $vSubStr = substr($vStr, 17 - $i, 1);
            $vSum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr , 11));
        }

        if($vSum % 11 != 1) return false;
    }
    return true;
}
?>