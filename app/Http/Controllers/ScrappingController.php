<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
require 'simple_html_dom.php';
class ScrappingController extends Controller
{
    //
    public function scrapper(){
      $file = fopen("salida.txt", "w");// abro un archivo para escribir mis respuestas
      $func= array(array('name'=>'','age'=>'','since'=>'','position'=>'','bio'=>''));
      //$url= "https://www.google.com/finance?q=Activision+Blizzard%2C+Inc.&ei=MKpuWZjODcy6es_5ppgE";

      //capturo la url de la empresa a scrappear en google finance
      $urlGoogle= "https://www.google.com/finance?q=Arconic+Inc.";

      //obtengo el url de la informacion de funcionarios en Reuters
      $urlReuters=$this->getUrlReuters($urlGoogle); //esta funcion puede devolver nulo si no regresa nada valido

      $html= file_get_html("https:".$urlReuters);
      
      //$html= file_get_html("http://www.reuters.com/finance/stocks/companyOfficers?symbol=ARNC.K&WTmodLOC=C4-Officers-5");

      if($html){ //si obtiene los datos de la pagina con exito empezamos el scrappin de los datos que necesitamos
        $data= $html->find('table[class=dataTable]'); //buscamos todas las tablas

      }
      //$data almacena el arreglo de todas las tablas

      //foreach ($data as $d) {
      $cont=0;
      $n=0;
      $miembros=$data[0]->find('tr');//BUSCAMOS LAS ETIQUETAS tr DEL PRIMER ELEMENTO DEL $data
      $bios=$data[1]->find('tr');

      foreach($bios as $b){
        $biography=$b->find('td',1);
        if ($biography){
          //echo $biography->innertext."\n";

          $func[$n]['bio']=$biography->innertext;
          $n++;
        }
      }

      foreach($miembros as $m){ //miembros contiene todas las etiquetas tr de la primera tabla



        $link=$m->find('h2 a',0);
        $otherData=$m->find('td');// extraigo todas las etiquetas td en donde se encuentran los otros datos del funcionarios

        if($otherData && $link){
            $name=$link->innertext;
            $age= $otherData[1]->innertext;
            $since=$otherData[2]->innertext;
            $curr_pos=$otherData[3]->innertext;

            $func[$cont]['name']=$name;
            $func[$cont]['age']=$age;
            $func[$cont]['since']=$since;
            $func[$cont]['position']=$curr_pos;
            $cont++;
            //echo $name." - ".$age." - ".$since." - ".$curr_pos."\n";
            //echo strlen($name)."\n";
          }
        }
        //echo $cont."-".$n."\n";

//file_put_contents("salida.txt", print_r($urlReuters,true));

      //escribir respuestas
      for($i=0;$i<$cont;$i++){
        fwrite($file,  $func[$i]['name']. PHP_EOL);
        fwrite($file,  $func[$i]['age']. PHP_EOL);
        fwrite($file,  $func[$i]['since']. PHP_EOL);
        fwrite($file,  $func[$i]['position']. PHP_EOL);
        fwrite($file,  $func[$i]['bio'].PHP_EOL.PHP_EOL);
      }


    }

    private function getUrlReuters($url){

      $content= file_get_contents($url);
      if($content){
        preg_match_all('/<a href="(.*?)"/',$content,$matches);
        if($matches){
          return $matches[1][23];
        }
      }

      return NULL;




    }

}
