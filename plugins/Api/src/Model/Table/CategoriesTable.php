<?php
namespace Api\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class CategoriesTable extends Table 
    {


    public function initialize(array $config)
    {	    
	    $this->addBehavior('Tree');

        $this->belongsToMany('CategoryOptions');
    }




}
