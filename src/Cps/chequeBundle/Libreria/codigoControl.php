<?php
namespace FacturaBundle\Libreria;

class codigoControl{
   
   private $codigo;
   private $digVer;
   private $datosF;
   private $llave;
    
   public function generar($nroAut, $nroFac, $idCli, $fch, $monto, $llave){
      $this->datosF[] = $nroAut;
      $this->datosF[] = $nroFac;
      $this->datosF[] = $idCli;
      $this->datosF[] = $fch;
      $this->datosF[] = round($monto);
      $this->llave = $llave;

      $this->paso1();
      $this->paso2();
      $this->paso3();
      $this->paso4();
      $this->paso5();
      $this->paso6();

      return $this->codigo;   
   }
   
   private function paso1(){
      $sum = 0;
      for ($i=2; $i<=5; $i++){
         for ($j=1; $j<=2; $j++)
            $this->datosF[$i-1] .= $this->obtenerVerhoeff($this->datosF[$i-1]);
         $sum = $sum + $this->datosF[$i-1];
      }
      for ($i=1; $i<=5; $i++){
         $dig = $this->obtenerVerhoeff($sum);
         $sum .= $dig;
         $this->digVer .= $dig;
      }
   }
   
   private function paso2(){
      $ind1 = 0; $ind2 = 0;
      for ($i=1; $i<=5; $i++){
         $ind1 = $ind1+$ind2; $ind2 = $this->digVer[$i-1]+1;
         $this->datosF[$i-1] .= substr($this->llave, $ind1, $ind2);
      }   
   }
   
   private function paso3(){
      $cadCon = "";
      for ($i=1; $i<=5; $i++)
         $cadCon .= $this->datosF[$i-1];
      
      $llaCif = $this->llave.$this->digVer;
      $this->cadAll = $this->allegedRC4($cadCon, $llaCif);
   }
   
   private function paso4(){
      $this->sp = array(0,0,0,0,0);
      $len = strlen($this->cadAll)-1;
      $this->st = $this->sumatoriaTotal();
      for($i=0;$i<=4;$i++){
         for($pos=$i;$pos<=$len;$pos=$pos+5){
            $this->sp[$i] = $this->sp[$i] + ord($this->cadAll[$pos]);
         }
      }
   }
   
   private function paso5(){
      $total = 0;
      for($i=0;$i<=4;$i++){
         $this->sp[$i] = floor(($this->st * $this->sp[$i]) / ($this->digVer[$i]+1));
         $total = $total + $this->sp[$i];
      }
      $sum = $total;
      $this->b64 = $this->base64($total);
   }
   
   private function paso6(){
      $cod = $this->allegedRC4($this->b64, $this->llave.$this->digVer);
      $len = strlen($cod);
      $this->codigo = "";
      for($i=0;$i<=$len-1;$i=$i+2){
         if ($i != 0) $this->codigo .= "-";
         $this->codigo .= $cod[$i].$cod[$i+1];
      }
   }
   
   private function base64($numero){
      $diccionario = array('0','1','2','3','4','5','6','7','8','9',
                           'A','B','C','D','E','F','G','H','I','J',
                           'K','L','M','N','O','P','Q','R','S','T',
                           'U','V','W','X','Y','Z','a','b','c','d',
                           'e','f','g','h','i','j','k','l','m','n',
                           'o','p','q','r','s','t','u','v','w','x',
                           'y','z','+','/'
                          );
      $cociente = 1;
      $palabra = "";
      while ($cociente > 0){
         $cociente = floor($numero / 64);
         $resto = $numero % 64;
         $palabra = $diccionario[$resto].$palabra;
         $numero = $cociente;
      }
      return $palabra;
   }
   
   private function sumatoriaTotal(){
      $resp = 0;
      for($i=0;$i<=(strlen($this->cadAll)-1);$i++)
         $resp = $resp + ord($this->cadAll[$i]);
      return $resp;
   }
   
   private function allegedRC4($mensaje, $key){
      $state = array();
      $x = 0;
      $y = 0;
      $index1 = 0;
      $index2 = 0;
      $NMen = 0;
      $MensajeCifrado = "";
      for($i=0;$i<=255;$i++) $state[$i] = $i;

      for($i=0;$i<=255;$i++){
         $index2 = (ord($key[$index1])+$state[$i]+$index2) % 256;
         $this->intercambio($state[$i], $state[$index2]);
         $index1 = ($index1 + 1) % strlen($key);
      }
      for($i=0;$i<=(strlen($mensaje)-1);$i++){
         $x = ($x+1) % 256;
         $y = ($state[$x] + $y) % 256;
         $this->intercambio($state[$x], $state[$y]);
         $NMen = ord($mensaje[$i]) ^ $state[($state[$x]+$state[$y]) % 256];
         $MensajeCifrado = $MensajeCifrado.$this->rellenaCero(dechex($NMen));
      }
      return strtoupper($MensajeCifrado);
   }
   
   private function intercambio(&$a, &$b){
        $aux = $a;
        $a = $b;
        $b = $aux;
   }
   
   private function rellenaCero($var){
        if (strlen($var) == 1)   $var = '0'.$var;
        return $var;
   }
   
   private function obtenerVerhoeff($cifra){
      $mul = array(
                  array(0,1,2,3,4,5,6,7,8,9),
                  array(1,2,3,4,0,6,7,8,9,5),
                  array(2,3,4,0,1,7,8,9,5,6),
                  array(3,4,0,1,2,8,9,5,6,7),
                  array(4,0,1,2,3,9,5,6,7,8),
                  array(5,9,8,7,6,0,4,3,2,1),
                  array(6,5,9,8,7,1,0,4,3,2),
                  array(7,6,5,9,8,2,1,0,4,3),
                  array(8,7,6,5,9,3,2,1,0,4),
                  array(9,8,7,6,5,4,3,2,1,0),
                  );            
      $per = array(
                  array(0,1,2,3,4,5,6,7,8,9),
                  array(1,5,7,6,2,8,3,0,9,4),
                  array(5,8,0,3,7,9,6,1,4,2),
                  array(8,9,1,6,0,4,3,5,2,7),
                  array(9,4,5,3,1,2,6,8,7,0),
                  array(4,2,8,6,5,7,3,9,0,1),
                  array(2,7,9,3,8,0,6,4,1,5),
                  array(7,0,4,6,9,1,3,2,5,8),
                  );
      $inv = array(0,4,3,2,1,5,6,7,8,9);
      $check = 0;
      
      $numeroInvertido = strrev($cifra);
      $len = strlen($cifra);
      for($i = 0; $i < $len; $i++)
         $check = $mul[$check][$per[($i+1) % 8][$numeroInvertido[$i]]]; 
      return $inv[$check];
   }

}
