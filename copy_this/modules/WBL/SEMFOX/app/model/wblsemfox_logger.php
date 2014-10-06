<?php
    /**
     * ./modules/WBL/SEMFOX/app/model/wblsemfox_logger.php
     * @author     blange <code@wbl-konzept.de>
     * @category   modules
     * @package    WBL_SEMFOX
     * @subpackage app/model
     * @version    $id$
     */

    use SEMFOX\Transport\Exception as SEMFOXException;

    /**
     * The logger for the module.
     * @author     blange <code@wbl-konzept.de>
     * @category   modules
     * @package    WBL_SEMFOX
     * @subpackage app/model
     * @version    $id$
     */
    class WBLSEMFOX_Logger extends oxBase {
        /**
         * Logs an Error for the semfox search.
         * @param Exception $oExc
         * @param mixed $mContext
         * @return WBLSEMFOX_Logger
         */
        public function logErrror(Exception $oExc, $mContext = null)
        {
            $oConfig  = $this->getConfig();
            $bWithMail = (bool) $sMail = $oConfig->getConfigParam('sAdminEmail');
            $sMessage = 'Message: ' . $oExc->getMessage() . '(' . $oExc->getCode() . ')';

            if ($oExc instanceof SEMFOXException && !$mContext) {
                $mContext = $oExc->getRequestContext();
            } // if

            if ($mContext) {
                $sMessage .= " | Context: \n" . print_r($mContext, true);
            } // if

            if ($oConfig->getConfigParam('bWBLSEMFOXWithLogging')) {
                /** @var oxUtils $oUtils */
                $oUtils    = class_exists('oxRegistry') ? oxRegistry::getUtils() : oxUtils::getInstance();
                $sLogFile  = 'semfox_' . date('Ymd') . '.log';

                $bWithMail = is_readable($sFileDir = $this->getConfig()->getLogsDir() . $sLogFile) &&
                    strpos(file_get_contents($sFileDir), $sMessage) === false;

                $oUtils->writeToLog(date('r') . ': ' . $sMessage . "\n", $sLogFile);
            } // if

            if ($bWithMail) {
                oxNew('oxemail')->sendEmail($sMail, 'SEMFOX error', $sMessage);
            } // if

            return $this;
        } // function
    } // class