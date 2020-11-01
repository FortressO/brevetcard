<?php
if( $_SERVER['REQUEST_METHOD'] !==  'POST' ) {
  header( 'Location: /error500.html' ) ;
}
  if( ! isset( $_POST['distance'] ) ) {  $distance=" ";
     } else  { $distance = $_POST['distance']; }
  if( ! isset( $_POST['evname'] ) ) { $evname=" ";
     } else  { $evname = $_POST['evname']; }
  if( ! isset( $_POST['evstart'] ) ) { $evstart=" ";
     } else  { $evstart=$_POST['evstart']; }
  if( ! isset( $_POST['maxhours'] ) ) { $maxhours=" ";
     } else  { $maxhours=$_POST['maxhours']; }
  if( ! isset( $_POST['maxminutes'] ) ) { $maxminutes=" ";
     } else  { $maxminutes=$_POST['maxminutes']; }
  if( ! isset( $_POST['emergtel'] ) ) { $emergtel=" ";
     } else  { $emergtel=$_POST['emergtel']; }
  $r=array();
  if( isset( $_POST['riderlist'] ) ) {
    $riderlist=$_POST['riderlist'];
    $r = json_decode( $riderlist , true );
  }
  $controllist=array();
  if( isset( $_POST['controllist'] ) ) {
    $controllist = json_decode( $_POST['controllist'] , true );
  }
  $pages=1;
  $nums=count( $r );
  $pages=floor($nums/2);
  if( ($nums % 2 != 0) || ( $pages == 0 ) ) $pages++;

function truncWord($pdf,$sentence,$length,$fontheight){
  if( $sentence == "" ) return $sentence;
  $warray=explode(" ",$sentence);
  if( count( $warray ) == 1 ) return $sentence;
  $sentence=$warray[0];
  $i=1;
  do{
    $w=$pdf->GetStringWidth( $sentence." ".$warray[$i]);
    if( $w > $length ) return $sentence;
    $sentence=$sentence." ".$warray[$i];
    $i++;
  } while( $i < count( $warray ) );
  return $sentence;
}

function squeeze($pdf,$str,$fontsize,$length){
  $fref=$fontsize;
  do{
    $pdf->SetFontSize($fontsize);
    $w=$pdf->GetStringWidth( $str );
    if( $w < $length ) return $fontsize;
    $fontsize -= 0.2;
  } while( ($fref - $fontsize) <  2.0 ); 
  return $fontsize;
}
class Column {
  public $marginL1;
  public $marginL2;
  public $marginL3;
  public $marginR1;
  public $marginR2;
  public $line1;
  public $line2;
  public $line3;
  function __construct($marginL1,$marginR1,$marginR2,$marginL2,$marginL3,$line1,$line2,$line3){
   $this->marginL1=$marginL1;
   $this->marginL2=$marginL2;
   $this->marginL3=$marginL3;
   $this->marginR1=$marginR1;
   $this->marginR2=$marginR2;
   $this->line1=$line1;
   $this->line2=$line2;
   $this->line3=$line3;
  }
}
$threeCol=array( new Column(6.9,35.4,70.0,36.4,49.4,35.4,48.4,73.0),
                 new Column(77.0,106.0,141.0,108.0,120.0,106.0,119.0,144.0),
                 new Column(147.5,176.0,208.0,177.0,190.0,176.0,189.6,209.5) );

$yOffset=array('top'=>6.4,'bottom'=>139.7);
$finepitch=3.7;

// calculate and assign variables for control grid layout
  $ncontrol=count( $controllist );
  if( $ncontrol <= 12 ){
   $nrows = 4;
//   $rowheight = 26;
   $rowheight = (144.0-12.8-15.8)/$nrows;
   $dy=5.9;
   $fontheight=14;
  } else {
     if( $ncontrol <= 15 ){
      $nrows = 5;
      $rowheight = 22;
      $dy=4.65;
      $fontheight=12;
     } else {
       $nrows = 6;
       $rowheight = 18;
       $dy = 4.0;
       $fontheight = 10;
       }
  }
$colPitch=73;
require('./transform.php');

$pdf = new PDF_Transform();

while( $pages > 0 ){

$pdf->AddPage('P','Letter');
// sets  width = 215.9mm height = 279.4mm
$pdf->SetMargins(6.4,6.4,6.4);

// layout marks absolute reference to the paper
// comment out this block to remove layout
$pdf->SetLineWidth(0.07);
//$pdf->Rect(6.4,6.4,(215.9-12.8),(279.4-3*6.4) );
$pdf->Line( 69.0, 6.4, 69.0, 6.4+5.0 );
$pdf->Line( 144.0, 6.4, 144.0, 6.4+5.0 );
$pdf->Line( 69.0, 139.7-5.0, 69.0, 139.7+5.0 );
$pdf->Line( 144.0, 139.7-5.0, 144.0, 139.7+5.0 );
$pdf->Line( 69.0, 261.6, 69.0, 266.6 );
$pdf->Line( 144.0, 261.6, 144.0, 266.6 );
// middle of page
$pdf->Line( 6.4, 139.7-1.5, 209.5, 139.7-1.5 );
// end of layout block
//
// 7 fonts are used in the document
// FPDF will automatically embed and subset the fonts - it says some where ...
$pdf->AddFont('PTSansNarrow-Regular','','PTSansNarrow-Regular.php');
$pdf->AddFont('PTSansNarrow-Bold','','PTSansNarrow-Bold.php');
$pdf->AddFont('OpenSans-Regular','','OpenSans-Regular.php');
$pdf->AddFont('OpenSans-Bold','','OpenSans-Bold.php');
$pdf->AddFont('OpenSans-Italic','','OpenSans-Italic.php');
$pdf->AddFont('OpenSans-Light','','OpenSans-Light.php');
$pdf->AddFont('OpenSans-SemiBold','','OpenSans-SemiBold.php');


foreach( $yOffset as $y0 ){
// column 1
$pdf->SetMargins(6.4,6.4,6.4);
$pdf->SetY( $y0+1.0 );
$pdf->SetFont('PTSansNarrow-Bold','',12);
$pdf->StartTransform();
$pdf->ScaleX(56, 6.4, 6.4);
$pdf->Write($finepitch,'REGULATIONS: ');
$pdf->SetFont('PTSansNarrow-Regular','',12);
$pdf->Write($finepitch,'Each participant is to be considered a private excursion');
$pdf->Ln($finepitch);
$pdf->Write($finepitch,'and remains responsible for any accidents in which they may be involved.');
$pdf->Ln($finepitch);
$pdf->Write($finepitch,'Each participant is responsible for following the route. Although');
$pdf->Ln($finepitch);
$pdf->Write($finepitch,'Randonneurs Ontario will endeavor to ensure that all route instructions');
$pdf->Ln($finepitch);
$pdf->Write($finepitch,'are correct, no responsibility can be accepted for participants becoming');
$pdf->Ln($finepitch);
$pdf->Write($finepitch,'lost. Should a participant become lost or stranded by mechanical problems');
$pdf->Ln($finepitch);
$pdf->Write($finepitch,'or fatigue, it will be their responsibility to get home.');
$pdf->Ln(6.5);
$pdf->SetFont('PTSansNarrow-Bold','',12);
$pdf->Write($finepitch,'There will be no "sag wagon"');
$pdf->Ln(6.5);
$pdf->Write($finepitch,"CONTROL CARD:");
$pdf->SetFont('PTSansNarrow-Regular','',12);
$pdf->Write($finepitch,"The participant to whom this card is issued must present");
$pdf->Ln($finepitch);
$pdf->Write($finepitch,"it at each control for the official stamp, signature and control time. Loss of");
$pdf->Ln($finepitch);
$pdf->Write($finepitch,"this card, or absence of any of the control stamps, or any irregularity in");
$pdf->Ln($finepitch);
$pdf->Write($finepitch,"stamping or signing of the card will result in disqualification.");
$pdf->Ln(6.0);

$pdf->SetFont('PTSansNarrow-Bold','',12);
$pdf->Write($finepitch,"CONDUCT: ");
$pdf->SetFont('PTSansNarrow-Regular','',12);
$pdf->Write($finepitch," Participants must at all times obey the rules of the road and");
$pdf->Ln($finepitch);
$pdf->Write($finepitch,"conduct themselves in a manner which will not discredit the Randonneurs");
$pdf->Ln($finepitch);
$pdf->Write($finepitch,"Ontario organization. Failure to do so will result in disqualification.");
$pdf->Ln(6.0);


$pdf->SetFont('PTSansNarrow-Bold','',12);
$pdf->Write($finepitch,"CYCLE: ");
$pdf->SetFont('PTSansNarrow-Regular','',12);
$pdf->Write($finepitch,"Any cycle permitted (bicycle, tandem, tricycle etc.) providing it is");
$pdf->Ln($finepitch);
$pdf->Write($finepitch,"powered by muscle power alone. Powerful front and rear lights");
$pdf->Ln($finepitch);
$pdf->Write($finepitch,"must be attached to the cycle night and day. The cycle must be in good");
$pdf->Ln($finepitch);
$pdf->Write($finepitch,"mechanical condition to participate in the event.");
$pdf->Ln(6.0);
$pdf->SetFont('PTSansNarrow-Bold','',12);
$pdf->Write($finepitch,"ASSISTANCE: ");
$pdf->SetFont('PTSansNarrow-Regular','',12);
$pdf->Write($finepitch,"Each participant must provide for his/her needs");
$pdf->Ln($finepitch);
$pdf->Write($finepitch,"during the event. Following vehicles are not permitted. Mechanical and");
$pdf->Ln($finepitch);
$pdf->Write($finepitch,"personal assistance may only be received at control points.");

$pdf->StopTransform();
$pdf->SetLineWidth(0.6);
$pdf->Rect(7.9,$y0+98.0,59.6,18.0);
$pdf->SetXY(7.9,$y0+112.0);
$pdf->SetFont('PTSansNarrow-Regular','',10);
$pdf->Cell(59.6,3.45,"MACHINE EXAMINER'S STAMP & SIGNATURE",0,0,'C');
// column 2
$pdf->SetFont('PTSansNarrow-Regular','',11);
$pdf->SetXY(73.0,$y0+2.0);
$pdf->Cell(10.0,4.0,"SCHEDULED");
$pdf->SetXY(73.0,$y0+6.0);
$pdf->Cell(10.0,4.0,"START TIME:");
$pdf->Line(93.0,$y0+9.0,109.0,$y0+9.0);
$pdf->SetXY(109.0,$y0+6.0);
$pdf->Cell(10.0,4.0,"DATE:");
$pdf->Line(119,$y0+9.0,140.0,$y0+9.0);
$pdf->SetXY(73.0,$y0+12.0);
$pdf->Cell(10.0,4.0,"FINISH TIME:");
$pdf->Line(93.0,$y0+15.0,109.0,$y0+15.0);
$pdf->SetXY(109.0,$y0+12.0);
$pdf->Cell(10.0,4.0,"DATE:");
$pdf->Line(119,$y0+15.0,140.0,$y0+15.0);
$pdf->SetXY(73.0,$y0+18.0);
$pdf->Cell(10.0,4.0,"TOTAL ALLOWABLE TIME");
$pdf->SetXY(73.0,$y0+22.0);
$pdf->Cell(10.0,4.0,"HRS.    ".$maxhours);
$pdf->Line(83.0,$y0+26.0,94.0,$y0+26.0);
$pdf->SetXY(95.0,$y0+22.0);
$pdf->Cell(10.0,4.0,"MIN.    ".$maxminutes);
$pdf->Line(104,$y0+26.0,115.0,$y0+26.0);
$pdf->SetXY(73.0,$y0+31.0);
$pdf->Cell(10.0,4.0,"TIME RIDER COMPLETED DISTANCE");
$pdf->SetXY(73.0,$y0+36.0);
$pdf->Cell(10.0,4.0,"HRS.");
$pdf->Line(83.0,$y0+39.0,94.0,$y0+39.0);
$pdf->SetXY(95.0,$y0+36.0);
$pdf->Cell(10.0,4.0,"MIN.");
$pdf->Line(104,$y0+39.0,115.0,$y0+39.0);

$pdf->SetXY(73.0,$y0+43.0);
$pdf->Cell(10.0,4.0,"QUALIFIED");
$pdf->SetXY(73.0,$y0+48.0);
$pdf->Cell(6.0,4.0,"Yes");
$pdf->Rect(81.0,$y0+48.0,8.0,4.0);
$pdf->SetXY(95.0,$y0+48.0);
$pdf->Cell(6.0,4.0,"No");
$pdf->Rect(102.0,$y0+48.0,8.0,4.0);

$pdf->Line(73,$y0+63.0,140.0,$y0+63.0);
$pdf->SetXY(73.0,$y0+66.0);
$pdf->SetFont('OpenSans-Italic','',11);
$pdf->Cell(66.0,4.0,"Signature of Official");
$pdf->Rect(75.0,$y0+73.0,66.0,50.0);

// column 3
$pdf->Image('rologo.png',150.0,$y0+1.0,57.0,42.0);
$pdf->SetXY(145.0,$y0+46.0);
$pdf->SetFont('PTSansNarrow-Bold','',18);
$pdf->StartTransform();
$pdf->ScaleX(56, 177.0, $y0+46.0);
$pdf->Cell(64.0,4,"CONTROL CARD");
$pdf->StopTransform();

//$pdf->SetXY(151.0,$y0+52.0);
//$pdf->StartTransform();
//$pdf->ScaleX(56, 151.0, $y0+52.0);
//$pdf->SetFont('OpenSans-Light','',14);
//$pdf->Cell(64.0,4,"Event:");
//$pdf->StopTransform();

$evn=$evname;
if( 0 !== preg_match('/(.*?)\s+(\d+)$/',trim( $evname ),$match) ) {
  if( 0 !== preg_match('/(200|300|400|600|1000)/',$match[2]) ) {
    $evn=$match[1];
  }
}

$pdf->SetFont('OpenSans-SemiBold','',12);
$fsize=4;
do{
  $fsize++;
  $pdf->SetFontSize( $fsize );
  $w = $pdf->GetStringWidth( $evn );
  } while($fsize < 19 && $w<=56.0 );

$pdf->SetXY(151.0,$y0+52.0);
$pdf->Cell(56.0,5,$evn,0,0,'C');
$pdf->SetXY(151.0,$y0+60.0);
if( $distance !== " " ) $pdf->Cell(56.0,5,$distance.' km',0,0,'C');

$pdf->SetFont('OpenSans-Regular','',12);
$f=array_pop( $r );
if( $f !=null && $f['name'] != "" ) {
 $rider="Rider: ".$f['name'];
 $fsize=13;
 do{
  $fsize--;
  $pdf->SetFontSize( $fsize );
  $w = $pdf->GetStringWidth( $rider );
  } while($fsize > 4 && $w>=56.0 );
 }
 else {
     $rider="Rider:";
    }
$pdf->SetXY(151.0,$y0+67.0);
$pdf->Cell(64.0,4,$rider);
$pdf->SetFontSize( 12 );

$pdf->SetXY(151.0,$y0+73.0);
$pdf->Cell(64.0,4,'Date: '.$evstart);
$pdf->SetMargins(151,6.4,6.4);
$pdf->SetFontSize(11.25);
$pdf->SetXY(151.0,$y0+80.0);
$pdf->Write(4.8,"Number: ");
$pdf->Ln(4.8);
$pdf->Write(4.8,"Event Organized Under the Rules and Regulations");
$pdf->Ln(4.8);
$pdf->Write(4.8,"of Les Randonneurs Mondiaux.");
$pdf->Ln(4.8);
$pdf->Write(4.8,"Emergency Contact Ride Organizer:");
$pdf->Ln(4.8);
$pdf->Write(4.8,"Tel: ".$emergtel);
$pdf->Line(160,$y0+118.0,200.0,$y0+118.0);


}

// back of sheet

$pdf->AddPage('P','Letter');
$pdf->SetLineWidth(0.07);
$pdf->SetMargins(6.4,6.4,6.4);
$pdf->SetXY(6.4,6.4);
$pdf->SetFont('OpenSans-Regular','',12);
// layout marks absolute reference to the paper
// comment out this block to remove layout
//$pdf->Rect(6.4,6.4,(215.9-12.8),(279.4-3*6.4) );
$pdf->Line( 73.0, 6.4, 73.0, 6.4+5.0 );
$pdf->Line( 144.0, 6.4, 144.0, 6.4+5.0 );
$pdf->Line( 73.0, 139.7-5.0, 73.0, 139.7+5.0 );
$pdf->Line( 144.0, 139.7-5.0, 144.0, 139.7+5.0 );
$pdf->Line( 73.0, 261.6, 73.0, 266.6 );
$pdf->Line( 144.0, 261.6, 144.0, 266.6 );
// middle of page
$pdf->Line( 6.4, 139.7-1.5, 209.5, 139.7-1.5 );
// end of layout block
$pdf->SetLineWidth(0.38);
$x0=6.4;

foreach( $yOffset as $y0 ){
// make header
  $pdf->Image("rologo_thumb.png",$x0+1.0,$y0+0.5,9.74,7.03);
  $pdf->Image("rologo_thumb.png",$x0+192.36,$y0+0.5,9.74,7.03);
  $pdf->Line( 6.4, $y0+8, 209.5, $y0+8);
  $pdf->SetFont('OpenSans-Regular','',$fontheight);
  $pdf->SetFontSize(13.0);
  $pdf->SetXY($x0+1.0+9.74,$y0+1.5);
  $pdf->Cell(144.0-69.0-9.74,5.3,'Randonneurs Ontario',0,0,'L');
  $pdf->SetXY(73.0,$y0+1.5);
  if( $evname !== " " ){
   $fsize=13.0;
   $w=$pdf->GetStringWidth( $evname );
   if( $w > 70.0 ){
    $fsize = 11.0;
    $pdf->SetXY(73.0,$y0+1.25);
   }
   $pdf->SetFontSize( $fsize );
   $shortname=truncWord($pdf,$evname,55.0,$fsize);
   $pdf->Cell(144.0-69.0,5.3,$shortname.' '.$distance.' km',0,0,'C');
   $pdf->SetFontSize(13.0);
  }
  $pdf->SetXY(144.0,$y0+1.5);
  $pdf->Cell(144.0-69.0-9.74,5.3,$evstart,0,0,'C');
  $pdf->SetFontSize(9.0);
  $pdf->SetFont('PTSansNarrow-Regular','',9);
  $linebottom = ( $nrows * $rowheight + 15.8 ) + $y0  ;
foreach($threeCol as $column){
  $pdf->Text($column->marginL1 ,$y0+11.7,"Control Location &");
  $pdf->Text($column->marginL1 ,$y0+15.3,"Open/Close Time");
  $pdf->Text($column->marginL2 ,$y0+13,"Time");
  $pdf->Text($column->marginL3 ,$y0+11.7,"Seal & Signature");
  $pdf->Text($column->marginL3 ,$y0+15.3,"of Control");
// make vertical column lines
//  $pdf->Line($column->line1,$y0+15.8,$column->line1,$linebottom);
  $pdf->Line($column->line3,$y0+8.0,$column->line3,$linebottom);
}
// fill in the cells
  $pdf->SetFont('OpenSans-Regular','',$fontheight);
$col=0;
  $jControl=0;
  do {
    for($i=0;$i<$nrows && $jControl < $ncontrol;$i++){
      $control=$controllist[$jControl++];
      $pdf->SetFont('OpenSans-Bold','',$fontheight);

// it doesn't fit
      $s="makeLine";
      $fsize=$fontheight;
      $decrease=0;
      do {
         $pdf->SetFontSize( $fsize );
         $w = $pdf->GetStringWidth( $control['name'] );
         $l = $threeCol[$col]->marginR1 - $threeCol[$col]->marginL1 - 0.3 ;
         if( $w <= $l ) {
           $s="fits";
         }
         $fsize--;
         $decrease++;
      } while( ($decrease < 7 ) && ( $w > $l) );
        $fsize++;
//      $pdf->Text($threeCol[$col]->marginL3, 2*$dy+$y0+15.8+$i*$rowheight,"w ".number_format($w,2)." l ".number_format($l,2));
      switch($s) {
       case "fits":
        $pdf->Line( $threeCol[$col]->line1,$y0+15.8+$i*$rowheight, $threeCol[$col]->line1, $y0+15.8+($i+1)*$rowheight );
        $pdf->Line( $threeCol[$col]->line2,$y0+15.8+$i*$rowheight, $threeCol[$col]->line2, $y0+15.8+($i+1)*$rowheight );
        $pdf->Text( $threeCol[$col]->marginL1, $dy+$y0+15.8+$i*$rowheight, $control['name']);
        break;
       case "makeLine":
        $pdf->Line( $threeCol[$col]->line1,$dy+$y0+15.8+$i*$rowheight, $threeCol[$col]->line1, $y0+15.8+($i+1)*$rowheight );
        $pdf->Line( $threeCol[$col]->line2,$dy+$y0+15.8+$i*$rowheight, $threeCol[$col]->line2, $y0+15.8+($i+1)*$rowheight );
        $pdf->Line( $threeCol[$col]->marginL1,$dy+$y0+15.8+$i*$rowheight, $threeCol[$col]->marginR2, $dy+$y0+15.8+$i*$rowheight );
        $pdf->Text( $threeCol[$col]->marginL1, $dy*0.8+$y0+15.8+$i*$rowheight,
          truncWord($pdf,  $control['name'],$threeCol[$col]->marginR2 - $threeCol[$col]->marginL1 ,$fontheight ) );
        break;
       default:
      }

      $pdf->SetFont('OpenSans-Regular','',$fontheight);
      $pdf->Text($threeCol[$col]->marginL1, 2*$dy+$y0+15.8+$i*$rowheight, $control['distance']);
      squeeze($pdf,$control['open'],$fontheight,$threeCol[$col]->marginR1 - $threeCol[$col]->marginL1 - 0.75 );
      $pdf->Text($threeCol[$col]->marginL1, 3*$dy+$y0+15.8+$i*$rowheight, $control['open']);
      $pdf->Text($threeCol[$col]->marginL1, 4*$dy+$y0+15.8+$i*$rowheight, $control['close']);
      $pdf->Line( $x0, $y0+15.8+$i*$rowheight,209.5,$y0+15.8+$i*$rowheight);
    } // for ($i=0; $i<$nrow && $jControl < $ncontrol; $i++)
    if( $jControl == $ncontrol ) { // finished
     do{
       while( $i < $nrows ){
          $pdf->Line( $x0, $y0+15.8+$i*$rowheight,209.5,$y0+15.8+$i*$rowheight);
          $pdf->Line( $threeCol[$col]->line1,$y0+15.8+$i*$rowheight, $threeCol[$col]->line1, $y0+15.8+($i+1)*$rowheight );
          $pdf->Line( $threeCol[$col]->line2,$y0+15.8+$i*$rowheight, $threeCol[$col]->line2, $y0+15.8+($i+1)*$rowheight );
          $i++;
       }
       $i=0;
       $col++;
     } while ($col<3);
    }
    $col++;
  } while ( $jControl < $ncontrol && $col < 3);
}

  $pages--;
}

$bname=str_replace(" ","_","Brevet_Cards_".$evname." ".$evstart.".pdf");
// return the page(s)
$pdf->Output('D',$bname);
?>
