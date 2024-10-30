<?php 
    /*
    Plugin Name: HedmanSoft Milestones
    Plugin URI: http://hedmansoft.com/milestones-plugin-page/
    Description: Plugin for displaying recurring milestones in the widget area
    Author: D. Hedman
    Version: 1.0
    Author URI: http://www.hedmansoft.com
    */

class hsmilestones_Milestone {
	public $milestoneDate;
	public $milestoneTitle;
	public $milestoneDescription;
	public $milestoneLink;
	public $milestoneLinkDisplay;
	public $milestoneBeginDays;
	public $milestoneOmitCountdown;
}
	

function hsmilestones_admin() {
    include('hedmansoft_milestones_admin.php');
}
function hsmilestones_admin_actions() {
 add_options_page("HedmanSoft Milestones", "HedmanSoft Milestones", 1, "HedmanSoft_Milestones", "hsmilestones_admin");
}

function hsmilestones_stylesheet( $posts ) {
	wp_enqueue_style( 'hs_milestones_css', plugins_url( '/hs_milestones.css', __FILE__ ) );
}
function hsmilestones_admin_enqueue( $posts ) {
	wp_enqueue_script( 'hs_milestones_script', plugin_dir_url( __FILE__ ) . 'jquery.datetimepicker.full.min.js', array('jquery'), '1.0' );
	wp_enqueue_style( 'hs_milestones', plugins_url( '/jquery.datetimepicker.css', __FILE__ ) );
}
 
add_action('admin_menu', 'hsmilestones_admin_actions');
add_action('wp_enqueue_scripts', 'hsmilestones_stylesheet');
add_action('admin_enqueue_scripts', 'hsmilestones_admin_enqueue' );

function hsmilestones_widget_display($atts) {
	$a = shortcode_atts( array('cutoff-days' => 30, 'show-header'=>'TRUE', 'events-header' => 'UPCOMING EVENTS', 'datetime-format' => 'm/d/Y h:i a'), $atts );
	$cutoffDays = $a['cutoff-days'];
	$eventsHeader = $a['events-header'];
	$dateTimeFormat = $a['datetime-format'];
	$showHeader = $a['show-header'];
	$hs_milestonesConfiguration = get_option("hs_milestones_configuration");
	$milestoneArray = $hs_milestonesConfiguration['hs_milestoneArray'];
	$size = count($milestoneArray);
	$htmlSrc = '<div class="hs-milestones-wrapper">';
	if($showHeader == "TRUE") {
		$htmlSrc = $htmlSrc.'<div class="hs-milestones-header">'.$eventsHeader.'</div>';
	}
	$nowDateTime = new DateTime();
	$tzoffset = intval(hsmilestones_getMyTimeZoneOffset());
	if($tzoffset < 0) {
		$nowDateTime->sub(new DateInterval('PT'.abs($tzoffset).'H'));
	}
	else {
		$nowDateTime->add(new DateInterval('PT'.$tzoffset.'H'));
	}
	$eventCounter = 0;
	for($i=0; $i<$size; $i++) {
		$thisResult = $milestoneArray[$i];
    	$milestoneDateTime = new DateTime(stripcslashes($thisResult->milestoneDate)); 
		$diffHours = ($milestoneDateTime->getTimestamp() - $nowDateTime->getTimestamp()) / 3600;
		$diffDays = ceil($diffHours/24);
		if($diffHours < 24) $diffDays = 0;
		$eventHtmlSrc = "";
		$beginDays = $thisResult->milestoneBeginDays != null? (int)$thisResult->milestoneBeginDays: $cutoffDays;
		if($diffHours > -1 && $diffDays <= $beginDays) {
			$eventHtmlSrc = $eventHtmlSrc.'<div class="hs-milestone-block">';
			$eventHtmlSrc = $eventHtmlSrc.'  <div class="hs-milestone-header">'.stripcslashes($thisResult->milestoneTitle).'</div>';
			if($thisResult->milestoneDescription) {
				$eventHtmlSrc = $eventHtmlSrc.'  <div class="hs-milestones-event">'.stripcslashes($thisResult->milestoneDescription).'</div>';
			}
			//Look for timestamp in the date format so we can place a <BR> between date and time
			$dateOnlyFormat = $dateTimeFormat;
			$timeOnlyFormat = "";
			$pos = strpos(strtoupper($dateTimeFormat) , "H");
			if($pos > -1) {
				$dateOnlyFormat = substr($dateTimeFormat, 0, $pos - 1);
				$timeOnlyFormat = substr($dateTimeFormat, $pos);
			}
			if($thisResult->milestoneOmitCountdown != "TRUE") {
				//Display date and time in 2 rows
				$eventHtmlSrc = $eventHtmlSrc.'  <div class="hs-milestones-date">'.date($dateOnlyFormat, $milestoneDateTime->getTimestamp());
				if($pos > -1) {
					$eventHtmlSrc = $eventHtmlSrc.'</div><div class="hs-milestones-date">'.date($timeOnlyFormat, $milestoneDateTime->getTimestamp());
				}
				$eventHtmlSrc = $eventHtmlSrc.'</div>';
			}
			else {
				//Display date and time as one row when the countdown is suppressed
				$eventHtmlSrc = $eventHtmlSrc.'  <div class="hs-milestones-date">'.date($dateTimeFormat, $milestoneDateTime->getTimestamp()).'</div>';
			}
			if($thisResult->milestoneLink) {
				$eventHtmlSrc = $eventHtmlSrc.'  <div class="hs-milestones-event"><a href="" onclick="window.open(\''.stripcslashes($thisResult->milestoneLink).'\');return false;">';
				if($thisResult->milestoneLinkDisplay) {
					$eventHtmlSrc = $eventHtmlSrc.$thisResult->milestoneLinkDisplay;
				}
				else {
					$eventHtmlSrc = $eventHtmlSrc.'Click for more information';
				}
				$eventHtmlSrc = $eventHtmlSrc.'</a></div>';
			}
			if($thisResult->milestoneOmitCountdown != "TRUE") {
				$eventHtmlSrc = $eventHtmlSrc.'  <div class="hs-milestone-countdown">';
				$diffUnits = $diffDays == 1? "day" : "days";
				$diff = $diffDays;
				$nowStyle = "";
				if(round($diffDays) < 1) {
					$diffUnits = round($diffHours) == 1? "hour" : "hours";
					$diff = round($diffHours);
					$diffMinutes = round($diffHours * 60);
					if(round($diffHours) < 1) {
						if($diffMinutes > 0) {
							$diff = $diffMinutes;
							$diffUnits = $diffMinutes == 1?"minute" : "minutes";
						}
						else {
							$diffUnits = "";
							$nowStyle=" style='color:red; '";
							$diff = "Now";
						}
					}
				}
				if($diff != "Now") {
					$diffUnits = $diffUnits." to go";
				}
				$eventHtmlSrc = $eventHtmlSrc.'  <span class="hs-milestone-difference"'.$nowStyle.'>'.$diff.'</span> ';
				$eventHtmlSrc = $eventHtmlSrc.'  <span class="hs-milestone-label">'.$diffUnits.'</span>';
				$eventHtmlSrc = $eventHtmlSrc.'  </div>';
			}
			$htmlSrc = $htmlSrc.$eventHtmlSrc.'</div>';
			$eventCounter ++;
		}
	}
	if($eventCounter == 0) {
		$htmlSrc = $htmlSrc.'  <div class="hs-milestones-event">No upcoming events found.</div>';
	}
	$htmlSrc = $htmlSrc.'</div>';
	return $htmlSrc;
}


function hsmilestones_display_func( $atts ){
	return hsmilestones_widget_display($atts);
}

function hsmilestones_getMyTimeZoneOffset() { 
	$gmtOffset = 0;
	if(get_option('gmt_offset') != null) {
		$gmtOffset =  get_option('gmt_offset');
	}
	return $gmtOffset;
}
add_shortcode( 'hs_milestones', 'hsmilestones_display_func' );
  
?>
