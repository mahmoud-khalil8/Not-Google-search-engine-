<?php 

function fetchHtml($url){
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $html = curl_exec($ch);
    curl_close($ch);
    return $html;
}


$url='https://www.google.com/search?q=cats';

$htmlContent=fetchHtml($url);

$dom=new DOMDocument(); 

// Suppress errors due to malformed HTML
libxml_use_internal_errors(true);
$dom->loadHTML($htmlContent);

libxml_clear_errors();

$links = $dom->getElementsByTagName('a');

function makeLinksValid($src,$url){

    $scheme=parse_url($url)['scheme'];
    $host=parse_url($url)['host']; 

    if(substr($src,0,2)=='//'){
        $src=$scheme.':'.$src ;
    }else if (substr($src,0,1)=='/'){
        $src=$scheme.'://'.$host .$src;
    }
    return $src ;
}

foreach ($links as $link) {
    
if ($link instanceof DOMElement) {
        $href = $link->getAttribute('href');
        
        if (strpos($href, '#') !== false || substr($href, 0, 11) == "javascript") {
            continue;
        }
        $href=makeLinksValid($href,$url);
        echo 'Link: ' . $href . '<br>';
        
        // echo 'Text: ' . $link->nodeValue . '<br><br>';
    }
}

// $images = $dom->getElementsByTagName('img');
// foreach ($images as $image) {
//     echo 'Image URL: ' . $image->getAttribute('src') . '<br>';
//     echo 'Alt Text: ' . $image->getAttribute('alt') . '<br><br>';
// }


?>