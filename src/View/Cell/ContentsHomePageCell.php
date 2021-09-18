<?php
namespace App\View\Cell;

use Cake\View\Cell;
use Cake\ORM\TableRegistry;

/**
 * Header cell
 */
class ContentsHomePageCell extends Cell
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
    public function display($id = null)
    {
        $records = TableRegistry::get('Contents')->find()->toArray();
/*
        $service_url = REST_PATH."/site/news/api/".$id;
        $curl = curl_init($service_url);
        curl_setopt($curl, CURLOPT_HEADER, 0); // allow return headers
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-HTTP-Method-Override: GET') );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET"); // set rest method
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        //execute the session
        $curl_response = curl_exec($curl); 
        //finish off the session
        curl_close($curl); 

        $json_array = json_decode($curl_response, true);
*/
        $this->set('data', $records);       
    }
  
    
    
}
