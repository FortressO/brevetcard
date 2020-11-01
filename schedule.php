<?php
include './mysql.php';
//include './dsn.php';
if( $_SERVER['REQUEST_METHOD'] ===  'GET' ) {
 header('Content-Type: application/json');
 date_default_timezone_set('America/Toronto');
 if( !empty($_GET['id']) ) {
   $query="SELECT * FROM Schedule WHERE Sched_Id=".$_GET['id'];
   } else {
     if( !empty($_GET["chapter"] ) ){ $chapter=" Chapter=\"".$_GET["chapter"]."\" && ";}
        else {$chapter="";}
     if( !empty($_GET["date"] ) ){ $date=$_GET["date"]; $limit=" Limit 1";}
        else {$date="2019-01-01"; $limit="";}
     if( !empty($_GET["from"] ) ){ $date=$_GET["from"]; $limit=" ";}
        else {$date="2019-01-01"; $limit="";}
     if( !empty($_GET["to"] ) ){ $to=$_GET["to"]; $limit=" ";}
        else {$to="2019-12-31"; $limit="";}
    $query="SELECT * FROM Schedule WHERE (".$chapter."(DATE between '".$date." 00:00:00'  AND '".$to."  00:00:00'  ))  ORDER BY Date ASC".$limit;
    }
 try {
    $conn = new PDO($dsn, $username, $password);
// force an exception if the following does not work out
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $rows = array();
    $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL)); 
    $stmt->execute();
    while( $r = $stmt->fetch(PDO::FETCH_ASSOC) ) { 
    $midnight =  strtotime($r['Date']) ;
    preg_match('/(\d\d):(\d\d):(\d\d)/', $r['Stime'], $matches, PREG_OFFSET_CAPTURE);
    $r += ['Unixtime' => ( 3600*intval($matches[1][0]) + 60*intval($matches[2][0])+intval($matches[3][0]) ) + $midnight ];
    $rows[] = $r; } 
    echo json_encode( (object) array('status'=>'ok','schedule'=>$rows ));
 }
 catch(PDOException $e) {
  echo json_encode ( (object) array('status'=>'error','message'=>$e->getMessage())) ;
 }
 $conn = null;
}
?>
