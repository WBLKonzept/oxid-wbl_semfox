<?php
    /**
     * Module-Metadata for the SEMFOX module.
     * @author     blange <code@wbl-konzept.de>
     * @category   modules
     * @package    WBL_SEMFOX
     * @subpackage oxAutoload
     * @version    $id$
     */

    $sMetadataVersion      = '1.1';
    $aWBLSEMFOXClasses = array();

    $aWBLSEMFOXFiles = array();

    foreach ($aWBLSEMFOXClasses as $sClass) {
        // OXID needs the slash
        $aWBLSEMFOXFiles[$sClass] = str_replace('_', '/', $sClass) . '.php';
    } // foreach

    $aModule = array(
        'author'      => 'WBL Konzept',
        'blocks' => array(
            'block' => 'widget_header_search_form',
            'file' => 'views/blocks/widget_header_search_form.tpl',
            'template' => 'widget/header/search.tpl'
        ),
        'description' => array(
            'de' => 'SEMFOX-Connector',
            'en' => 'SEMFOX-Connector'
        ),
        'email'       => 'code@wbl-konzept.de',
        'extend'      => $aWBLSEMFOXClasses,
        'files'       => $aWBLSEMFOXFiles,
        'id'          => 'WBL_SEMFOX',
        'settings'    => array(
            array(
                'group' => 'WBL_SEMFOX_GENERAL'
            ),
            array(
                'group' => 'WBL_SEMFOX_SUGGEST'
            ),
            array(
                'group' => 'WBL_SEMFOX_CONNECTION'
            )
        ),
        'title'       => 'WBL SEMFOX',
        'thumbnail'   => 'wbl_logo.jpg',
        'url'         => 'http://wbl-konzept.de',
        'version'     => '1.0.0-dev'
    );