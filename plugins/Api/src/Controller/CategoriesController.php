<?php
namespace Api\Controller;

use Cake\ORM\TableRegistry;
use Api\Controller\AppController;
use App\Traits\CurlTrait;
use App\Traits\ApiTrait;
use App\Traits\TranslateTrait;

class CategoriesController extends AppController
{
    use CurlTrait;
    use ApiTrait;
    use TranslateTrait;

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

                if($record = $this->Categories->get($id)) {
	                $record['flag'] = 'delete';
	                $this->Categories->save($record);
	                $records[] = $record;	                
                }
                
		    }
		    $this->__outputJSON(['News' => $records]);
	    } else if($id) {
	    
            if($record = $this->Categories->get($id)) {
	            $record['flag'] = 'delete';
	            $this->Categories->save($record);
	            $this->__outputJSON(['News' => $record]);
            }
        }
        
        $this->render(false);

    }


/*
    public function setAliases()
    {
        $categories = $this->Categories->find();
       
        foreach($categories as $category) { debug($category['id']);
	        $category['alias'] = strtolower($this->__translit($category['name']));
	        if($category['id'] == 86) debug($category);
	        $this->Categories->save($category);
        }
        
        $this->render(false);
    }
*/


    public function index(){}

    /*
	 * Getting API datas from Remote Server with Auth and saving record (05.01.2019)
	 *
	 * return new Entity   
     */



    function bindOptions()
    {
	    $model = TableRegistry::get('CategoriesCategoryOptions');

		$node = $model->find()
		    ->where(['category_id' => $_GET['category_id'], 'category_option_id' => $_GET['category_option_id']])->first();

	    if($_GET['status'] && !$node) {
		    
		    $entity = $model->newEntity(['category_id' => $_GET['category_id'], 'category_option_id' => $_GET['category_option_id']]);
            $node = $model->save($entity);
            $this->__outputJSON(['response' => $node]);
	    } else if(!$_GET['status'] && $node) {
		    $node = $model->delete($node);
	    }
	    
	    
	    $this->render(false);
    }







/*
    public function add()
    { 
	    // Распаковываем пришедшие через пост данные
	    parse_str($this->request->getData('data'), $data);
	    
	    $CategoriesObj = TableRegistry::get('Api.Categories');
	   
	    if($data['crud'] == 'create'){ 
		    
		    $this->apiAuth();
		    $entity = $CategoriesObj->newEntity($data);
		    $CategoriesObj->save($entity);
		    
		} else if($data['crud'] == 'update') {
			
			$this->apiAuth();
			$entity = $CategoriesObj->get($data['id']);
			$entity = $CategoriesObj->patchEntity($entity, $data);
			$CategoriesObj->save($entity);
			
		} else {
			
			$entity = $CategoriesObj->find()->where(['id' => $data['id']])->first()->toArray();
		}    

        //$Categories = ['error' => 0, 'msg' => 'Record has been added'];        
        $this->__outputJSON(['Api.Categories' => $entity]);

        $this->render(false);
               
    }
*/

/*
    function get($id = null)
    {
	    //TableRegistry::get('Api.Categories')->get($id);
	    debug($this->request->getData());
	    $id = (!$id) ? $this->request->getData('id') : $id;
	    
	    if(TableRegistry::get('Api.Categories')->exists(['id' => $id])) $this->__outputJSON(['Api.Categories' => TableRegistry::get('Api.Categories')->get($id)]);
	    $this->__outputJSON(['error' => 'Запись не найдена']);
	    
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
        $categories = $this->Categories->recover();
        
        //$categories = TableRegistry::getTableLocator()->get('Categories');
        //$categories->recover();

    }


    
}