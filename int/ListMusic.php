<?php
  include_once("fest.php");
  A_Check('Steward');

  dostaffhead("List Music", ["/js/clipboard.min.js", "/js/emailclick.js", "/js/InviteThings.js"]);

  global $YEAR,$PLANYEAR,$Book_Colours,$Book_States,$Book_Actions,$Book_ActionExtras,$Importance,$InsuranceStates,$PerfTypes,$Cancel_Colours,$Cancel_States,$Book_ActionColours;
  include_once("DanceLib.php"); 
  include_once("MusicLib.php"); 
  include_once("Email.php"); 
  

  echo fm_hidden('Year',$YEAR);
  $YearTab = 'SideYear';

  $Type = (isset($_GET['T'])? $_GET['T'] : 'M' );
  if ($Type == 'Z') {
    $TypeSel = " IsASide=0 AND IsAnAct=0 AND IsFunny=0 AND IsFamily=0 AND IsCeilidh=0 AND IsOther=0 ";
    $Perf = "Uncategorised performers";
    $DiffFld = "Importance";    
  }  else {
    $Perf = ""; 
    foreach ($PerfTypes as $p=>$d) if ($d[4] == $Type) { $Perf = $p; $PerfD = $d; };

    $TypeSel = $PerfD[0] . "=1 ";
    $DiffFld = $PerfD[2] . "Importance";
  }
  
  if (Access('Staff',($PerfD[2] ?? 'OtherPerf'))) echo "<div class=floatright style=text-align:right><div class=Bespoke>" .
       "Sending:<button class=BigSwitchSelected id=BespokeM onclick=Add_Bespoke()>Generic Messages</button><br>" .  
       "Switch to: <button class=BigSwitch id=GenericM onclick=Add_Bespoke()>Bespoke Messages</button></div>" .
       "<div class=Bespoke hidden id=BespokeMess>" .
       "Sending:<button class=BigSwitchSelected id=GenericM1 onclick=Remove_Bespoke()>Bespoke Messages</button><br>" .  
       "Switch to: <button class=BigSwitch id=BespokeM1 onclick=Remove_Bespoke()>Generic Messages</button></div>" .
       "</div>";
       

  echo "<div class=content><h2>List $Perf $YEAR</h2>\n";
  
  $Ins_colours = ['red','orange','lime'];
  echo "Click on column header to sort by column.  Click on Acts's name for more detail and programme when available,<p>\n";

  echo "If you click on the email link, press control-V afterwards to paste the standard link into message.<p>";
  $col5 = $col6 = $col7 = $col8 = $col9 = $col10 = $col11 = '';

  if (isset($_GET['ACTION'])) {
    $sid = $_GET['SideId'];
    $side = Get_Side($sid);
    $sidey = Get_SideYear($sid);
    Music_Actions($_GET['ACTION'],$side,$sidey);
  }

  if ($_GET['SEL'] == 'ALL') {
    $SideQ = $db->query("SELECT y.*, s.* FROM Sides AS s LEFT JOIN $YearTab as y ON s.SideId=y.SideId AND y.Year='$YEAR' WHERE $TypeSel AND s.SideStatus=0 ORDER BY SN");
    $col5 = "Book State";
    $col6 = "Actions";
    if (substr($YEAR,0,4) == '2020') $col10 = 'Change';
  } else if ($_GET['SEL'] == 'INV') {
    $LastYear = $PLANYEAR-1;
    $SideQ = $db->query("SELECT y.*, s.* FROM Sides AS s LEFT JOIN $YearTab as y ON s.SideId=y.SideId AND y.Year='$PLANYEAR' WHERE $TypeSel AND s.SideStatus=0 ORDER BY SN");
    $col5 = "Invited $LastYear";
    $col6 = "Coming $LastYear";
    $col7 = "Invite $PLANYEAR";
    $col8 = "Invited $PLANYEAR";
    $col9 = "Coming $PLANYEAR";
  } else if ($_GET['SEL'] == 'Coming') {
    $SideQ = $db->query("SELECT s.*, y.*, IF(s.DiffImportance=1,s.$DiffFld,s.Importance) AS EffectiveImportance FROM Sides AS s, $YearTab as y " .
                "WHERE $TypeSel AND s.SideId=y.SideId AND y.Year='$YEAR' AND y.YearState=" . 
                $Book_State['Contract Signed'] . " ORDER BY EffectiveImportance DESC, SN"); 
    $col5 = "Complete?";
  } else if ($_GET['SEL'] == 'Booking') {
    $SideQ = $db->query("SELECT s.*, y.* FROM Sides AS s, $YearTab as y WHERE $TypeSel AND s.SideId=y.SideId AND y.Year='$YEAR' AND ( y.YearState>0 || y.TickBox4>0)" . 
                " AND s.SideStatus=0 ORDER BY SN");
    $col5 = "Book State";
    $col6 = "Actions";
    $col7 = "Imp";
    $col8 = "Insurance";
    $col9 = "Missing";
    $col10 = 'Messages';
    if (substr($YEAR,0,4) == '2020') $col11 = 'Change';
    echo "Under <b>Actions</b> various errors are reported, the most significant error is indicated.  Please fix these before issuing the contracts.<p>\n";
    echo "Missing: P - Needs Phone, E Needs Email, T Needs Tech Spec, B Needs Bank (Only if fees), I Insurance.<p>";
  } else if ($_GET['SEL'] == 'Avail') {
    $SideQ = $db->query("SELECT s.*, y.* FROM Sides AS s, $YearTab as y WHERE $TypeSel AND s.SideId=y.SideId AND y.Year='$YEAR' AND s.SideStatus=0 ORDER BY SN");
    $col5 = "Book State";
    $col6 = "Actions";
    $col7 = "Imp";
    $col8 = "Availability";
    $col9 = 'Messages';
//    $col9 = "Prev Fest State";
 
  } else if ($_GET['SEL'] == 'BookingLastYear') {

    $PrevYear = '2021'; // TODO fix fudge
    echo "<div class=content><h2>List $Perf $PrevYear</h2>\n";
    $SideQ = $db->query("SELECT s.*, y.* FROM Sides AS s, $YearTab as y WHERE $TypeSel AND s.SideId=y.SideId AND y.Year='$PrevYear' AND ( y.YearState>0 || y.TickBox4>0)" . 
                " AND s.SideStatus=0 ORDER BY SN");
    $col5 = "Book State";
    $col6 = "Actions";
    $col7 = "Imp";
    $col8 = "Next Year Avail";
    $col9 = 'Messages';
    if (substr($YEAR,0,4) == '2020') $col10 = 'Change';
  } else { // general public list
    $SideQ = $db->query("SELECT y.*, s.*, IF(s.DiffImportance=1,s.$DiffFld,s.Importance) AS EffectiveImportance  FROM Sides AS s, $YearTab as y " .
                "WHERE $TypeSel AND s.SideId=y.SideId AND y.Year='$YEAR' AND y.YearState=" . 
                $Book_State['Contract Signed'] . " ORDER BY EffectiveImportance DESC SN");
  }

  if (!$SideQ || $SideQ->num_rows==0) {
    echo "<h2>No Acts Found</h2>\n";
  } else {
    $coln = 0;
    echo "<div class=Scrolltable><table id=indextable border style='min-width:1200px'>\n";
    echo "<thead><tr>";
    echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Name</a>\n";
    echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Type</a>\n";
    if ($_GET['SEL']) {
      echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Contact</a>\n";
      echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Email</a>\n";
//      echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Link</a>\n";
    }
    if ($col5) echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>$col5</a>\n";
    if ($col6) echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>$col6</a>\n";
    if ($col7) echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>$col7</a>\n";
    if ($col8) echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>$col8</a>\n";
    if ($col9) echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>$col9</a>\n";
    if ($col10) echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>$col10</a>\n";
    if ($col11) echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>$col11</a>\n";
//    for($i=1;$i<5;$i++) {
//      echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>EM$i</a>\n";
//    }

    echo "</thead><tbody>";

  if (Access('SysAdmin')) {
    echo "Debug: <span id=DebugPane></span><p>"; 
  } else {
    echo "<div hidden><tr><td>Debug:<td colspan=8><span id=DebugPane></span><p></div>"; 
  }


  if (Access('SysAdmin')) {
    echo "<tr><td>Debug:<td colspan=8><span id=DebugPane></span>"; 
  }
  
    while ($fetch = $SideQ->fetch_assoc()) {
      $snum = $fetch['SideId'];
      echo "<tr><td><a href=AddPerf?sidenum=$snum&Y=$YEAR>" . $fetch['SN'] . "</a>";
      if ($fetch['SideStatus']) {
        echo "<td>DEAD";
      } else {
        echo "<td>" . $fetch['Type'];// . $fetch['syId'];
      }
      if ($_GET['SEL']) {
        echo "<td>" . ($fetch['HasAgent']?$fetch['AgentName']:$fetch['Contact']);
        echo "<td>" . linkemailhtml($fetch,'Act',(!$fetch['Email'] && $fetch['AltEmail']? 'Alt' : '' ));
      } 

      $State = $fetch['YearState'];
      if (isset($State)) {
//echo "</table><br>"; var_dump($fetch);exit;
        Contract_State_Check($fetch,0); 
        $State = $fetch['YearState'];
      } else {
        $state = 0;
      }
      for ($fld=5; $fld<11; $fld++) {
        $ff = "col$fld";
        switch ($$ff) {

        case 'Book State': 
          if (!isset($State)) $State = 0;
          if ($fetch['TickBox4']) {
            $CState = $fetch['TickBox4'];
            echo "<td style='background-color:" . $Cancel_Colours[$CState] . "' id=BookState$snum >" . $Cancel_States[$CState];
          } else {
            echo "<td style='background-color:" . $Book_Colours[$State] . "' id=BookState$snum >" . $Book_States[$State];
          }
          break;

        case 'Confirmed':
          echo "<td  id=BookConfirm$Snum >" . ($fetch['ContractConfirm']?'Yes':'');
          break;

        case 'Actions':
          echo "<td>";
          if (!Access('Staff',($PerfD[2] ?? 'OtherPerf'))) break;  // Not your area
          $acts = $Book_Actions[$Book_States[$State]];
          if ($acts) {
            $acts = array_reverse(preg_split('/,/',$acts) );
//            echo "<form method=Post Action=PerformerActions>" . fm_Hidden('SEL',$_GET['SEL']) . fm_hidden('SideId',$fetch['SideId']) . fm_hidden('T',$Type) . 
//                  fm_hidden('Y',$YEAR). fm_hidden('SideId',$fetch['SideId']);
            foreach($acts as $ac) {
              switch ($ac) {
                case 'Contract':
                  $NValid = Contract_Check($fetch['SideId'],0);
                  if ($NValid) {
                    echo $NValid;
                    continue 2;
                  }
                  break;
                case 'Dates':
                  if (!Feature('EnableDateChange')) continue 2;
                  break;
                case 'FestC':
                  if (!Feature('EnableCancelMsg')) continue 2;
                  break;
              }
              echo "<button type=button id=$ac$snum class=ProfButton onclick=MList_ProformaSend('Music_$ac',$snum,'$ac','SendPerfEmail')" . 
                     Music_Proforma_Background($ac) . ">$ac</button>"; 



//              echo "<button class=floatright name=ACTION value='$ac' type=submit " . $Book_ActionExtras[$ac] . 
//                   " style='background:" . $Book_ActionColours[$ac] . ";'>$ac</button>";
            }
            echo "</form>";
          } 
          break;
        case 'Imp':
          echo "<td>" . $Importance[($fetch['DiffImportance']?$fetch[$DiffFld]:$fetch['Importance'])];
          break;
        case 'Insurance':
          $ins = (isset($fetch['Insurance']) ? $fetch['Insurance'] : 0);
          echo "<td style=background:" . $Ins_colours[$ins] . ">" . $InsuranceStates[$ins];
          break;
        case 'Missing':
          $keys = '';
          if (!$fetch['Phone'] && !$fetch['Mobile']) $keys .= 'P';
          if (!$fetch['Email'] && !$fetch['AgentEmail']) $keys .= 'E';
          if ($fetch['StagePA'] == 'None') $keys .= 'T';
          if ($fetch['TotalFee']  && ( !$fetch['SortCode'] || !$fetch['Account'] || !$fetch['AccountName'])) $keys .= 'B';
          if ($fetch['Insurance'] == 0) $keys .= 'I';
          echo "<td>$keys";
          break;
        case 'Change':
          echo "<td>";
          
          echo (isset($fetch['TickBox4']) ? ['','Sent','Ack'][$fetch['TickBox4']] : "");
          break;
        case 'Availability' :
          echo "<td>";
          if ($fetch['MFri']) { 
            echo "F";
            if ($fetch['FriAvail']) echo "*";
          }
          if ($fetch['MSat']) {
            echo " Sa";
            if ($fetch['SatAvail']) echo "*";           
          }
          if ($fetch['MSun']) {
            echo " Su";
            if ($fetch['SunAvail']) echo "*";          
          }    
          break;
        case 'Prev Fest State':
          if (!isset($State)) $State = 0;
          $Prevy = Get_SideYear($fetch['SideId'],$PrevYear);
          if ($fetch['TickBox4']) {
            $CState = $Prevy['TickBox4'];
            echo "<td style='background-color:" . $Cancel_Colours[$CState] . "'>" . $Cancel_States[$CState];
          } else {
            $LState = $Prevy['YearState'];
            echo "<td style='background-color:" . $Book_Colours[$LState] . "'>" . $Book_States[$LState];
          }
          break;
        
        case 'Next Year Avail' :
          $thisyear = Get_SideYear($fetch['SideId'],$PLANYEAR);
          echo "<td>";
          echo (isset($thisyear['TickBox4']) ? ['','Sent','Ack'][$thisyear['TickBox4']] : "");
          if (isset($thisyear['MFri'])) {
            if ($thisyear['MFri']) { 
              echo "F";
              if ($thisyear['FriAvail']) echo "*";
            }
            if ($thisyear['MSat']) {
              echo " Sa";
              if ($thisyear['SatAvail']) echo "*";           
            }
            if ($thisyear['MSun']) {
              echo " Su";
              if ($thisyear['SunAvail']) echo "*";          
            }  
          }  
                    
          break;
          
        case 'Messages':
          echo "<td width=250 height=38 style='max-width:200;max-height:38;'>";
          echo "<div id=Vited$snum class=scrollableY>";
          if (isset($fetch['Invited'])) echo $fetch['Invited'];
          echo "</div>";
          break;
          
        default:
          break;

        }
      }
    }
    echo "</tbody></table></div>\n";
  }
  dotail(); 
?>
