<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class NewsTable extends Table 
    {


    public function initialize(array $config)
    {
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Users');
        $this->hasMany('Files', [
	        'foreignKey' => 'record_id',
	        'conditions' => [ 'model_alias' => 'News' ] 
        ]);

        $this->hasOne('Cover', [
	        'foreignKey' => 'record_id',
	        'conditions' => [ 'model_alias' => 'News' ] ,
	        'className' => 'Files'
        ]);

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'modified' => 'always',
                ]
            ]
        ]);
    }




}
