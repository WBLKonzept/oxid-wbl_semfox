<?php
    /**
     * ./modules/WBL/SEMFOX/app/model/wblsemfox_article.php
     * @author     blange <code@wbl-konzept.de>
     * @category   modules
     * @package    WBL_SEMFOX
     * @subpackage app/model
     * @version    $id$
     */

    use SEMFOX\Wrapper,
        SEMFOX\Transport\Exception as SEMFOXException;

    /**
     * The article module for the SEMFOX Connection.
     * @author     blange <code@wbl-konzept.de>
     * @category   modules
     * @package    WBL_SEMFOX
     * @subpackage app/model
     * @version    $id$
     */
    class WBLSEMFOX_Article extends WBLSEMFOX_Article_parent {
        /**
         * The SEMFOX-Wrapper or void.
         * @todo Refactor on the next (third) copy to a factory!
         * @var void|\SEMFOX\Wrapper
         */
        protected $oWBLSEMFOXWrapper = null;

        /**
         * Returns the name of the main category in the actual language for the actual subshop.
         * @return string
         */
        public function getMainCatNameForWBLSEMFOX()
        {
            $sReturn = '';

            if (($oCat = $this->getCategory()) && ($oCat->getId())) {
                $sReturn = $oCat->oxcategories__oxtitle->value;
            } // if

            return $sReturn;
        } // function

        /**
         * Lazy-Loads the SEMFOX-Wrapper.
         * @return \SEMFOX\Wrapper
         */
        protected function getWBLSEMFOXWrapper()
        {
            if (!$this->oWBLSEMFOXWrapper) {
                $oConfig = $this->getConfig();

                $this->setWBLSEMFOXWrapper(new Wrapper(array(
                    'apiKey'     => $oConfig->getConfigParam('sWBLSEMFOXAPIKey'),
                    'customerId' => $oConfig->getConfigParam('sWBLSEMFOXCustomerId'),
                    'restPort'   => $oConfig->getConfigParam('sWBLSEMFOXPort'),
                )));
            } // if

            return $this->oWBLSEMFOXWrapper;
        } // function

        /**
         * This function is triggered whenever article is saved or deleted or after the stock is changed.
         * Originally we need to update the oxstock for possible article parent in case parent is not buyable
         * Plus you may want to extend this function to update some extended information.
         * Call oxArticle::onChange($mAction, $mOXID) with ID parameter when changes are executed over SQL.
         * (or use module class instead of oxArticle if such exists)
         *
         * @param string|void $mAction   Action constant
         * @param string|void $mOXID     Article ID
         * @param string|void $mParentId Parent ID
         * @return mixed
         * @todo   Should the stock update be handled aswell? For fast stock changes, we should write a batch update!
         */
        public function onChange($mAction = null, $mOXID = null, $mParentId = null)
        {
            $mParent = parent::onChange($mAction, $mOXID, $mParentId);

            if ($mAction === ACTION_DELETE && $this->isLoaded()) {
                $this->deleteForWBLSEMFOX();
            } elseif ($this->getId() &&
                ($mAction === ACTION_INSERT || $mAction === ACTION_UPDATE))
            {
                $this->updateWBLSEMFOX();
            } // else

            return $mParent;
        } // function

        /**
         * Removes the article from the SEMFOX data.
         *
         * This method needs the data of the object, loaded in the delete call, so no params!
         * @return int The count of deleted elements.
         */
        protected function deleteForWBLSEMFOX()
        {
            $iReturn = 0;

            try {
                $iReturn = (int) $this->getWBLSEMFOXWrapper()->products->delete(array(
                    'articleNumber' => $this->{'oxarticles__' . $this->getConfig()->getConfigParam('sWBLSEMFOXIDField')}->value
                ));
            } catch (SEMFOXException $oExc) {
                /** @var WBLSEMFOX_Logger $oLogger */
                $oLogger = class_exists('oxRegistry') ? oxRegistry::get('WBLSEMFOX_Logger') : oxNew('WBLSEMFOX_Logger');
                $oLogger->logErrror($oExc);
            } // catch

            return $iReturn;
        } // function

        /**
         * Sets the used SEMFOX-Wrapper!
         * @param Wrapper $oWrapper
         * @return WBLSEMFOX_Article
         */
        protected function setWBLSEMFOXWrapper(Wrapper $oWrapper)
        {
            $this->oWBLSEMFOXWrapper = $oWrapper;
            unset($oWrapper);

            return $this;
        } // function

        /**
         * Updates the article for semfox.
         * @return WBLSEMFOX_Article
         * @todo   Refactor to Request-Adapter classes?
         */
        protected function updateWBLSEMFOX()
        {
            if ($aMapping = $this->getConfig()->getConfigParam('aWBLSEMFOXFieldMapping')) {
                $aData = array();

                foreach ($aMapping as $sOXID => $sSEMFOX) {
                    $aData[$sSEMFOX] = $sOXID;

                    if (strpos($sOXID, '(') !== false) {
                        $aData[$sSEMFOX] = $this->{str_replace('()', '', $sOXID)}();
                    } else {
                        $aData[$sSEMFOX] = $this->{'oxarticles__' . $sOXID}->value;
                    } // else
                } // foreach
            } // if

            try {
                $this->getWBLSEMFOXWrapper()->products->put(array('productsJsonArray' => $aData)); // No Return value till yet.
            } catch (SEMFOXException $oExc) {
                /** @var WBLSEMFOX_Logger $oLogger */
                $oLogger = class_exists('oxRegistry') ? oxRegistry::get('WBLSEMFOX_Logger') : oxNew('WBLSEMFOX_Logger');
                $oLogger->logErrror($oExc);
            } // catch

            return $this;
        } // function
    } // class