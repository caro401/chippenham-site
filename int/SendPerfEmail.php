<?php 
// Set fields in data
include_once("fest.php");
include_once("DanceLib.php");
include_once("MusicLib.php");
include_once("Email.php");
global $PLANYEAR,$USER,$USERID,$PerfTypes,$CONF;

A_Check("Staff");

if (isset($_REQUEST['REEDIT'])) {
  system("rm Temp/$USERID.*");
  $LogId = $_REQUEST['LogId'];
  $Log = Get_Email_Log($LogId);
  
  $id = $Log['TypeId'];
  $Side = Get_Side($id);
  $Sidey = Get_SideYear($id);
  $subject = Feature('FestName') . " $PLANYEAR and " . $Side['SN'];
  $Mess = $Log['TextBody'];  
  $Mess = preg_replace("/<p>\s*\n\s*\n/","\n\n",$Mess);
  $Mess = preg_replace("/\s*\n\s*\n<p>/","\n\n",$Mess);
  $label = 'Unknown';
  $proforma = '';
  
  $Atts = Get_Email_Attachments($LogId);
  foreach ($Atts as $Att) {
    if ($Att['AttType'] == 1) {
      $sfx = pathinfo($Att['AttFileName'],PATHINFO_EXTENSION );
      $attnum = $Att['AttName'];
      $tf = $USERID . "." . $attnum . "." . time() . ".$sfx";
      copy($Att['AttFileName'],"Temp/$tf");    
      $Mess = preg_replace("/<img src=cid:img_$attnum.$sfx>/","<img src='Temp/$tf'>",$Mess);
    } // This is fine to display but not for editting - need to replace with something else 
  }

  
} else {

  $id = $_REQUEST['I'];
  $proforma = (isset($_REQUEST['N'])?$_REQUEST['N']:'');
  $label = (isset($_REQUEST['L'])?$_REQUEST['L']:"");
  $Atts = (isset($_REQUEST['ATTS'])?json_decode($_REQUEST['ATTS'],true):[]);

//  var_dump($Atts);
  $Side = Get_Side($id);
  $Sidey = Get_SideYear($id);
  $subject = Feature('FestName') . " $PLANYEAR and " . $Side['SN'];
  $Mess = (isset($_POST['Message'])?$_POST['Message']:(Get_Email_Proforma($proforma))['Body']);
  $To = $Side['Email'];
  if (isset($_REQUEST['E']) && isset($Side[$_REQUEST['E']]) ) {
    $To = $Side[$_REQUEST['E']];
  }

  if (isset($_REQUEST['CANCEL'])) {  echo "<script>window.close()</script>"; exit; }

  if (isset($_REQUEST['SEND'])) {

    $ReplyTo = '';
    $ReplyName = '';
    $IsAC = 0;
    set_user();


    foreach ($PerfTypes as $t=>$p) {
      if ($Side[$p[0]]) {
        $IsAC++;
        $EmailsFrom = Feature($p[1] . 'EmailsFrom','');
        if ($IsAC > 1) break;
        if ($EmailsFrom == 'USER') { $IsAC+=2; break; }
        if (!empty($EmailsFrom)) $ReplyTo = $EmailsFrom;
      }
    }

    if (empty($Sidey['BookedBy'])) {
      $ReplyTo = $USER['FestEmail'];
    } else if ($IsAC > 1 || empty($ReplyTo)) {
      if ($USERID == $Sidey['BookedBy']) {
        if (!empty($USER['FestEmail'])) {
          $ReplyTo = $USER['FestEmail'];
          if (!strstr($ReplyTo,'@')) $ReplyTo .= '@' . Feature('HostURL');
        }
      } else {
        $User = Gen_Get('FestUsers',$Sidey['BookedBy'],'UserId');
        $ReplyTo = $User['FestEmail'];
        if (!strstr($ReplyTo,'@')) $ReplyTo .= '@' . Feature('HostURL');
      }
    }

    if (isset($_REQUEST['E']) && isset($Side[$_REQUEST['E']]) ) {
      $To = $Side[$_REQUEST['E']];
    }

    $too = [['to',$To,$Side['Contact']],
//            ['from',$ReplyTo,Feature('ShortName')], // WRONG ANYWAY
            ['replyto',$ReplyTo,Feature('ShortName')]];


    if ($_POST['CCs']) {
      $CCs = explode("\n",$_POST['CCs']);
      foreach ($CCs as $CC) {
        if (!strstr($CC,'@')) continue;
        Clean_Email($CC);
        $too[] = ['cc',$CC];
      }
    }
  
//  var_dump($too);  
//var_dump($Atts);
  echo Email_Proforma(1,$id,$too,$Mess,$subject,'Dance_Email_Details',[$Side,$Sidey],'Performer',$Atts);
//echo "<p>Afeter Proforma:";
//var_dump($Atts);  
  Dance_Email_Details_Callback($proforma,[$Side,$Sidey]);
  // Log to "Invited field"
  $prefix = '';
  $Sidey = Get_SideYear($id); // Need to refetch as callback has overwritten it
    if ($label) $prefix .= "<span " . Music_Proforma_Background($label) . ">$label:";
    $prefix .= date('j/n/y');
    if ($label) $prefix .= "</span>";
    if (strlen($Sidey['Invited'])) {
      $Sidey['Invited'] = $prefix . ", " . $Sidey['Invited'];
    } else {
      $Sidey['Invited'] = $prefix;  
    }

    Put_SideYear($Sidey);
    // Special Change Notice Code
    if ($label == 'Change' || $label == 'Reinvite') {
      Dance_Record_Change($id, $prefix);
    }
//var_dump($Atts);
//    if (empty($CONF['testing'])) 
    echo "<script>window.close()</script>"; 
    exit;
  }
}

dominimalhead("Email for " . $Side['SN'],["css/festconstyle.css"]);

Replace_Help('Dance',1);

echo "<h2>Email for " . $Side['SN'] . " - " . $Side['Contact'] . "</h2>";
if (isset($_POST['PREVIEW'])) {
  echo "<p><h3>Preview...</h2>";
  $MessP = $Mess;
  Parse_Proforma($MessP,$helper='Dance_Email_Details',[$Side,$Sidey],1);
  echo "<div style='background:white;border:2;border-color:blue;padding:20;margin:20;width:90%;max-width:80ch;height:50%;overflow:scroll' >$MessP</div>";
}
echo "<h3>Edit the message below, then click Preview, Send or Cancel</h3>";
echo "Put &lt;p&gt; for paras, &lt;br&gt; for line break, &lt;b&gt;<b>Bold</b>&lt;/b&gt;, &amp;amp; for &amp;, &amp;pound; for &pound; <p> ";

echo "<form method=post>" . fm_hidden('id',$id) . fm_hidden('L',$label) . fm_hidden('N',$proforma);
echo fm_textarea("CC",$_POST,'CCs',6,1); 
if (isset($Atts) && $Atts) {
  echo " Attached: ";
  foreach ($Atts as $A) {
    if ($A['AttType'] == 0) { 
      echo " <a target=_blank href='ShowFile?l=" . $A['AttFileName'] . "'>" . $A['AttFileName'] . "</a> "; 
    } else {
      // TODO
    }
  }
  echo fm_hidden('ATTS',json_encode($Atts));
}
echo "<br>";
echo "<div style='width:90%;height:70%'>
      <textarea name=Message id=OrigMsg style='background:white;border:2;border-color:blue;padding:20;margin:20;width:100%;height:100%'
       onchange=UpdateHtml('OrigMsg','ActMsg'))>" .  htmlspec($Mess) . "</textarea></div><p><br><p>\n";

echo " <input type=submit name=PREVIEW value=Preview> <input type=submit name=SEND value=Send> <input type=submit name=CANCEL value=Cancel>\n";
echo "</form>";

//echo fm_DragonDrop(, $Type,$Cat,$id,&$Data,$Mode=0,$Mess='',$Cond=1,$tddata1='',$tdclass='',$hide=0) {

?>

