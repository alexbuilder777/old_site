<?php
namespace Api\Controller;

use Cake\ORM\TableRegistry;
use Api\Controller\AppController;
use App\Traits\CurlTrait;
use App\Traits\ApiTrait;

class CategoryOptionsController extends AppController
{
    use CurlTrait;
    use ApiTrait;

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

                if($record = $this->CategoryOptions->get($id)) {
	                $record['flag'] = 'delete';
	                $this->CategoryOptions->save($record);
	                $records[] = $record;	                
                }
                
		    }
		    $this->__outputJSON(['News' => $records]);
	    } else if($id) {
	    
            if($record = $this->CategoryOptions->get($id)) {
	            $record['flag'] = 'delete';
	            $this->CategoryOptions->save($record);
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
/*
    public function add()
    { 
	    // Распаковываем пришедшие через пост данные
	    parse_str($this->request->getData('data'), $data);
	    
	    $CategoryOptionsObj = TableRegistry::get('Api.CategoryOptions');
	   
	    if($data['crud'] == 'create'){ 
		    
		    $this->apiAuth();
		    $entity = $CategoryOptionsObj->newEntity($data);
		    $CategoryOptionsObj->save($entity);
		    
		} else if($data['crud'] == 'update') {
			
			$this->apiAuth();
			$entity = $CategoryOptionsObj->get($data['id']);
			$entity = $CategoryOptionsObj->patchEntity($entity, $data);
			$CategoryOptionsObj->save($entity);
			
		} else {
			
			$entity = $CategoryOptionsObj->find()->where(['id' => $data['id']])->first()->toArray();
		}    

        //$CategoryOptions = ['error' => 0, 'msg' => 'Record has been added'];        
        $this->__outputJSON(['Api.CategoryOptions' => $entity]);

        $this->render(false);
               
    }
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




    public function recover()
    {
        $CategoryOptions = $this->CategoryOptions->recover();
        
        //$CategoryOptions = TableRegistry::getTableLocator()->get('CategoryOptions');
        //$CategoryOptions->recover();

    }


    
}