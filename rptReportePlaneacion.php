<?php
session_start();
// Include the main TCPDF library (search for installation path).
require_once('./tcpdf/examples/tcpdf_include.php');

$p1 = $_GET['param1']; //Cuenta
$p2 = $_GET['param2']; //idAuditoria


function conecta(){
  try{
    require 'src/conexion.php'; 
    $db = new PDO("sqlsrv:Server={$hostname}; Database={$database}", $username, $password );
    return $db;
  }catch (PDOException $e) {
    print "ERROR: " . $e->getMessage();
    die();
  }
}

function consultaRetorno($sql,$db){
    $query=$db->prepare($sql);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

$sql = "SELECT a.idAuditoria, COALESCE(a.clave, convert(varchar,a.idAuditoria)) claveAuditoria,  ar.nombre sArea, isnull(aresp.nombre,'') sResponsable,isnull(asresp.nombre,'') sSubresponsable, " .
      "isnull(dbo.lstSujetosByAuditoria(a.idAuditoria),'') sSujeto, dbo.lstObjetosByAuditoria(a.idAuditoria) sObjeto, ta.nombre sTipo, a.objetivo sObjetivo, a.alcance sAlcance,CONCAT(em.nombre,' ',em.paterno,' ', em.materno) nombre, CONCAT(epl.nombre,' ' ,epl.paterno, ' ' ,epl.materno) vobo,CAST(DAY(GETDATE()) AS VARCHAR(2)) + ' DE ' + UPPER(DATENAME(MM, GETDATE())) + ' DE ' + CAST(YEAR(GETDATE()) AS VARCHAR(4)) AS fecha " . 
      "FROM sia_programas p INNER JOIN sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma=a.idPrograma  " . 
      "LEFT JOIN sia_areas ar on a.idArea=ar.idArea " .
      "LEFT JOIN sia_areasresponsables aresp on ar.idArea=aresp.idArea and a.idResponsable = aresp.idResponsable ".
      "LEFT JOIN sia_areassubresponsables asresp on aresp.idArea=asresp.idArea  and aresp.idResponsable = asresp.idResponsable   and a.idSubresponsable = asresp.idSubresponsable " .
      "LEFT JOIN sia_unidades u on a.idCuenta = u.idCuenta and a.idSector=u.idSector and a.idSubsector = u.idSubsector and a.idUnidad=u.idUnidad  " .
      "LEFT  JOIN sia_objetos o on a.idObjeto=o.idObjeto and a.idCuenta=o.idCuenta and a.idPrograma=o.idPrograma and a.idAuditoria = o.idAuditoria  " .
      "LEFT JOIN sia_tiposAuditoria ta on a.tipoAuditoria=ta.idTipoAuditoria " .
      "LEFT JOIN sia_auditoriasauditores aa on a.idAuditoria = aa.idAuditoria and a.idCuenta = aa.idCuenta and a.idPrograma = aa.idPrograma " .
      "LEFT JOIN sia_empleados em on aa.idAuditor=em.idEmpleado " .
      "LEFT JOIN sia_empleados epl on ar.idEmpleadoTitular=epl.idEmpleado " .
      "WHERE a.idCuenta='$p1' AND a.idAuditoria='$p2' and aa.lider='SI' ORDER BY a.idAuditoria;";      
      
$db=conecta();
$datos=consultaRetorno($sql, $db);

function convierte($cadena){
  $str = utf8_decode($cadena);
  return $str;
}


$auditoria=convierte(str_replace('/',"\n", $datos[0]['idAuditoria']));
$clave=convierte(str_replace('/',"\n", $datos[0]['claveAuditoria']));
$sArea=convierte(str_replace('/',"\n", $datos[0]['sArea']));
$sResponsable=$datos[0]['sResponsable'];
$sSubresponsable=convierte(str_replace('/',"\n", $datos[0]['sSubresponsable']));

$sSujeto=$datos[0]['sSujeto'];
$sObjeto=$datos[0]['sObjeto'];
$sAlcance=$datos[0]['sAlcance'];
$sTipo=convierte(str_replace('/',"\n", $datos[0]['sTipo']));
$sObjetivo=convierte(str_replace('/',"\n", $datos[0]['sObjetivo']));
$nombre=convierte(str_replace('/',"\n", $datos[0]['nombre']));
$vobo=$datos[0]['vobo'];

$fecha=$datos[0]['fecha'];

class MYPDF extends TCPDF {
      // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 10);
        // Page number
        //$this->Cell(20);
        $this->Cell(186, 3,' | '.$this->getAliasNumPage().' | '. ' de '.' | '.$this->getAliasNbPages().' | ',1,1,'R');
    }
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Auditoria Superior de la Ciudad de México');
$pdf->SetTitle('Reporte de Planeación ' .$clave);
 
 $pdf->setPrintHeader(false);
//$pdf->setPrintFooter(false);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// -------------------------------------------------------------------

// set font
$pdf->SetFont('helvetica', '', 20);

// add a page
$pdf->AddPage();

$text1 = '
<table cellspacing="0" cellpadding="0" border="0">
    
    <tr>
        <td><img img src="img/logo-top.png"/></td>
        <td colspan="3"><p><font size="10"><b> AUDITORÍA SUPERIOR DE LA CIUDAD DE MÉXICO<br> PROGRAMA GENERAL DE AUDITORÍA DE LA CUENTA PÚBLICA '. $p1.'<br> REPORTE DE PLANEACIÓN</b></p></font></td>
    </tr>
</table>';

$pdf->SetFontSize(9);
$pdf->writeHTML($text1);

//$pdf->SetFont('helvetica', '', 8);

// -------------------------------------------------------------------

$tbl = <<<EOD
<table cellspacing="0" cellpadding="0" border="0">
    
    <tr>
        <td colspan="3" align="right">Fecha: </td>
        <td align="center">{$fecha}</td>
        
    </tr>
</table>
EOD;

$pdf->writeHTML($tbl, true, false, false, false, '');



// -------------------------------------------------------------------

$tbl = <<<EOD
<table cellspacing="0" cellpadding="1" border="1">
    
    <tr>
        <td align="center"><b>DIRECCIÒN GENERAL:</b></td>
        <td align="center"><font size="7">{$sArea}</font></td>
        <td align="center"><b>Clave de la Auditoría:</b> </td>
        <td align="center"><font size="7">{$clave}</font></td>
    </tr>
    <tr>
        <td align="center"><b>DIRECCIÓN ÁREA: </b></td>
        <td align="center"><font size="7">{$sResponsable}</font></td>
        <td align="center"><b>RUBRO O FUNCIÓN DE GASTO: </b></td>
        <td align="center"><font size="7">{$sObjeto}</font></td>
    </tr>
    <tr>
        <td align="center"><b>SUJETO FISCALIZADO: </b></td>
        <td align="center"><font size="7">{$sSujeto}</font></td>
        <td align="center"><b>TIPO DE AUDITORÍA: </b></td>
        <td align="center"><font size="7">{$sTipo}</font></td>
    </tr>
</table>
EOD;

$pdf->writeHTML($tbl, true, false, false, false, '');

// -----------------------------------------------------------------------------

$tbl = <<<EOD
<table cellspacing="0" cellpadding="1" border="1">
    
    <tr>
        <td colspan="2"><b>OBJETIVO DE LA AUDITORIA: </b></td>
        <td colspan="4">{$sObjetivo}</td>
    </tr>
    <tr>
        <td colspan="2"><b>ALCANCE DE LA AUDITORIA: </b></td>
        <td colspan="4">{$sAlcance}</td>
    </tr>
    
</table>
EOD;

$pdf->writeHTML($tbl, true, false, false, false, '');

// -----------------------------------------------------------------------------
$sql="SELECT f.nombre sFase, orden, min(aa.fInicio ) desde, max(aa.fFin) hasta, sum(convert(int,aa.diasEfectivos)) cantidad " .
        "FROM sia_fases f " . 
        "LEFT JOIN sia_auditoriasactividades aa on f.idFase = aa.idFase and  aa.idAuditoria='$p2' Group by f.nombre, orden order by f.orden;";

$db=conecta();
$datos=consultaRetorno($sql, $db);

$tbl = <<<EOD
  <table cellspacing="0" cellpadding="1" border="1">
    <tr style="background-color:#E7E6E6;">
      <th rowspan="2" align="center"><b>FASE</b></th>
      <th colspan="2" align="center"><b>FECHA</b></th>
      <th rowspan="2" align="center"><b>DÍAS HÁBILES</b></th>
    </tr>
    <tr style="background-color:#E7E6E6;">
      <th width='20%' align="center"><b>INICIO</b></th>
      <th width='20%' align="center"><b>TÉRMINO</b></th>
    </tr>
EOD;

foreach ($datos as $row) {
$tbl .= <<<EOD
  <tr>
    <td>{$row['sFase']}</td>
    <td>{$row['desde']}</td>
    <td>{$row['hasta']}</td>
    <td align="center">{$row['cantidad']}</td>
  </tr>
EOD;
}

$tbl .= <<<EOD
  </table>
EOD;


$pdf->writeHTML($tbl, true, false, false, false, '');

// -----------------------------------------------------------------------------

$sql="SELECT CONCAT(ap.orden,'.-  ',ap.nombre) nombre, aua.actividad  actividad " .
        "FROM sia_programas p " .
        "INNER JOIN sia_auditorias a on p.idCuenta=a.idCuenta and p.idPrograma=a.idPrograma " .
        "LEFT JOIN sia_auditoriasauditores aa on a.idAuditoria = aa.idAuditoria and a.idCuenta = aa.idCuenta and a.idPrograma = aa.idPrograma " .
        "LEFT JOIN sia_AuditoriasApartados aua on a.idAuditoria=aua.idAuditoria " .
        "LEFT JOIN sia_apartados ap on aua.idApartado=ap.idApartado " .
        "WHERE a.idCuenta='$p1' AND a.idAuditoria='$p2' and aa.lider='SI' ORDER BY ap.orden;";

$db=conecta();
$dat=consultaRetorno($sql, $db);

$tbl = <<<EOD
<table cellspacing="0" cellpadding="1" border="1">
EOD;

foreach ($dat as $row2) {
$tbl .= <<<EOD
  <tr>
    <th>
    <p align='left'><b>{$row2['nombre']}</b></p>
    <p style='text-align: justify;'>{$row2['actividad']}</p></th>
    
  </tr>
EOD;
}

$tbl .= <<<EOD
</table>
EOD;

$pdf->writeHTML($tbl, true, false, false, false, '');

// -----------------------------------------------------------------------------
/*$sql="SELECT f.nombre sFase, orden, min(aa.fInicio ) desde, max(aa.fFin) hasta, sum(convert(int,aa.diasEfectivos)) cantidad " .
        "FROM sia_fases f " . 
        "LEFT JOIN sia_auditoriasactividades aa on f.idFase = aa.idFase and  aa.idAuditoria=:auditoria Group by f.nombre, orden order by f.orden";
$dbQuery = $db->prepare($sql);    
        $dbQuery->execute(array(':auditoria' => $p2));
        $rs2 = $dbQuery->fetchAll(PDO::FETCH_ASSOC);*/
// NON-BREAKING TABLE (nobr="true")

$tbl = <<<EOD
<table cellspacing="0" cellpadding="1" border="1">
 
 <tr>
  <td align="center"><b>ELABORÓ</b></td>
  <td align="center"><b>VISTO BUENO</b></td>
 </tr>
 <tr>
  <td align="center"><b><br><br><br><br>{$nombre}</b></td>
  <td align="center"><b><br><br><br><br>{$vobo}</b></td>
 </tr>
  <tr>
  <td align="center"><b>DIRECTOR DE ÁREA</b></td>
  <td align="center"><b>DIRECTOR GENERAL</b></td>
 </tr>

</table>
EOD;

$pdf->writeHTML($tbl, true, false, false, false, '');
// -----------------------------------------------------------------------------

//Close and output PDF document
$pdf->Output('Reporte de planeacion', 'I');

//============================================================+
// END OF FILE
//============================================================+