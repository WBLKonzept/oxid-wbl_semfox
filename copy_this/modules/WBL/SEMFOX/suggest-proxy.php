<?php
    // clean and fast suggest proxy requested by semfox for hiding api keys etc.

    $sShopBaseDir = realpath(__DIR__ . '/../../../') . DIRECTORY_SEPARATOR;

    if (file_exists($sShopBaseDir . 'bootstrap.php')) {
        require_once $sShopBaseDir . 'bootstrap.php';

        $oConfig = oxRegistry::getConfig();
        $oUtils  = oxRegistry::getUtils();
        $sQuery  = $oConfig->getRequestParameter('query', true);
    } else {
        function getShopBasePath()
        {
            return $GLOBALS['sShopBaseDir'];
        } // function

        require_once $sShopBaseDir . 'modules/functions.php';
        require_once $sShopBaseDir . 'core/oxfunctions.php';

        @$oConfig = oxConfig::getInstance();
        $oUtils = oxUtils::getInstance();
        $sQuery = oxConfig::getParameter('query');
    } // else

    $sContent = '';

    if ($sQuery) {
        if (!$sPort = $oConfig->getConfigParam('sWBLSEMFOXPort')) {
            $sPort = 8585;
        } // if

        $sContent = file_get_contents(
            "http://semfox.com:{$sPort}/queries/suggest?apiKey=apiKey={$oConfig->getConfigParam('sWBLSEMFOXAPIKey')}&customerId={$oConfig->getConfigParam('sWBLSEMFOXCustomerId')}&query={$sQuery}"
        );
    } // if

    if (!$sContent) {
        error_404_handler();
    } else {
        $oUtils->setHeader('Content-Type: application/json;charset="UTF-8"');
        $oUtils->setHeader('Content-Length: ' . strlen($sContent));
        print $sContent;
    } // else
