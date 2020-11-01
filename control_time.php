<?php
 header('Content-Type: application/json');
 date_default_timezone_set('America/Toronto');

class Control {
  public $distance;
  public $name;
  public $open;
  public $close;
  public function __construct( $dist, $nam, $open, $close ){
    $this->distance=$dist;
    $this->name=$nam;
    $this->open=$open;
    $this->close=$close;
  }
}

$radius= 6371 * 1.001;
$toRad = M_PI / 180.0;

function distance($lat1,$lon1,$lat2,$lon2){
  global $radius;
  $dLat = $lat2-$lat1;
  $dLon = $lon2-$lon1;
  $a = sin($dLat/2) * sin($dLat/2) + sin($dLon/2) * sin($dLon/2) * cos($lat1) * cos($lat2);
  $c = 2 * atan2(sqrt($a), sqrt(1-$a));
  return $radius * $c;
}
$speed=array('200'=>array('distance'=>200.0,'minspeed'=>15.0,'maxspeed'=>34.0,'maxtime'=>13.5,'maxhours'=>'13','maxminutes'=>'30'),
             '300'=>array('distance'=>300.0,'minspeed'=>15.0,'maxspeed'=>32.0,'maxtime'=>20.0,'maxhours'=>'20','maxminutes'=>'00'),
             '400'=>array('distance'=>400.0,'minspeed'=>15.0,'maxspeed'=>32.0,'maxtime'=>27.0,'maxhours'=>'27','maxminutes'=>'00'),
             '600'=>array('distance'=>600.0,'minspeed'=>15.0,'maxspeed'=>30.0,'maxtime'=>40.0,'maxhours'=>'40','maxminutes'=>'00'),
            '1000'=>array('distance'=>1000.0,'minspeed'=>11.428,'maxspeed'=>28.0,'maxtime'=>75.0,'maxhours'=>'75','maxminutes'=>'00'),
            '1300'=>array('distance'=>1300.0,'minspeed'=>13.333,'maxspeed'=>26.0,'maxtime'=>97.5,'maxhours'=>'97','maxminutes'=>'30'));

function lookUpClose($distance,$eventDistance){
  global $speed;
  if( $distance < 0.1 ) return 1.0;
  $dAc=0.0; $tAc=0.0;
  foreach( $speed as $key=>$spd ) {
   if( ($eventDistance == $key ) &&  ($distance >= floatval( $eventDistance )) ) 
         {  return $speed[$eventDistance]['maxtime'];  }
   if( $distance < $spd['distance'] ) { return $tAc+($distance - $dAc)/$spd['minspeed']; }
   $tAc += ( $spd['distance'] - $dAc )/$spd['minspeed'];
   $dAc = $spd['distance'];
  }
 return 90.0;
}
function lookUpOpen($distance,$eventDistance){
  global $speed;
  $dAc=0.0; $tAc=0.0;
  foreach( $speed as $key=>$spd ) {
   if( ( $eventDistance == $key ) && ($distance >= floatval( $eventDistance )) ) 
        {  return $tAc+($distance - $dAc)/$speed[$eventDistance]['maxspeed'];  }
   if( $distance < $spd['distance'] ) { return $tAc+($distance - $dAc)/$spd['maxspeed']; }
   $tAc += ( $spd['distance'] - $dAc )/$spd['maxspeed'];
   $dAc = $spd['distance'];
  }
 return 90.0;
}
/*
if ( !($_SERVER['REQUEST_METHOD'] ===  'GET')  || !(isset($_GET["chapter"])) || !(isset($_GET["track"])) || !(isset($_GET["distance"]))
                                                                             || !(isset($_GET["starttime"])) ) {
    header( 'Location: https://www.randonneursontario.ca/error500.html' ) ;
    exit;
 }
*/
if ( !($_SERVER['REQUEST_METHOD'] ===  'GET')  || !(isset($_GET["event"]))  ) {
    header( 'Location: https://www.randonneursontario.ca/error500.html' ) ;
    exit;
 }
function filterCue( $rawText ){
  if( preg_match('/(.*)start\s*date\s+\d\d\d\d-\d\d-\d\d(.*)/i',$rawText,$matches, PREG_OFFSET_CAPTURE) ) $rawText=strval($matches[1][0])." ".strval($matches[2][0]);
  if( preg_match('/(.*)start\s*time\s+\d\d.\d\d(.*)$/i',$rawText,$matches, PREG_OFFSET_CAPTURE) ) $rawText=strval($matches[1][0])." ".strval($matches[2][0]);
  if( preg_match('/(.*)\([a-zA-Z]{2}\)(.*)/',$rawText,$matches, PREG_OFFSET_CAPTURE) ) $rawText=strval($matches[1][0])." ".strval($matches[2][0]);
  if( preg_match('/(.*)\(ri\d+\)(.*)/',$rawText,$matches, PREG_OFFSET_CAPTURE) ) $rawText=strval($matches[1][0])." ".strval($matches[2][0]);
  return $rawText;
}
function getPlace( $rawText ){
  if( preg_match('/.*place(.*)/i',$rawText,$matches, PREG_OFFSET_CAPTURE) ) return trim(strval($matches[1][0]));
  $bits=explode(",",$rawText);
  $nbits=count( $bits );
  if( $nbits > 1 ) return trim(strval($bits[ $nbits - 1 ]));
  return trim($rawText);
}
function control_time_format($dT,$start){
$t=intval( $start ) + $dT*3600 + 30;
return date('D G:i',$t);
}

$event=json_decode( $_GET['event'] );
$starttime=$event->Unixtime;
$fdate=date('F j, Y',$starttime);
$distance=$event->Distance;
$result_json=array();
$path = array("Huron"=>"hurroutes","Ottawa"=>"ottroutes","Simcoe"=>"simroutes","Toronto"=>"torroutes");
//if( preg_match("/.*\.gpx.*/",$_GET["track"]) ) {
if( $event->RWGPS == "" ){
#  $filepath="../".$path[$_GET['chapter']]."/".$_GET['track'];
#  $xml=simplexml_load_file($filepath) or die("Error: Cannot create object");
$white=array(" ","'");
$gpxname=str_replace($white,'_',$event->Route).'_'.$distance.'.gpx';
$url="https://www.randonneursontario.ca/routes/".$path[$event->Chapter]."/".$gpxname;
$xml = file_get_contents($url);
if( ! $xml=simplexml_load_string($xml) ){
  echo json_encode ( (object) array('status'=>'error','message'=>'Failed to retrive XML file '.$gpxname)) ;
  exit;
  }
  $firstCue=true;
  $first=true;
  $trkDist=0.0;
  $cueDist=-2.0;  
foreach( $xml->trk as $trk ){
  foreach( $trk->trkseg as $trkseg ){
   foreach( $trkseg->trkpt as $trkpt ) {
   $lat=$trkpt['lat'];
   $lon=$trkpt['lon'];
   if( $first ) {
     $first=false;
    } else {
       $trkDist += distance(((float)$lastlat)*$toRad,((float)$lastlon)*$toRad,((float)$lat)*$toRad,((float)$lon)*$toRad);
    }
   $lastlat=$lat;
   $lastlon=$lon;
   $cmt=$trkpt->cmt;
   if( $trkpt->name ) {
       $name=$trkpt->name;
       if( preg_match("/control/i",$name) ) { $cmt='control '.$cmt;}
     }
   if( preg_match("/(start|control|finish|start:|finish:|control:)\s(.*)/i",$cmt,$parts) ) {
        if( $firstCue ) {
          $firstCue=false;
          if( $trkDist > 1.0 ) {
// if there is no cue in first 1 km just put in a control with blank name
           $result_json[]=new Control("0.0km"," ",0.0,1.0 );
          }
        }
    $cueDist=$trkDist;
    $result_json[]=new Control(number_format($trkDist, 1, '.', '')."km",getPlace(filterCue($parts[2])),control_time_format( lookUpOpen($trkDist,$distance),$starttime),control_time_format( lookUpClose($trkDist,$distance),$starttime) );
   }
  }}}
if( $cueDist < intval($distance) || ( $trkDist - $cueDist) > 1.0 ) {
// if track goes past the last cue by 1 km, insert a control with blank name unless the last control was past design distance
    $result_json[]=new Control(number_format($trkDist, 1, '.', '')."km"," ",control_time_format( lookUpOpen($trkDist,$distance),$starttime),control_time_format( lookUpClose($trkDist,$distance),$starttime) );
}
echo json_encode( (object) array('status'=>'ok','control_list'=>$result_json,'maxhours'=>$speed[$distance]['maxhours'],'maxminutes'=>$speed[$distance]['maxminutes'],'startdate'=>$fdate ));
  exit;
}
$url=$event->RWGPS.".tcx";
$xml = file_get_contents($url);
if( ! $xml=simplexml_load_string($xml) ){
  echo json_encode ( (object) array('status'=>'error','message'=>'Failed to retrive RWGPS file '.$url)) ;
  exit;
  }
$firstCue=true;
$first=true;
$trkDist=0.0;
foreach( $xml->Courses->Course as $crs ){
  $index=0;
  foreach( $crs->CoursePoint as $cpt) {
   $name=$cpt->Name;
   $lat=$cpt->Position->LatitudeDegrees;
   $lon=$cpt->Position->LongitudeDegrees;
   $notes=$cpt->Notes;
   if( preg_match("/(START|CONTROL|CTRL|CTL|FINISH)(.*)/i",$notes,$parts) ) {
    do {
       $crsave=$crs;
       $trkpt=$crs->Track->Trackpoint[$index++];
       $lat1=$trkpt->Position->LatitudeDegrees;
       $lon1=$trkpt->Position->LongitudeDegrees;
     if( $first ){
       $first=false;
     } else {
       $trkDist += distance(((float)$lastlat)*$toRad,((float)$lastlon)*$toRad,((float)$lat1)*$toRad,((float)$lon1)*$toRad);
     }
     $lastlat=$lat1;
     $lastlon=$lon1;
     $d=distance(((float)$lat)*$toRad,((float)$lon)*$toRad,((float)$lat1)*$toRad,((float)$lon1)*$toRad);
     if( $d < 1.0e-3 ) {
       if( ! preg_match("/of\s+route/",$parts[2] )) {
        if( $firstCue ) {
          $firstCue=false;
          if( $trkDist > 1.0 ) {
// if there is no cue in first 1 km just put in a control with blank name
           $result_json[]=new Control("0.0km"," ",0.0,1.0 );
          }
        }
        $openTime=control_time_format( lookUpOpen($trkDist,$distance),$starttime);
        $closeTime=control_time_format( lookUpClose($trkDist,$distance),$starttime);
        $result_json[]=new Control( number_format($trkDist, 1, '.', '')."km",getPlace(filterCue($parts[2])),$openTime,$closeTime );
      }
     }
    } while( $d >= 1.0e-3 );
   }
  }
}
if( $firstCue ){
// no controls at all
  $result_json[]=new Control("0.0km"," ",control_time_format(0.0,$starttime),control_time_format(1.0,$starttime) );
}
if( $trkDist < intval($distance) ) {
// the last cue occured before design distance, so run to end of track and put in a control with blank name
           $lastTrackLength = count( $crsave->Track->Trackpoint );
           while( $index < $lastTrackLength ) {
             $trkpt=$crsave->Track->Trackpoint[$index++];
             $lat1=$trkpt->Position->LatitudeDegrees;
             $lon1=$trkpt->Position->LongitudeDegrees;
             $trkDist += distance(((float)$lastlat)*$toRad,((float)$lastlon)*$toRad,((float)$lat1)*$toRad,((float)$lon1)*$toRad);
             $lastlat=$lat1;
             $lastlon=$lon1;
           }
        $result_json[]=new Control(number_format($trkDist, 1, '.', '')."km"," ",control_time_format( lookUpOpen($trkDist,$distance),$starttime),control_time_format( lookUpClose($trkDist,$distance),$starttime) );
}
echo json_encode( (object) array('status'=>'ok','control_list'=>$result_json,'maxhours'=>$speed[$distance]['maxhours'],'maxminutes'=>$speed[$distance]['maxminutes'],'startdate'=>$fdate ));
?>
