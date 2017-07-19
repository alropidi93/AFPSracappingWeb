<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
require 'simple_html_dom.php';
class ScrappingController extends Controller
{
    //
    public function scrapper(){
      //$file = fopen("salida.txt", "w");// abro un archivo para escribir mis respuestas

      //$url= "https://www.google.com/finance?q=Activision+Blizzard%2C+Inc.&ei=MKpuWZjODcy6es_5ppgE";

      //capturo la url de la empresa a scrappear en google finance
      $urlGoogle= "https://www.google.com/finance?q=Arconic+Inc.";

      //obtengo el url de la informacion de funcionarios en Reuters
      $urlReuters=$this->getUrlReuters($urlGoogle); //esta funcion puede devolver nulo si no regresa nada valido


      
      //$html= file_get_html($urlReuters);
      $html= file_get_html("http://www.reuters.com/finance/stocks/companyOfficers?symbol=ARNC.K&WTmodLOC=C4-Officers-5");
      if($html){
        $data= $html->find('table[class=dataTable]');
      }


      //foreach ($data as $d) {
      $cont=0;
      $miembros=$data[0]->find('tr');//BUSCAMOS LAS ETIQUETAS tr DEL PRIMER ELEMENTO DEL $data
      foreach($miembros as $m){


        $cont=$cont+1;
        $link=$m->find('h2 a',0);
        echo $link. "\n";;

      }

      return $cont;
      //}

      //return $cont;
      //file_put_contents("salida.txt", print_r($urlReuters,true));


      //return $urlReuters;

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
