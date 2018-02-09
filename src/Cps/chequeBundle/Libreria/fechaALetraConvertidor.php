<?php
/**
* Clase que implementa un coversor de números
* a letras.
*
* Soporte para PHP >= 5.4
* Para soportar PHP 5.3, declare los arreglos
* con la función array.
*
* @author AxiaCore S.A.S
*
*/

namespace Cps\chequeBundle\Libreria;

class fechaALetraConvertidor{

    function obtenerFechaEnLetra($fecha){

        $num = date("j", strtotime($fecha));
    
        $anno = date("Y", strtotime($fecha));
    
        $mes = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
    
        $mes = $mes[(date('m', strtotime($fecha))*1)-1];
    
        return $num.' de '.$mes.' del '.$anno;
    
    }
    function obtenermes($fecha){

        $num = date("j", strtotime($fecha));
    
        $anno = date("Y", strtotime($fecha));
    
        $mes = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
    
        $mes = $mes[(date('m', strtotime($fecha))*1)-1];
    
        return $mes.' '.$anno;
    
    }
    function mesliteral($fecha){

        $num = date("j", strtotime($fecha));
    
        $anno = date("Y", strtotime($fecha));
    
        $mes = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
    
        $mes = $mes[(date('m', strtotime($fecha))*1)-1];
    
        return $mes;
    
    }
}
