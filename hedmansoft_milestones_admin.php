
<?php 
	$hs_milestonesConfiguration = get_option("hs_milestones_configuration");
    if(!isset($hs_milestonesConfiguration)) {
    	$hs_milestonesConfiguration = array();
    }
	$milestoneArray = $hs_milestonesConfiguration['hs_milestoneArray'];
	if(!isset($milestoneArray)) {
		$milestoneArray = array();
	}
	$size = count($milestoneArray);
    if($_POST['hsmilestones_action']) {
		wp_verify_nonce('hsmilestone_nonce', 'post-hsmilestone-data');
		check_admin_referer('post-hsmilestone-data', 'hsmilestone_nonce' );
		$postAction = sanitize_text_field($_POST['hsmilestones_action']);
    	if($postAction == 'P') {
	        //Form data sent
	 		//Look for changes in the milestone list
			unset($milestoneArray);
			for($i = 0; $i < $size; ++$i) {
	        	$milestoneDate = sanitize_text_field($_POST['mdate'.$i]);
	        	$milestoneTitle = sanitize_text_field($_POST['mtitle'.$i]);
	        	$milestoneLink = sanitize_text_field($_POST['mlink'.$i]);
	        	$milestoneDescription = sanitize_text_field($_POST['mdesc'.$i]);
	        	$milestoneLinkDisplay = sanitize_text_field($_POST['mlinkd'.$i]);
	        	$days = sanitize_text_field($_POST['mdays'.$i]);
	        	$milestoneBeginDays = $days!= null?intval($days):null;
	        	$milestoneOmitCountdown = sanitize_text_field($_POST['momit'.$i]);
	        	$buildMilestone = new hsmilestones_Milestone();
				$buildMilestone->milestoneDate=$milestoneDate;
				$buildMilestone->milestoneTitle=$milestoneTitle;
				$buildMilestone->milestoneDescription=$milestoneDescription;
				$buildMilestone->milestoneLink=$milestoneLink;
				$buildMilestone->milestoneLinkDisplay=$milestoneLinkDisplay;
				$buildMilestone->milestoneBeginDays=$milestoneBeginDays;
				$buildMilestone->milestoneOmitCountdown=$milestoneOmitCountdown;
				$milestoneArray[$i] = $buildMilestone;
	        }
	        ?>
	        <div class="updated"><p><strong><?php _e('Options saved.'); ?></strong></p></div>
	        <?php
		} else {
			if($postAction == 'R') {
				//REMOVE A ROW - also rebuild other rows for possible changes
				$ordinal = intval(sanitize_text_field($_POST['hsmilestones_actionOrdinal']));
				$index = 0;
				unset($milestoneArray);
				for($i = 0; $i < $size; ++$i) {
					if($i != $ordinal) {
			        	$milestoneDate = sanitize_text_field($_POST['mdate'.$i]);
			        	$milestoneTitle = sanitize_text_field($_POST['mtitle'.$i]);
			        	$milestoneDescription = sanitize_text_field($_POST['mdesc'.$i]);
			        	$milestoneLink = sanitize_text_field($_POST['mlink'.$i]);
			        	$milestoneLinkDisplay = sanitize_text_field($_POST['mlinkd'.$i]);
	        			$days = sanitize_text_field($_POST['mdays'.$i]);
			        	$milestoneBeginDays = $days!= null?intval($days):null;
			        	$milestoneOmitCountdown = sanitize_text_field($_POST['momit'.$i]);
			        	$buildMilestone = new hsmilestones_Milestone();
						$buildMilestone->milestoneDate=$milestoneDate;
						$buildMilestone->milestoneTitle=$milestoneTitle;
						$buildMilestone->milestoneDescription=$milestoneDescription;
						$buildMilestone->milestoneLink=$milestoneLink;
						$buildMilestone->milestoneLinkDisplay=$milestoneLinkDisplay;
						$buildMilestone->milestoneBeginDays=$milestoneBeginDays;
						$buildMilestone->milestoneOmitCountdown=$milestoneOmitCountdown;
						$milestoneArray[$index] = $buildMilestone;
						$index ++;
					}
					else {
						//Skip this row
					}
				}
			} 
			else if($postAction == 'A') {
				//ADD A NEW ROW - also rebuild other rows for possible changes
				$ordinal = intval(sanitize_text_field($_POST['hsmilestones_actionOrdinal']));
				unset($milestoneArray);
				for($i = 0; $i < $size; ++$i) {
		        	$milestoneDate = sanitize_text_field($_POST['mdate'.$i]);
		        	$milestoneTitle = sanitize_text_field($_POST['mtitle'.$i]);
		        	$milestoneDescription = sanitize_text_field($_POST['mdesc'.$i]);
		        	$milestoneLink = sanitize_text_field($_POST['mlink'.$i]);
		        	$milestoneLinkDisplay = sanitize_text_field($_POST['mlinkd'.$i]);
        			$days = sanitize_text_field($_POST['mdays'.$i]);
		        	$milestoneBeginDays = $days!= null?intval($days):null;
		        	$milestoneOmitCountdown = sanitize_text_field($_POST['mlinkd'.$i]);
		        	$buildMilestone = new hsmilestones_Milestone();
					$buildMilestone->milestoneDate=$milestoneDate;
					$buildMilestone->milestoneTitle=$milestoneTitle;
					$buildMilestone->milestoneDescription=$milestoneDescription;
					$buildMilestone->milestoneLink=$milestoneLink;
					$buildMilestone->milestoneLinkDisplay=$milestoneLinkDisplay;
					$buildMilestone->milestoneBeginDays=$milestoneBeginDays;
					$buildMilestone->milestoneOmitCountdown=$milestoneOmitCountdown;
					$milestoneArray[$i] = $buildMilestone;
		        }
				$buildMilestone = new hsmilestones_Milestone();
				$buildMilestone->milestoneDate=sanitize_text_field($_POST['mdate'.$ordinal]);
				$buildMilestone->milestoneTitle=sanitize_text_field($_POST['mtitle'.$ordinal]);
				$buildMilestone->milestoneDescription=sanitize_text_field($_POST['mdesc'.$ordinal]);
				$buildMilestone->milestoneLink=sanitize_text_field($_POST['mlink'.$ordinal]);
				$buildMilestone->milestoneLinkDisplay=sanitize_text_field($_POST['mlinkd'.$ordinal]);
    			$days = sanitize_text_field($_POST['mdays'.$ordinal]);
			    $buildMilestone->milestoneBeginDays = $days!= null?intval($days):null;
	        	$buildMilestone->milestoneOmitCountdown = sanitize_text_field($_POST['momit'.$ordinal]);
				$milestoneArray[$ordinal] = $buildMilestone;
	    	} else {
	        	//Normal page display
	    	}
	    }
	}
	if($milestoneArray) usort($milestoneArray, "hsmilestones_cmp");		//sort the array by date before saving to configuration
    $hs_milestonesConfiguration['hs_milestoneArray'] = $milestoneArray;
	$size = count($milestoneArray);
	update_option('hs_milestones_configuration',$hs_milestonesConfiguration);
    
    //Sort the milestone array by date
    function hsmilestones_cmp($a, $b) {
	    return strcmp($a->milestoneDate, $b->milestoneDate);
	}

?>
<div class="wrap">

    <?php    echo "<h2>" . __( 'HedmanSoft Milestones Admin Options', 'hs_milestones' )."</h2>"; ?>
     
    <form name="hsmilestones_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
        <input type="hidden" name="hsmilestones_action" value="P">
        <input type="hidden" name="hsmilestones_actionOrdinal" >
        <p style="font-size:8; width:60%;">When using the shortcode for this plugin, you can use the following shortcode parameters:<br/>
        show-header="TRUE"/"FALSE" - will determine whether to show a header text at the top of the list of milestones. Default is TRUE.<br/>
        events-header="" - Will allow you to replace the default of "UPCOMING EVENTS" with whatever header you want.  show-header would need to be TRUE for this value to be used.<br/>
        datetime-format="" This is the format of the event date and time you wish to use.  The default is "m/d/Y h:i a".  If a timestamp is included in the format string then the time will be displayed BELOW the date to save horizontal space and improve readibility. Valid date formats are defined <a href="" onclick="window.open('http://php.net/manual/en/function.date.php');return false;">here</a><br/>
        cutoff-days="x" where x is a number of days. By default this would be 30 days but you can override this on an event-by-event basis by entering a number below in the Begin Showing Days column. 
        </p>
        <?php    
        echo "<table><tr><td colspan='9' align='center'>List of Milestones</td></tr>";
    	echo "<tr style='vertical-align:bottom'><td></td>";
    	echo "<td>Event Date</td>";
    	echo "<td>Event Title</td>";
    	echo "<td>Event Description</td>";
    	echo "<td>(Optional) Link</td>";
    	echo "<td>(Optional) Link Display</td>";
    	echo "<td style='width:12px;'>(Optional)<br/>Days Before<br/>Showing</td>";
    	echo "<td>(Optional)<br/>Omit<br/>Countdown</td>";
    	echo "<td>Action</td>";
    	echo "</tr>";
		$nowDateTime = new DateTime();
		$tzoffset = intval(hsmilestones_getMyTimeZoneOffset());
		if($tzoffset < 0) {
			$nowDateTime->sub(new DateInterval('PT'.abs($tzoffset).'H'));
		}
		else {
			$nowDateTime->add(new DateInterval('PT'.$tzoffset.'H'));
		}
		for($i = 0; $i < $size; ++$i) {
			$rowNumber = 0 + $i + 1;
        	echo "<tr>";
        	echo "<td>".$rowNumber."</td>";
        	$d1 = new DateTime(stripcslashes($milestoneArray[$i]->milestoneDate)); 
			$diff = ($d1->getTimestamp() - $nowDateTime->getTimestamp()) / 3600;
			
        	$classDisplay = round($diff * 60) < 0? 'style="color:orange"':'';
        	echo "<td><div id='mdatediv".$i."'><input type='text' size='18'".$classDisplay." name='mdate".$i."' id='mdate".$i."' value='".stripcslashes($milestoneArray[$i]->milestoneDate)."'></div></td>";
        	echo "<td><div id='mtitlediv".$i."'><input type='text' ".$classDisplay." name='mtitle".$i."' value='".$milestoneArray[$i]->milestoneTitle."'></div></td>";
        	echo "<td><div id='mdescdiv".$i."'><input type='text' ".$classDisplay." name='mdesc".$i."' value='".$milestoneArray[$i]->milestoneDescription."'></div></td>";
        	echo "<td><div id='mlinkdiv".$i."'><input type='text' ".$classDisplay." name='mlink".$i."' value='".$milestoneArray[$i]->milestoneLink."'></div></td>";
        	echo "<td><div id='mlinkddiv".$i."'><input type='text' ".$classDisplay." name='mlinkd".$i."' value='".$milestoneArray[$i]->milestoneLinkDisplay."'></div></td>";
        	echo "<td><div id='mdaysdiv".$i."'><input type='text' ".$classDisplay." name='mdays".$i."' size='2' maxlength='2' value='".$milestoneArray[$i]->milestoneBeginDays."'></div></td>";
        	echo "<td style='align:center'><div id='momitdiv".$i."'><input type='checkbox' ".$classDisplay." name='momit".$i."' id='momit".$i."' value='".$milestoneArray[$i]->milestoneOmitCountdown."'></div></td>";
        	echo "<td><a href='' onclick='hsmilestones_removeMe(".$i.");return false;'>Remove</a></td>";
        	echo "</tr>";
        }
        $rowNumber = 0 + $size + 1;
    	echo "<tr>";
    	echo "<td>".($rowNumber)."</td>";
    	echo "<td><div id='mdatediv".$size."'><input type='text' size='18' name='mdate".$size."' id='mdate".$size."' value=''></div></td>";
    	echo "<td><div id='mtitlediv".$size."'><input type='text' name='mtitle".$size."'></div></td>";
    	echo "<td><div id='mdescdiv".$size."'><input type='text' name='mdesc".$size."'></div></td>";
    	echo "<td><div id='mlinkdiv".$size."'><input type='text' name='mlink".$size."'></div></td>";
    	echo "<td><div id='mlinkddiv".$size."'><input type='text' name='mlinkd".$size."'></div></td>";
    	echo "<td><div id='mdaysdiv".$size."'><input type='text' name='mdays".$size."' size='2' maxlength='2'></div></td>";
    	echo "<td style='align:center'><div id='momitdiv".$size."'><input type='checkbox' id='momit".$size."' name='momit".$size."' value=\"FALSE\"></div></td>";
    	echo "<td><a href='' onclick='hsmilestones_addMe(".$size.");return false;'>Add</a></td>";
    	echo "</tr>";
        echo "</table>";
		wp_nonce_field( 'post-hsmilestone-data', 'hsmilestone_nonce' );
        ?>
        <p class="submit">
        <input type="button" onClick="hsmilestones_submitMe()" name="Submit" value="<?php _e('Update Milestones', 'hs_milestones' ) ?>" />
        </p>
        
    </form>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-dateFormat/1.0/jquery.dateFormat.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.js"></script>
    <script language="javascript">
    var theForm = window.document.hsmilestones_form;
    var tableSize = <?php echo $size ?>;
	jQuery( document ).ready(function() {
		jQuery.datetimepicker.setLocale('en');
	    for(var i=0; i<=tableSize; i++) {
	    	var ckbox = window.document.getElementById('momit'+i);
	    	if(ckbox != null && ckbox.value == "TRUE") ckbox.checked = true;
			jQuery('#mdate'+i).datetimepicker({
			dayOfWeekStart : 0,
			step: 30,
			format:	'm/d/Y H:i',
			lazyInit: true,
			lang:'en',
			});

		}
	});    	

    function hsmilestones_removeMe(what) {
    	theForm.hsmilestones_action.value="R";
    	theForm.hsmilestones_actionOrdinal.value=what;
    	theForm.submit();
    }
    function hsmilestones_addMe(what) {
    	theForm.hsmilestones_action.value="A";
    	theForm.hsmilestones_actionOrdinal.value=what;
    	var issues = "";
		if(eval('theForm.mdate'+tableSize).value == "") {
    		issues += "You need to provide a valid date for row " + (tableSize + 1) + ".\n";
    		window.document.getElementById("mdatediv"+tableSize).style.borderColor = "red";
    		window.document.getElementById("mdatediv"+tableSize).style.borderStyle = "solid";
		}
		if(eval('theForm.mtitle'+tableSize).value == "") {
    		issues += "Event title must be entered for row " + (tableSize+1) + ".\n";
    		window.document.getElementById("mtitlediv"+tableSize).style.borderColor = "red";
    		window.document.getElementById("mtitlediv"+tableSize).style.borderStyle = "solid";
		}
		if(eval('theForm.mdays'+tableSize).value != "" && (parseInt(eval('theForm.mdays'+tableSize).value) < 1 || parseInt(eval('theForm.mdays'+tableSize).value) > 30 )) {
    		issues += "Days value entered for row " + (tableSize+1) + " is optional but should be between 1 and 30.\n";
    		window.document.getElementById("mdaysdiv"+tableSize).style.borderColor = "red";
    		window.document.getElementById("mdaysdiv"+tableSize).style.borderStyle = "solid";
		}

    	if(issues != "") {
    		alert(issues);
    		return false;
    	}
		var ckbox = window.document.getElementById('momit'+tableSize);
		if(ckbox.checked) {
			ckbox.value="TRUE";
		}
		else {
			ckbox.value="FALSE";
		}
    	theForm.submit();
    }
    function hsmilestones_submitMe() {
    	//Check form values for valid entries
    	theForm.hsmilestones_action.value="P";
    	var issues = "";
    	for(i=0; i<tableSize; i++) {
    		window.document.getElementById("mdatediv"+i).style.borderColor = "initial";
    		window.document.getElementById("mdatediv"+i).style.borderStyle = "none";
    		window.document.getElementById("mtitlediv"+i).style.borderColor = "initial";
    		window.document.getElementById("mtitlediv"+i).style.borderStyle = "none";
    		window.document.getElementById("mlinkdiv"+i).style.borderColor = "initial";
    		window.document.getElementById("mlinkdiv"+i).style.borderStyle = "none";
    		window.document.getElementById("mlinkddiv"+i).style.borderColor = "initial";
    		window.document.getElementById("mlinkddiv"+i).style.borderStyle = "none";
    		window.document.getElementById("mdaysdiv"+i).style.borderColor = "initial";
    		window.document.getElementById("mdaysdiv"+i).style.borderStyle = "none";
    	}
    	for(i=0; i<tableSize; i++) {
			if(eval('theForm.mdate'+i).value == "") {
	    		issues += "You need to provide a date and time for row " + (i+1) + ".\n";
	    		window.document.getElementById("mdatediv"+i).style.borderColor = "red";
	    		window.document.getElementById("mdatediv"+i).style.borderStyle = "solid";
    		}
    		if(eval('theForm.mtitle'+i).value == "") {
	    		issues += "You need to provide an event title for row " + (i+1) + ".\n";
	    		window.document.getElementById("mtitlediv"+i).style.borderColor = "red";
	    		window.document.getElementById("mtitlediv"+i).style.borderStyle = "solid";
    		}
			if(eval('theForm.mdays'+i).value != "") {
				if(parseInt(eval('theForm.mdays'+i).value) < 1 || 
				  parseInt(eval('theForm.mdays'+i).value) > 30) {
		    		issues += "Days value entered for row " + (i+1) + " is optional but should be between 1 and 30.\n";
		    		window.document.getElementById("mdaysdiv"+i).style.borderColor = "red";
		    		window.document.getElementById("mdaysdiv"+i).style.borderStyle = "solid";
				}
			}
			var ckbox = window.document.getElementById('momit'+i);
			if(ckbox.checked) {
				ckbox.value="TRUE";
			}
			else {
				ckbox.value="FALSE";
			}
  		
    	}
    	if(issues != "") {
    		alert(issues);
    		return false;
    	}
    	theForm.submit();
    }
    </script>
    	
</div>
