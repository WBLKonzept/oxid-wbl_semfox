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
    $sWBLSEMFOXOXIDConfig  = class_exists('oxRegistry', false) ? oxRegistry::getConfig() : oxConfig::getInstance();
    $sWBLSEMFOXOXIDVersion = $sWBLSEMFOXOXIDConfig->getVersion();
    $aWBLSEMFOXClasses = array(
        'oxsearch' => 'WBL\SEMFOX\App\Model\Search.php'
    );
    $aWBLSEMFOXFiles = array();

    foreach ($aWBLSEMFOXClasses as $sClass) {
        // OXID needs the slash
        $aWBLSEMFOXFiles[$sClass] = str_replace('_', '/', $sClass) . '.php';
    } // foreach

    $aModule = array(
        'author'      => 'WBL Konzept',
        'blocks' => array(
            array(
                'block' => 'widget_header_search_form',
                'file' => 'views/blocks/widget_header_search_form.tpl',
                'template' => 'widget/header/search.tpl'
            )
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
                'group' => 'WBL_SEMFOX_GENERAL',
                'name'  => 'sWBLSEMFOXAPIKey',
                'type'  => (version_compare($sSysVersionForLoader, '4.9.0', '>=')) ? 'password' : 'str'
            ),
            array(
                'group' => 'WBL_SEMFOX_GENERAL',
                'name'  => 'sWBLSEMFOXCustomerId',
                'type'  => 'str'
            ),
            array(
                'group' => 'WBL_SEMFOX_SUGGEST',
                'name'  => 'sWBLSEMFOXSuggestThrottleTime',
                'type'  => 'str',
                'value' => 50
            ),
            array(
                'group' => 'WBL_SEMFOX_SUGGEST',
                'name'  => 'bWBLSEMFOXHighlight',
                'type'  => 'bool',
                'value' => true
            ),
            array(
                'group' => 'WBL_SEMFOX_SUGGEST',
                'name'  => 'bWBLSEMFOXSuggestURL',
                'type'  => 'str',
                'value' => 'http://semfox.com:8585/queries/suggest?apiKey=apiKey=kftruanreiotsdaifaiseapeiorsdafb&customerId=6&query='
            ),
            array(
                'group' => 'WBL_SEMFOX_SUGGEST',
                'name'  => 'bWBLSEMFOXQueryVisualizationHeadline',
                'type'  => 'str',
                'value' => 'Ihre Suche Visualisiert'
            ),
            array(
                'group' => 'WBL_SEMFOX_SUGGEST',
                'name'  => 'bWBLSEMFOXinstantVisualFeedback',
                'type'  => 'str',
                'value' => 'none'
            ),
            array(
                'group' => 'WBL_SEMFOX_SUGGEST',
                'name'  => 'bWBLSEMFOThrottleTime',
                'type'  => 'str',
                'value' => '50'
            ),
            array(
                'group' => 'WBL_SEMFOX_CONNECTION',
                'name'  => 'sWBLSEMFOXPort',
                'type'  => 'str',
                'value' => '8585'
            ),
            array(
                'group' => 'WBL_SEMFOX_CONNECTION',
                'name'  => 'sWBLSEMFOXConnectionTimeout',
                'type'  => 'str',
                'value' => '3'
            )
        ),
        'title'       => 'WBL SEMFOX',
        'thumbnail'   => 'wbl_logo.jpg',
        'url'         => 'http://wbl-konzept.de',
        'version'     => '1.0.0-dev'
    );