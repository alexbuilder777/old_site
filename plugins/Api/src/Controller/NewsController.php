<?php
namespace Api\Controller;

use Api\Controller\AppController;
use App\Traits\CurlTrait;
use App\Traits\ApiTrait;
use Cake\ORM\TableRegistry;

class NewsController extends AppController
{
    use CurlTrait;

    public function initialize()
    {
        parent::initialize();
    }
    
    public function beforeRender(\Cake\Event\Event $event){
	    
	    $this->viewBuilder()->setLayout(false);	    
    }

    public function index(){}


    public function list($last_id = 0)
    {	    
	    $modelName = $this->name;
	    $this->__outputJSON( $this->$modelName->find()->where(['id >' => $last_id, 'flag' => 'on'])->limit(50)->order([ 'date' => 'DESC' ])->toArray() );

	    $this->render(false);
    } 

/*
    public function markDeleted($id)
    {
        if($record = $this->News->get($id)) {
	        $record['flag'] = 'delete';
	        $this->News->save($record);
	        $this->__outputJSON(['News' => $record]);
        }

    }
*/

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