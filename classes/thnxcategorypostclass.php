<?php

class thnxcategorypostclass extends ObjectModel
{
	public $id;
	public $id_thnx_category_post;
	public $id_post;
	public $id_category;
	public $type;
	public static $definition = array(
		'table' => 'thnx_category_post',
		'primary' => 'id_thnx_category_post',
		'multilang' => false,
		'fields' => array(
			'id_post' =>	array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
			'id_category' =>	array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
			'type' =>	array('type' => self::TYPE_STRING, 'validate' => 'isString'),
		),
	);
}