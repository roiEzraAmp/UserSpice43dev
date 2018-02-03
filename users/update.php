<?php
require_once 'init.php';
require_once $abs_us_root.$us_url_root.'users/includes/header.php';
require_once $abs_us_root.$us_url_root.'users/includes/navigation.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}
$count = 0;
$updates = $db->query("SELECT * FROM updates")->results();
$existing_updates=[];
foreach($updates as $u){
  $existing_updates[] = $u->migration;
}
?>
<div id="page-wrapper">

  <div class="container">

    <!-- Page Heading -->
    <div class="row">
      <div class="col-sm-12">
<?php
//demo migration
$update = '3GJYaKcqUtw7';
if(!in_array($update,$existing_updates)){
//fix vericodes

$u = $db->query("SELECT id FROM users")->results();
foreach($u as $me){
  $db->update('users',$me->id,['vericode'=>randomstring(15)]);
}
  logger(1,"System Updates","Reformatted existing vericodes");

  logger(1,"System Updates","Update $update successfully deployed.");
  $db->insert('updates',['migration'=>$update]);
  echo "Applied update ".$update."<br>";
  $count++;
}

$update = '3GJYaKcqUtz8';
if(!in_array($update,$existing_updates)){
//fix vericodes
$test = $db->query("SELECT * FROM users WHERE id = 1")->first();
if($test->vericode == '322418'){
$u = $db->query("SELECT id FROM users")->results();
foreach($u as $me){
  $db->update('users',$me->id,['vericode'=>randomstring(15)]);
}
  logger(1,"System Updates","Reformatted existing vericodes");

  logger(1,"System Updates","Update $update successfully deployed.");
}
  echo "Patched vericode vulnerability<br>";
  $db->insert('updates',['migration'=>$update]);
  echo "Applied update ".$update."<br>";
  $count++;
}

$update = '69qa8h6E1bzG';
if(!in_array($update,$existing_updates)){
//Change old logs to IP Logging
$db->query("UPDATE logs SET logtype = ? WHERE logtype = ? AND lognote LIKE ?",array("IP Logging","User","%blacklisted%attempted%visit"));
logger(1,"System Updates","Updated old Blacklisted logs to IP Logging type.");
//Add new DB field
$db->query("ALTER TABLE users ADD cloak_allowed tinyint(1) NOT NULL");
logger(1,"System Updates","Added cloaking to users.");
}

$update = '2XQjsKYJAfn1';
if(!in_array($update,$existing_updates)){
$db->query("ALTER TABLE settings ADD force_notif tinyint(1)");
$db->query("ALTER TABLE settings ADD cron_ip varchar(255)");
$db->update("settings",1,['cron_ip'=>'off']);

echo "<font color='red'>For security reasons</font>, your cron jobs have been temporarily disabled.  Please visit <a href='cron_manager.php'>Cron Manager</a> for more information.<br>";
  logger(1,"System Updates","Update $update successfully deployed.");
  $db->insert('updates',['migration'=>$update]);
  echo "Applied update ".$update."<br>";
  $count++;
}

$update = '549DLFeHMNw7';
if(!in_array($update,$existing_updates)){
$db->query("UPDATE settings SET force_notif=0 WHERE force_notif IS NULL");
  logger(1,"System Updates","Updated force_notif to 0 if you had not set it already.");
  logger(1,"System Updates","Update $update successfully deployed.");
  $db->insert('updates',['migration'=>$update]);
  echo "Applied update ".$update."<br>";
  $count++;
}

if($count == 1){
echo "Finished applying ".$count." update.<br>";
}else{
echo "Finished applying ".$count." updates.<br>";
}
?>
<a href="admin.php">Return to the Admin Dashboard</a>
</div></div></div></div>
