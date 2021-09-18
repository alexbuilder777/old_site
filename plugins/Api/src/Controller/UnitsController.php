<?php
namespace Api\Controller;

use Api\Controller\AppController;

class UnitsController extends AppController
{

    function attachUnit()
    {
	    if($this->request->getData('id')) throw new \Api\Exception\ExceptionApi('Не указан ID');
	    
	    parse_str($this->request->getData('data'), $data); 
	  
	    $data['rus_name'] = (isset($data['rus_name1'])) ? $data['rus_name1'] : '';
	    $data['eng_name'] = (isset($data['eng_name1'])) ? $data['eng_name1'] : '';
	    
	    $entity =  $this->Units->newEntity($data);
	 
	    if($this->Units->save($entity)) {
		    $this->__outputJSON($entity);
	    } else {
		    throw new \Api\Exception\ExceptionApi($entity);
	    }
	    
	    $this->render(false);
    }

    function detach($id)
    {
	    $entity = $this->Units->find()->where(['id' => $id])->first();
	    if(!$entity) throw new \Api\Exception\ExceptionApi('Запись не найдена');
	    $this->Units->delete($entity);
	    $this->__outputJSON($entity);
    }

}