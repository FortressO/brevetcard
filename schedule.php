<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('America/Toronto');
include './mysql.php';
//include './dsn.php';

if( $_SERVER['REQUEST_METHOD'] ===  'GET' ) {
  $id = empty($_GET["id"]) ? "%" : $_GET["id"];
  $chapter = empty($_GET["chapter"]) ? "%" : $_GET["chapter"];
  $from = empty($_GET["date"]) ? "1970-01-01" : $_GET["date"];
  $from = empty($_GET["from"]) ? $from : $_GET["from"];
  $to = empty($_GET["to"]) ? "2999-12-31" : $_GET["to"];
  $limit = empty($_GET["date"]) ? 999 : 1;
  $query = 'SELECT * FROM Schedule WHERE
    Sched_ID LIKE :id &&
    Chapter LIKE :chapter && 
    (Date between :from AND :to)
    ORDER By Date ASC Limit :limit';
 try {
    $conn = new PDO($dsn, $username, $password);
// force an exception if the following does not work out
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $rows = array();
    $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL)); 
    $stmt->bindValue(':id', $id);
    $stmt->bindValue(':chapter', $chapter);
    $stmt->bindValue(':from', $from . ' 00:00:00');
    $stmt->bindValue(':to', $to . ' 00:00:00');
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
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
