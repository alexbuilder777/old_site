<?php
namespace Api\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use App\Traits\ApiTrait;
use Cake\ORM\TableRegistry;

class AppController extends Controller
{

    use ApiTrait;
 
    public function initialize()
    {
        parent::initialize();
    }


    public function resortRecords()
    {
        $data = ($_GET) ? $_GET : $_POST;

        $model = $this->loadModel($data['model']);
       // $model->recover();

        $record = $model->get($data['record_id']);

        //If dropping bottom to top (DESC SORT)
        if($data['position_start'] < $_GET['position_end']) {//debug($data['position_start'] - $data['position_end']);
            $delta =  abs($data['position_start'] - $data['position_end']);debug('moveDown--'.$delta);
            $model->moveDown($record, $delta);

        } else {

            $delta = abs($data['position_end'] - $data['position_start']);debug('moveUp--'.$delta);
            //debug(abs($_GET['position_start'] - $_GET['position_end']));
            $model->moveUp($record, $delta);
        }

        $this->__outputJSON(['response' => $record]);
        $this->render(false);

    }

    /*
	 * Adapter For Api methods   
     */
    public function method($method, $params = null)
    {
        try {
	        $this->$method($params);		
        } catch (Exception\ExceptionApi $e) {
            return $e->uploadError();
        }	    
    }

    // Main API method
    public function get()
    {
	    //$this->apiAuth();
	    	
	    $params_default = [
		    'findType' => 'all',
		    'lastId' => 0,
		    'limit' => 500,
		    'between' => 0,
		    'where' => [$this->name . '.flag !=' => 'delete'],
		    'order' => [$this->name . '.id' => 'DESC'],
		    'first' => 0,
		    'contain' => null,
		    'innerJoin' => []
	    ];
	    
	    parse_str($this->request->getData('data'), $options);
	    $options = array_merge($params_default, $options, $_GET);

	    $modelName = $this->name;
	    
	    $query = $this->$modelName->find($options['findType']);

        if(isset($options['innerJoin'])) {
	        foreach($options['innerJoin'] as $assocModelName => $values) {	           

                $query->innerJoinWith(
                    $assocModelName, function ($q) use ($assocModelName, $values) {
                        return $q->where([$assocModelName . '.id IN' => $values]);
                    }
                );
           
	        }
        }
	  
	    $query->where($options['where'])->limit($options['limit'])->order($options['order']);

	    if($options['contain']) $query->contain($options['contain']);

	    
	    if($options['first']) $this->__outputJSON($query->first()->toArray());

	    $this->__outputJSON($query->toArray());

	    $this->render(false);
    }


    public function add()
    { 
	    parse_str($this->request->getData('data'), $data);
	    
	    $modelObj = TableRegistry::get('Api.' . $this->name);
	  
	    $optionsDefault = [
		    'contain' => [],
		    'where' => ['Nomenclatures.id' => '0'],
		    'first' => 1,
		    'limit' => 1
	    ];
	    $options = array_merge($optionsDefault, $_GET);
	   
	    if(!isset($data['crud'])) { 
		    $entity = 
		    $modelObj->find()
		        ->where($options['where'])
		        ->contain($options['contain'])
		        ->first();
		    $entity = ($entity) ? $entity->toArray() : [];
		    //debug($data);
		    if($data) $entity = $this->_add($data, $modelObj); 
		     
		    
		    $this->__outputJSON($entity);
		    
	    } else {
	    
	        if($data['crud'] == 'create') { 
		        
		        $this->apiAuth();
		        $entity = $modelObj->newEntity($data);
		        $modelObj->save($entity);
		        
		    } else if($data['crud'] == 'update') {
		    	
		    	$this->apiAuth();
		    	$entity = $modelObj->get($data['id']);
		    	$entity = $modelObj->patchEntity($entity, $data);
		    	$modelObj->save($entity);
		    	
		    } else {
		    	
		    	$entity = $modelObj->find()->where(['id' => $data['id']])->first();
		    	$entity = ($entity) ? $entity->toArray() : $modelObj->newEntity();
		    } 
		}   
       
        $this->__outputJSON(['Api.' . $this->name => $entity]);

        $this->render(false);
               
    }

    function _add($data, $modelObj)
    {
            $this->crudStatus = (isset($data['id']) && $data['id']) ? 'update' : 'add';

		    $entity = $modelObj->newEntity($data);
		    $modelObj->save($entity);
		    return $entity;

    }


    public function markDeleted($id = null)
    {
	    // Распаковываем пришедшие через пост данные
	    parse_str($this->request->getData('data'), $data);
	    
	    $modelName = $this->name;
	    
	    if(isset($data['ids'])) {
		    
		    $data['ids'] = unserialize($data['ids']);	    
		    $records = [];
		    foreach($data['ids'] as $id) {

                if($record = $this->$modelName->get($id)) {
	                $record['flag'] = 'delete';
	                $this->$modelName->save($record);
	                $records[] = $record;	                
                }
                
		    }
		    $this->__outputJSON(['News' => $records]);
	    } else if($id) {
	    
            if($record = $this->$modelName->get($id)) {
	            $record['flag'] = 'delete';
	            $this->$modelName->save($record);
	            $this->__outputJSON(['News' => $record]);
            }
        }
        
        $this->render(false);

    }


    // Set response as JSON
    public function __outputJSON($data) {
	    header('Content-Type: application/json; charset=UTF-8');

		$response['response'] = $data;
		
        die(json_encode( $response, JSON_NUMERIC_CHECK ));
    }

    
}
