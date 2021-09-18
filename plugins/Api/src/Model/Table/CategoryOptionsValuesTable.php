<?php
namespace Api\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class CategoryOptionsValuesTable extends Table 
{

    public function initialize(array $config)
    {	    
	    $this->addBehavior('Tree');
    }


}
