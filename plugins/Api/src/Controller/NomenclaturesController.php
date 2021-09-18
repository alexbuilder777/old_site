<?php
namespace Api\Controller;

use Api\Controller\AppController;
use Cake\ORM\TableRegistry;
use App\Traits\TranslateTrait;

class NomenclaturesController extends AppController
{
	
	use TranslateTrait;

    function parsePriceSave()
    {
        try {
	        $this->__parsePriceSave();		
        } catch (\App\Exception\Exception $e) {
            return $e->uploadError();
        }   
	}    

    public function setAliases()
    {
        $categories = $this->Nomenclatures->find();
       
        foreach($categories as $category) {
	        $category['link'] = strtolower($this->__translit($category['name']));
	       
	        $this->Nomenclatures->save($category);
        }
        
        $this->render(false);
    }

    function __parsePriceSave()
    {
	    parse_str($this->request->getData('data'), $data);
        
        $saveCount = 0;
        foreach($data['nmcl'] as $nmcl) {
	        
	        $savedNmcl = $this->Nomenclatures->save($this->Nomenclatures->newEntity($nmcl));
	        
/*
	        if(isset($nmcl['categories']['_ids'])) {
		        foreach($nmcl['categories']['_ids'] as $category_id) {
			        
                    $node = [];
			        $node['category_id'] = $category_id;
			        $node['nomenclature_id'] = $savedNmcl['id'];
			        //TableRegistry::get('CategoriesNomenclatures')->save(TableRegistry::get('CategoriesNomenclatures')->newEntity($node));

		        }
	        }
*/
	        
	        $saveCount++;
        }
        
        $this->__outputJSON(['msg' => 'Сохранено ' . $saveCount . ' записей', 'code' => '0']);	    
    }

    function bindOptions()
    {
	    $model = TableRegistry::get('CategoriesNomenclatures');

		$node = $model->find()
		    ->where(['nomenclature_id' => $_GET['nomenclature_id'], 'category_id' => $_GET['category_id']])->first();

	    if($_GET['status'] && !$node) {
		    
		    $entity = $model->newEntity(['nomenclature_id' => $_GET['nomenclature_id'], 'category_id' => $_GET['category_id']]);
            $node = $model->save($entity);
            $this->__outputJSON(['response' => $node]);
	    } else if(!$_GET['status'] && $node) {
		    $node = $model->delete($node);
	    }
	    
	    
	    $this->render(false);
    }

    function bindValues()
    {
	    $model = TableRegistry::get('CategoryOptionsValuesNomenclatures');

		$node = $model->find()
		    ->where(['nomenclature_id' => $_GET['nomenclature_id'], 'category_options_value_id' => $_GET['category_options_value_id']])->first();

	    if($_GET['status'] && !$node) {
		    
		    $entity = $model->newEntity(['nomenclature_id' => $_GET['nomenclature_id'], 'category_options_value_id' => $_GET['category_options_value_id']]);
            $node = $model->save($entity);
            $this->__outputJSON(['response' => $node]);
	    } else if(!$_GET['status'] && $node) {
		    $node = $model->delete($node);
	    }
	    
	    
	    $this->render(false);
    }

}