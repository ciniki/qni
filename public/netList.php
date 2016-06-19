<?php
//
// Description
// -----------
// This method will return the list of Netss for a business.
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id:        The ID of the business to get Nets for.
//
// Returns
// -------
//
function ciniki_qni_netList($ciniki) {
    //
    // Find all the required and optional arguments
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $args = $rc['args'];

    //
    // Check access to business_id as owner, or sys admin.
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'qni', 'private', 'checkAccess');
    $rc = ciniki_qni_checkAccess($ciniki, $args['business_id'], 'ciniki.qni.netList');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }

    //
    // Get the list of nets
    //
    $strsql = "SELECT ciniki_qni_nets.id, "
        . "ciniki_qni_nets.name, "
        . "ciniki_qni_nets.permalink, "
        . "ciniki_qni_nets.net_type, "
        . "ciniki_qni_nets.flags, "
        . "ciniki_qni_nets.status "
        . "FROM ciniki_qni_nets "
        . "WHERE ciniki_qni_nets.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
        . "";
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryArrayTree');
    $rc = ciniki_core_dbHashQueryArrayTree($ciniki, $strsql, 'ciniki.qni', array(
        array('container'=>'nets', 'fname'=>'id', 
            'fields'=>array('id', 'name', 'permalink', 'net_type', 'flags', 'status')),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( isset($rc['nets']) ) {
        $nets = $rc['nets'];
    } else {
        $nets = array();
    }

    return array('stat'=>'ok', 'nets'=>$nets);
}
?>
