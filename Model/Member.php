<?php
App::uses('TrackerAppModel', 'Tracker.Model');

/**
 * This model is here solely for permissions pickup
 */
class Member extends TrackerAppModel {
	
	public $useTable = false;
	
	

	public $belongsTo = array(
		'Self' => array(
			'className' => 'Users.User',
			'foreignKey' => 'id',
			'conditions' => '',
			'fields' => array('id'),
			'order' => ''
			)
		);
	
}