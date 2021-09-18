<?php
namespace Api\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class CategoryOptionsTable extends Table 
    {


    public function initialize(array $config)
    {
	    
	    $this->addBehavior('Tree');
	    
	    $this->belongsToMany('Categories');

        $this->addAssociations([
            'hasMany' => [
                'CategoryOptionsValues' => [
	                'conditions' => ['CategoryOptionsValues.flag' => 'on']
                ]
            ]
        ]);

    
    }




}
