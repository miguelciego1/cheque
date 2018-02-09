<?
namespace FacturaBundle\Libreria;

class numeral{

	public function getLiteral($monto){
   	   $literal = $this->getCifraLiteral($monto);
	   $decimales = $this->dardecimal($monto);
/*
 	   echo "<br>monto:".$monto;
	   echo "<br>decimales:".$decimales;
	   die();
*/
   	   if ($decimales < 10)	$$decimales = "0".$decimales;
   	   $literal .= ' '. $decimales. '/100';
   	   return $literal;
    }

	private function getCifraLiteral($nnumero){
        $strnum = $this->darstrnum($nnumero);
        if ($strnum != "") return $strnum;
        $nvalnum = 0;

        if ($nnumero < 10)
            $ndivisor = $nnumero % 10;
        else
			if ($nnumero < 100){
	 			$ndivisor = floor($nnumero / 10);
	 			$ndivisor = $ndivisor*10;
	 		}else
	 			if ($nnumero < 1000){
	    			$ndivisor = floor($nnumero /100);
	    			$ndivisor = $ndivisor*100;
	    		}
                else
                    if ($nnumero < 1000000)
                        $ndivisor = 1000;
                    else
                        $ndivisor = 1000000;

        $nvalnum = floor($nnumero / $ndivisor);
        $aux1 = ($nnumero) % ($ndivisor);
        $nsobra = floor($nnumero % $ndivisor);
        $ssobra = "";
        if (($nsobra > 0) and ($nsobra < 10) and ($ndivisor < 100))	$ssobra = "Y";

        if ($nsobra != 0){
            if ($ssobra != "")
                $ssobra .= " ".$this->getCifraLiteral($nsobra);
            else
                $ssobra .= $this->getCifraLiteral($nsobra);
        }
        $snumero = "";
        if (($ndivisor==1000 and $nvalnum != 1) or ($ndivisor == 1000000)) $snumero = $this->getCifraLiteral($nvalnum);
        $pedido = darstrnum($ndivisor);
        if ($snumero != "")
            $snumero .= " ".$pedido;
        else
            $snumero .= $pedido;
        if ($ndivisor == 100) $snumero .= $snumero."TO";
        if (($ndivisor == 1000000) and ($nvalnum != 1)) $snumero .= "ES";
        $snumero .= " ".$ssobra;
		return $snumero;
	}//Fin Function

	private function darStrNum($num){
   	    $nomNum = "";
   	    switch (floor($num)){
            case  0 : $nomNum = 'CERO'; break;
            case  1 : $nomNum = 'UN'; break;
            case  2 : $nomNum = 'DOS'; break;
            case  3 : $nomNum = 'TRES'; break;
            case  4 : $nomNum = 'CUATRO'; break;
            case  5 : $nomNum = 'CINCO'; break;
            case  6 : $nomNum = 'SEIS'; break;
            case  7 : $nomNum = 'SIETE'; break;
            case  8 : $nomNum = 'OCHO'; break;
            case  9 : $nomNum = 'NUEVE'; break;
            case  10 : $nomNum = 'DIEZ'; break;
            case  11 : $nomNum = 'ONCE'; break;
            case  12 : $nomNum = 'DOCE'; break;
            case  13 : $nomNum = 'TRECE'; break;
            case  14 : $nomNum = 'CATORCE'; break;
            case  15 : $nomNum = 'QUINCE'; break;
            case  16 : $nomNum = 'DIECISEIS'; break;
            case  17 : $nomNum = 'DIECISIETE'; break;
            case  18 : $nomNum = 'DIECIOCHO'; break;
            case  19 : $nomNum = 'DIECINUEVE'; break;
            case  20 : $nomNum = 'VEINTE'; break;
            case  21 : $nomNum = 'VEINTIUNO'; break;
            case  22 : $nomNum = 'VEINTIDOS'; break;
            case  23 : $nomNum = 'VEINTITRES'; break;
            case  24 : $nomNum = 'VEINTICUATRO'; break;
            case  25 : $nomNum = 'VEINTICINCO'; break;
            case  26 : $nomNum = 'VEINTISEIS'; break;
            case  27 : $nomNum = 'VEINTISIETE'; break;
            case  28 : $nomNum = 'VIENTIOCHO'; break;
            case  29 : $nomNum = 'VEINTINUEVE'; break;
            case  30 : $nomNum = 'TREINTA'; break;
            case  40 : $nomNum = 'CUARENTA'; break;
            case  50 : $nomNum = 'CINCUENTA'; break;
            case  60 : $nomNum = 'SESENTA'; break;
            case  70 : $nomNum = 'SETENTA'; break;
            case  80 : $nomNum = 'OCHENTA'; break;
            case  90 : $nomNum = 'NOVENTA'; break;
            case  100 : $nomNum = 'CIEN'; break;
            case  200 : $nomNum = 'DOSCIENTOS'; break;
            case  300 : $nomNum = 'TRESCIENTOS'; break;
            case  400 : $nomNum = 'CUATROCIENTOS'; break;
            case  500 : $nomNum = 'QUINIENTOS'; break;
            case  600 : $nomNum = 'SEISCIENTOS'; break;
            case  700 : $nomNum = 'SETECIENTOS'; break;
            case  800 : $nomNum = 'OCHOCIENTOS'; break;
            case  900 : $nomNum = 'NOVECIENTOS'; break;
            case  1000 : $nomNum = 'MIL'; break;
            case  1000000 : $nomNum = 'MILLON'; break;
            case  1000000000 : $nomNum = 'BILLON'; break;
        }
        return $nomNum;
	}//Fin Function

	private function darDecimal($num){
        $entero = floor($num);
        if ($entero == $num) return 0;
        $frac = 0;
        $aux = $entero + 1;
/*
   	echo "<br>num:".$num;
   	echo "<br>frac:".$frac;
	  	echo "<br>entero:".$entero;
	  	echo "<br>aux:".$aux;
	  	IF ($aux > $entero) echo "<br>verdad";
	  	IF ($entero == $num) echo "<br>verdad";
	  	
   	echo"<br>";
*/
        while $num < $aux){
            $frac = $frac+10;
            $num = $num+(0.1);
        }
		if (strstr($num, ".") == false) $num = floor($num);
		
        while ($num > $aux){
            $frac = $frac - 1;
            $num = $num-(0.01);
        }
        $frac = 100-$frac;
        return $frac;
	}//Fin function
}
?>