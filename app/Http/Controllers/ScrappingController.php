<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
require 'simple_html_dom.php';
class ScrappingController extends Controller
{
    //
    public function scrapper(){
      //$file = fopen("salida.txt", "w");// abro un archivo para escribir mis respuestas
      $csv = fopen("salida2.csv", "w");
      $in = fopen("datos.txt", "r");
      $func= array(array('company'=>'','name'=>'','age'=>'','since'=>'','position'=>'','bio'=>''));
      $nameCompany="";
      //lectura
      $inputs= array();
      $names=array();
      $n_inputs=0;

      if(!$in){
        return "No se pudo abrir el archivo";
      }
      $nameCompany=fgets($in);//obtiene el nombre de la compañia
      $line = fgets($in);//obtiene el link de la compañia


      while(!feof($in)) {

        $nameCompany=fgets($in);//obtiene el nombre de la compañia
        if(feof($in)) break;
        $line = fgets($in);//obtiene el link de la compañia

        $inputs[$n_inputs] = substr($line, 0, strpos($line, "\n"));//obtiene el link de la compañia pulido
        $names[$n_inputs] = substr($nameCompany, 0, strpos($nameCompany, "\n"));

        $urlGoogle=$inputs[$n_inputs];


        //obtengo el url de la informacion de funcionarios en Reuters
        echo $names[$n_inputs]."\n";

        $urlReuters=$this->getUrlReuters($urlGoogle); //esta funcion puede devolver nulo si no regresa nada valido

        echo $urlReuters."\n\n";

        $html= file_get_html("https:".$urlReuters);

        //$html= file_get_html("http://www.reuters.com/finance/stocks/companyOfficers?symbol=ARNC.K&WTmodLOC=C4-Officers-5");

      if($html!=NULL){ //si obtiene los datos de la pagina con exito empezamos el scrappin de los datos que necesitamos
        $data= $html->find('table[class=dataTable]'); //buscamos todas las tablas
        //$data almacena el arreglo de todas las tablas
        if (!empty($data)){
        //foreach ($data as $d) {
        $cont=0;
        $n=0;
        $miembros=$data[0]->find('tr');//BUSCAMOS LAS ETIQUETAS tr DEL PRIMER ELEMENTO DEL $data
        $bios=$data[1]->find('tr');

        foreach($bios as $b){
          $biography=$b->find('td',1);
          if ($biography){
            //echo $biography->innertext."\n";
            //$bio=$biography->innertext;

            //if ($bio[0]!=' ') $func[$n]['bio']=$biography->innertext;
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


              if ($name!="") $func[$cont]['name']=$name;
              else  $func[$cont]['name']="null";

              if ($age!="") $func[$cont]['age']=$age;
              else  $func[$cont]['age']="null";

              if ($since!="") $func[$cont]['since']=$since;
              else  $func[$cont]['since']="null";

              if ($curr_pos!="") $func[$cont]['position']=$curr_pos;
              else  $func[$cont]['position']="null";


              $func[$cont]['company']=$names[$n_inputs];

              $cont++;
              //echo $name." - ".$age." - ".$since." - ".$curr_pos."\n";
              //echo strlen($name)."\n";
            }
          }
    //file_put_contents("salida.txt", print_r($urlReuters,true));

        //escribir respuestas
        for($i=0;$i<$cont;$i++){
          //fwrite($file,  $func[$i]['company']. PHP_EOL);
          //fwrite($file,  $func[$i]['name']. PHP_EOL);
          //fwrite($file,  $func[$i]['age']. PHP_EOL);
          //fwrite($file,  $func[$i]['since']. PHP_EOL);
          //fwrite($file,  $func[$i]['position']. PHP_EOL);
          //fwrite($file,  $func[$i]['bio'].PHP_EOL.PHP_EOL);
          $row = array ( $func[$i]['company'],  $func[$i]['name'],$func[$i]['age'],$func[$i]['since'],
                  $func[$i]['position'],$func[$i]['bio']);
          fputcsv($csv, $row);
        }

      }
      else echo "No se encontro data\n\n";
      }


      $n_inputs++;
    }
    //fin de lectura

      return $n_inputs;
    }

    private function getUrlReuters($url){
      //$file = fopen("salida.txt", "w");// abro un archivo para escribir mis respuestas
      $content= file_get_contents($url);
      if($content){
        preg_match_all('/<a href="(.*?)"/',$content,$matches);
        if($matches){
          //if (array_key_exists(1, $matches)) {
            //if (array_key_exists(23, $matches[1])){
              //return $matches[1][23];
            //}

          //}
          file_put_contents("salida.txt", print_r($matches,true));
          //
          $k=0;
          $encontrado=FALSE;
          foreach ($matches[1] AS $key =>$value) {
            if (stristr($value, "officersDirectors") === FALSE) {
              continue;
            } else {
              $encontrado=true;
              $k=$key;
              break;

            }
          }
          if (array_key_exists(1, $matches)) {
            if ($encontrado){
              return $matches[1][$k];
            }

          }

          file_put_contents("salida.txt", print_r($matches,true));
          //return $matches[1][$k];

        }
      }

      return NULL;




    }

}
