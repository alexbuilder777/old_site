<?php
namespace App\Traits;	
# Загрузка методов класса по URL-у
trait CurlTrait
{

    function curlGet($options = []) 
    {
        $ua = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13';
        
        $ch = curl_init($options['url']);
        curl_setopt_array($ch, [
	        CURLOPT_URL            => $options['url'],
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER         => 0, // allow return headers
            CURLOPT_HTTPHEADER     => array('X-HTTP-Method-Override: POST'),
            CURLOPT_HTTPHEADER     => array('X-CSRF-Token: askjdhfas7d8fyas7udfhj'),
            
           // CURLOPT_USERAGENT      => $ua,
            CURLOPT_COOKIESESSION  => false,
            CURLOPT_COOKIE         => 'srfToken=hd7ASD87asy7duyiajknSD; colour=red',
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POST           => true
        ]);
       
        if(isset($options['data'])) curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($options['data']));
      
        $output = curl_exec($ch);//debug($output);
        $response_info = curl_getinfo ( $ch );          
        curl_close($ch);
        // debug($response_info);
        $response = [];      
        if($response_info['http_code'] == '200' ) {
            $response = json_decode($output, true);               	            
        } 
        return $response;
    }    
    
}
?>