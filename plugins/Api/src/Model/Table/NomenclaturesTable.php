<?php
namespace Api\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class NomenclaturesTable extends Table 
    {


    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'modified' => 'always',
                ]
            ]
        ]);

        $this->belongsToMany('Categories');
        $this->belongsToMany('CategoryOptionsValues');

        $this->hasOne('Images', [
	        'foreignKey' => 'record_id',
	        'conditions' => [ 'model_alias' => 'Nomenclatures' ] ,
	        'className' => 'Files'
        ]);

        $this->hasOne('Cover', [
	        'foreignKey' => 'record_id',
	        'conditions' => [ 'model_alias' => 'News' ] ,
	        'className' => 'Files'
        ]);

    }




}
