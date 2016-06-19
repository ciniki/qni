<?php
//
// Description
// -----------
//
// Arguments
// ---------
//
// Returns
// -------
//
function ciniki_qni_objects($ciniki) {
	
	$objects = array();
	$objects['net'] = array(
		'name'=>'Nets',
		'o_name'=>'net',
		'o_container'=>'nets',
		'sync'=>'yes',
		'table'=>'ciniki_qni_nets',
		'fields'=>array(
			'name'=>array('name'=>'Net Name'),	// This gets returned if arguments aren't parsed correctly by the API
			'permalink'=>array('name'=>'Permalink'),
			'net_type'=>array('name'=>'Net Type'),	// Mandatory
			'flags'=>array('name'=>'Options','default'=>'0'),
			'status'=>array('name'=>'Status','default'=>'10'),
			'primary_image_id'=>array('name'=>'Primary Image','ref'=>'ciniki.images.image','default'=>'0'),
			'synopsis'=>array('name'=>'Synopsis','default'=>''),
			'description'=>array('name'=>'Description','default'=>''),
			),
		'history_table'=>'ciniki_qni_history',
		);
	
	return array('stat'=>'ok', 'objects'=>$objects);
}
?>
