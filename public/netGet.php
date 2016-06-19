<?php
//
// Description
// ===========
// This method will return all the information about an nets.
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id:         The ID of the business the nets is attached to.
// net_id:          The ID of the nets to get the details for.
//
// Returns
// -------
//
function ciniki_qni_netGet($ciniki) {
    //
    // Find all the required and optional arguments
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'),
        'net_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Nets'),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $args = $rc['args'];

    //
    // Make sure this module is activated, and
    // check permission to run this function for this business
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'qni', 'private', 'checkAccess');
    $rc = ciniki_qni_checkAccess($ciniki, $args['business_id'], 'ciniki.qni.netGet');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }

    //
    // Load business settings
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'businesses', 'private', 'intlSettings');
    $rc = ciniki_businesses_intlSettings($ciniki, $args['business_id']);
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $intl_timezone = $rc['settings']['intl-default-timezone'];

    //
    // Return default for new Nets
    //
    if( $args['net_id'] == 0 ) {
        $net = array('id'=>0,
            'name'=>'',
            'permalink'=>'',
            'net_type'=>'200',
            'flags'=>'0',
            'status'=>'10',
            'primary_image_id'=>'0',
            'synopsis'=>'',
            'description'=>'',
        );
    }

    //
    // Get the details for an existing Nets
    //
    else {
        $strsql = "SELECT ciniki_qni_nets.id, "
            . "ciniki_qni_nets.name, "
            . "ciniki_qni_nets.permalink, "
            . "ciniki_qni_nets.net_type, "
            . "ciniki_qni_nets.flags, "
            . "ciniki_qni_nets.status, "
            . "ciniki_qni_nets.primary_image_id, "
            . "ciniki_qni_nets.synopsis, "
            . "ciniki_qni_nets.description "
            . "FROM ciniki_qni_nets "
            . "WHERE ciniki_qni_nets.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
            . "AND ciniki_qni_nets.id = '" . ciniki_core_dbQuote($ciniki, $args['net_id']) . "' "
            . "";
        ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQuery');
        $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.qni', 'net');
        if( $rc['stat'] != 'ok' ) {
            return array('stat'=>'fail', 'err'=>array('pkg'=>'ciniki', 'code'=>'4012', 'msg'=>'Nets not found', 'err'=>$rc['err']));
        }
        if( !isset($rc['net']) ) {
            return array('stat'=>'fail', 'err'=>array('pkg'=>'ciniki', 'code'=>'4013', 'msg'=>'Unable to find Nets'));
        }
        $net = $rc['net'];
    }

    return array('stat'=>'ok', 'net'=>$net);
}
?>
