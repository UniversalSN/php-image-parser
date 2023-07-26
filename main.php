<?php

use DiDom\Document;

require __DIR__ . '/vendor/autoload.php';

$result = '<div class="container-imgs" style="display: flex; flex-direction:column; margin-left: auto; margin-right: auto; margin-top: 100px;">';
$counter = 0;
$sum_size = 0;

if(isset($_POST['parse-url'])) {
    if(str_starts_with($_POST['parse-url'], "https://") or str_starts_with($_POST['parse-url'], "http://")){

        $url = $_POST['parse-url'];

        $document = new Document($url, true);

        $imgs = $document->find('img');     

        foreach($imgs as $key=>$img){
            if(!(str_starts_with($img->src, "https://")) and !(str_starts_with($img->src, "http://"))){
                unset($imgs[$key]);
            }
        }

        $imgs = array_values($imgs);
            
        for($i=0;$i<ceil(count($imgs)/4.0);$i++){
            $result .= '<div class="img-row" style="display:flex; flex-direction:row; justify-content:center">';

            for($j=0;$j<4;$j++){
                if(($i * 4 + $j) >= count($imgs)) continue;

                if(str_starts_with($imgs[$i * 4 + $j]->src, "https://") or str_starts_with($imgs[$i * 4 + $j]->src, "http://")){
                    $headers = get_headers($imgs[$i * 4 + $j]->src);
    
                    if($headers != false){

                        $result .= '<div class="img-ceil" style="display:flex; height:20%; width:20%; padding: 5px; border: black solid 1px;">';
                        $result .= '<img src="' . $imgs[$i * 4 + $j]->src . '" style="height:100%; width:100%;">';
                        $result .= '</div>';
    
                        foreach($headers as $header){
                            if(str_contains($header, 'Content-Length')){
                                $sum_size += (int)explode('Content-Length: ', $header)[1];
                            }
                        }
                    }
                }
            }

            $result .= '</div>';
        }
    }

    $result .= "</div>";

    echo'<pre style="margin-left: 45%;"> Size of all images: ' . (float)($sum_size / (1024 * 1024)) . ' Mb </pre>' . $result;
}