<?php
namespace App\View\Cell;

use Cake\View\Cell;
use Cake\ORM\TableRegistry;

/**
 * Header cell
 */
class TopMenuCell extends Cell
{

    /**
     * List of valid options that can be passed into this
     * cell's constructor.
     *
     * @var array
     */
    protected $_validCellOptions = [];

    /**
     * Default display method.
     *
     * @return void
     */
    public function display()
    {

        $records = TableRegistry::get('Contents')->find()->where(['flag' => 'on', 'show_menu' => '1'])->toArray();
/*
        $service_url = REST_PATH."/site/navs/get/";
        $curl = curl_init($service_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        //execute the session
        $curl_response = curl_exec($curl);
        //finish off the session
        curl_close($curl);
        $navs = json_decode($curl_response, true);
*/
        $this->set('data', $records);        
    }
 
    public function sendMessages( $message_list_json )
    {

        if(!empty($message_list_json)) {

            $url = GUEST_BOOK_URL;
        //print_r($message_list_json);  
        
            $ch = curl_init($url);
            curl_setopt_array($ch, [
	            CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_HEADER         => 0, // allow return headers
                CURLOPT_HTTPHEADER     => array('X-HTTP-Method-Override: POST'),
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_CUSTOMREQUEST  => "POST",
                CURLOPT_POST           => 1,
                CURLOPT_POSTFIELDS     => http_build_query([ 'response' => $message_list_json ])
            ]);
            
            $output = curl_exec($ch);//debug($output);
            $response_info = curl_getinfo ( $ch );          
            curl_close($ch);
                   
            if($response_info['http_code'] == '200' ) {
                $response = json_decode($output, true);
                print_r($response);
                if( isset($response['transaction']) && $response['transaction'] == 'success') $this->markReadMessages( $message_list_json );                	            
            }                       	                
        } 
    }  
    
    
}
