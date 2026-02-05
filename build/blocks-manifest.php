<?php
// This file is generated. Do not modify it manually.
return array(
	'portal-slot' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'jco/portal-slot',
		'version' => '0.1.0',
		'title' => 'JCORE Campaign Portal Slot',
		'category' => 'widgets',
		'icon' => 'share-alt',
		'description' => '',
		'attributes' => array(
			'slotId' => array(
				'type' => 'string',
				'default' => ''
			),
			'maxItems' => array(
				'type' => 'number',
				'default' => 1
			)
		),
		'example' => array(
			
		),
		'supports' => array(
			'html' => false
		),
		'textdomain' => 'jcore-portti',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'render' => 'file:./render.php'
	)
);
