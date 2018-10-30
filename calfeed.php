<?php
function calfeed($result, $teamId){
if(is_numeric($teamId)){
		$calname = $result[0]['leagueShort'] . " ATV/KV Basel";
	}else{
		$calname = "ATV/KV Basel";
	}
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename=kalender.ics');
//print_r($result);
	// the iCal date format. Note the Z on the end indicates a UTC timestamp.
date_default_timezone_set('Europe/Zurich');
define('DATE_ICAL', 'Ymd\THis\Z');
// max line length is 75 chars. New line is \\n
$caldata ="BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//ATVKV Basel//CalDAV Client//EN
METHOD:PUBLISH
X-WR-CALNAME: " . $calname . "
X-WR-TIMEZONE:Europe/Zurich
BEGIN:VTIMEZONE
TZID:W. Europe Standard Time
BEGIN:STANDARD
DTSTART:20131002T030000
RRULE:FREQ=YEARLY;BYDAY=-1SU;BYHOUR=3;BYMINUTE=0;BYMONTH=10
TZNAME:W. Europe Standard Time
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
END:STANDARD
BEGIN:DAYLIGHT
DTSTART:20130301T020000
RRULE:FREQ=YEARLY;BYDAY=-1SU;BYHOUR=2;BYMINUTE=0;BYMONTH=3
TZNAME:W. Europe Daylight Time
TZOFFSETFROM:+0100
TZOFFSETTO:+0200
END:DAYLIGHT
END:VTIMEZONE\n";
// loop over events
foreach ($result as $termin):
$date = $termin['gameDateTime'];
$caldata .= "BEGIN:VEVENT
CREATED: 20181027T151648Z
SUMMARY:(".$termin['leagueShort'].") ".$termin['teamAName']." vs. ". $termin['teamBName']."
UID:".$termin['gameId']."@".$termin['leagueShort'].".atvkv.ch
DTSTAMP:" . gmdate(DATE_ICAL)."
DTSTART:" . gmdate(DATE_ICAL, strtotime($date))."
DTEND:" . gmdate(DATE_ICAL, strtotime($date)+5400)."
LAST-MODIFIED:" . gmdate(DATE_ICAL)."
LOCATION:".$termin['venue']." \n ".$termin['venueAddress']." \n ".$termin['venueZip']." \n ".$termin['venueCity']."
END:VEVENT\n";
endforeach;
// close calendar
$caldata .= "END:VCALENDAR";
$trimmed = trim($caldata);
print_r($trimmed);
}