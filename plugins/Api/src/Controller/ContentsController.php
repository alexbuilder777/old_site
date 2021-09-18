<?php
namespace Api\Controller;

use Api\Controller\AppController;
use App\Traits\CurlTrait;
use App\Traits\ApiTrait;
use Cake\ORM\TableRegistry;

class ContentsController extends AppController
{
    use CurlTrait;

    public function initialize()
    {
        parent::initialize();
    }
    
    public function beforeRender(\Cake\Event\Event $event){
	    
	    $this->viewBuilder()->setLayout(false);	    
    }

    public function markDeleted($id = null)
    {
	    // Распаковываем пришедшие через пост данные
	    parse_str($this->request->getData('data'), $data);
	    
	    if(isset($data['ids'])) {
		    
		    $data['ids'] = unserialize($data['ids']);	    
		    $records = [];
		    foreach($data['ids'] as $id) {

                if($record = $this->Contents->get($id)) {
	                $record['flag'] = 'delete';
	                $this->Contents->save($record);
	                $records[] = $record;	                
                }
                
		    }
		    $this->__outputJSON(['News' => $records]);
	    } else if($id) {
	    
            if($record = $this->Contents->get($id)) {
	            $record['flag'] = 'delete';
	            $this->Contents->save($record);
	            $this->__outputJSON(['News' => $record]);
            }
        }
        
        $this->render(false);

    }


    public function index(){}

    /*
	 * Getting API datas from Remote Server with Auth and saving record (05.01.2019)
	 *
	 * return new Entity   
     */



    // Set response as JSON
    public function __outputJSON($data, $error = NULL) {
	    header('Content-Type: application/json; charset=UTF-8');

	    $this->render(false);
	    $this->viewBuilder()->setLayout(false);
    
	    if(!$error) { 
		    $response['response'] = $data;
            die(json_encode( $response, JSON_NUMERIC_CHECK ));
        } else { 
	        $response['error'] = $error;
            die(json_encode( $response, JSON_NUMERIC_CHECK ));	        
        }
    }



    
}