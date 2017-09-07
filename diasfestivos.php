<?php

 

//1.- Pasar la fecha inicial y final a maketime y obtener un arreglo con todas los días intermedios. 

 

function DiasHabiles($fecha_inicial,$fecha_final)

{

list($dia,$mes,$year) = explode("-",$fecha_inicial);

$ini = mktime(0, 0, 0, $mes , $dia, $year);

list($diaf,$mesf,$yearf) = explode("-",$fecha_final);

$fin = mktime(0, 0, 0, $mesf , $diaf, $yearf);

 

$r = 1;

while($ini != $fin)

{

$ini = mktime(0, 0, 0, $mes , $dia+$r, $year);

$newArray[] .=$ini;

$r++;

}

return $newArray;

}

 

 

 

//2.- Una función que evalué el arreglo de fechas obtenido, que contenga los feriados nacionales que correspondan (restando) y que reste los sábados y domingos. 

 

function Evalua($arreglo)

{

$feriados        = array(

'1-1',  //  Año Nuevo (irrenunciable)  

'10-4',  //  Viernes Santo (feriado religioso)  

'11-4',  //  Sábado Santo (feriado religioso)  

'1-5',  //  Día Nacional del Trabajo (irrenunciable)  

'21-5',  //  Día de las Glorias Navales  

'29-6',  //  San Pedro y San Pablo (feriado religioso)  

'16-7',  //  Virgen del Carmen (feriado religioso)  

'15-8',  //  Asunción de la Virgen (feriado religioso)  

'18-9',  //  Día de la Independencia (irrenunciable)  

'19-9',  //  Día de las Glorias del Ejército  

'12-10',  //  Aniversario del Descubrimiento de América  

'31-10',  //  Día Nacional de las Iglesias Evangélicas y Protestantes (feriado religioso)  

'1-11',  //  Día de Todos los Santos (feriado religioso)  

'8-12',  //  Inmaculada Concepción de la Virgen (feriado religioso)  

'13-12',  //  elecciones presidencial y parlamentarias (puede que se traslade al domingo 13)  

'25-12',  //  Natividad del Señor (feriado religioso) (irrenunciable)  

);

 

$j= count($arreglo);

 

for($i=0;$i<=$j;$i++)

{

$dia = $arreglo["$i"];

 

        $fecha = getdate($dia);

            $feriado = $fecha['mday']."-".$fecha['mon'];

                    if($fecha["wday"]==0 or $fecha["wday"]==6)

                    {

                        $dia_ ++;

                    }

                        elseif(in_array($feriado,$feriados))

                        {

                            $dia_++;

                        }

}

$rlt = $j - $dia_;

return $rlt;

}

 

 

 

//3.- Se llama a las funciones. 

$CantidadDiasHabiles = Evalua(DiasHabiles('19-10-2010','28-12-2010'));

 

echo   $CantidadDiasHabiles;

 

?>