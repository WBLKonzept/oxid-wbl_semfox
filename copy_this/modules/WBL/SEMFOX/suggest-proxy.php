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
        $oUtils   = oxUtils::getInstance();
        $sQuery   = oxConfig::getParameter('query');
    } // else

    use SEMFOX\Wrapper,
        SEMFOX\Transport\Exception as SEMFOXException;

    $sContent = '';

    if ($sQuery) {
        $oSF = new Wrapper(array(
            'apiKey'         => $oConfig->getConfigParam('sWBLSEMFOXAPIKey'),
            'customerId'     => $oConfig->getConfigParam('sWBLSEMFOXCustomerId'),
            'requestTimeout' => (int) $oConfig->getConfigParam('sWBLSEMFOXSuggestTimeout')
        ));

        try {
            $sContent = (string) $oSF->queries->suggest->get(array(
                'limit'       => ($iLimit = (int) $oConfig->getConfigParam('sWBLSEMFOXSuggestArticleLimit')) ? $iLimit : 8,
                'numSuggests' => ($iLimit = (int) $oConfig->getConfigParam('sWBLSEMFOXSuggestSearchLimit')) ? $iLimit : 5,
                'query'       => $sQuery
            ));
        } catch (SEMFOXException $oExc) {
            // silent catch
        } // catch
    } // if

    if (!$sContent) {
        error_404_handler();
    } else {
        $oUtils->setHeader('Content-Type: application/json;charset="UTF-8"');
        $oUtils->setHeader('Content-Length: ' . strlen($sContent));
        print $sContent;
    } // else
