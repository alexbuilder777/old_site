<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class ContentsTable extends Table 
    {


    public function initialize(array $config)
    {
	    
	    $this->addBehavior('Tree');
	    
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Users');
        $this->hasMany('Images', [
	        'foreignKey' => 'record_id',
	        'conditions' => [ 'model_alias' => 'Contents', 'flag' => 'on', 'label' => 'content' ],
           // 'order' => ['lfty' => 'DESC'],
	        'className' => 'Files' 
        ]);

        $this->hasOne('Cover', [
	        'foreignKey' => 'record_id',
	        'conditions' => [ 'model_alias' => 'Contents' ] ,
	        'className' => 'Files'
        ]);

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'date_created' => 'new',
                    'date_modified' => 'always',
                ]
            ]
        ]);
    }




}
