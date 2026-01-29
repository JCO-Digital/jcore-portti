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
			'termIds' => array(
				'type' => 'array',
				'default' => array(
					
				),
				'items' => array(
					'type' => 'number'
				)
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
