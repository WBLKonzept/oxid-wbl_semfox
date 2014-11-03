<?php
    /**
     * ./modules/WBL/SEMFOX/app/model/wblsemfox_articlelist.php
     * @author     blange <code@wbl-konzept.de>
     * @category   modules
     * @package    WBL_SEMFOX
     * @subpackage app/model
     * @version    $id$
     */

    use SEMFOX\Response,
        SEMFOX\Transport\Exception as SEMFOXException,
        SEMFOX\Wrapper;

    /**
     * The article list module for the SEMFOX Connection.
     * @author     blange <code@wbl-konzept.de>
     * @category   modules
     * @package    WBL_SEMFOX
     * @subpackage app/model
     * @version    $id$
     */
    class WBLSEMFOX_ArticleList extends WBLSEMFOX_ArticleList_parent
    {
        /**
         * Is the search with semfox skipped in this runtime?
         * @var bool
         */
        protected $bSkipWBLSEMFOXSearch = false;

        /**
         * The SEMFOX-Wrapper or void.
         * @var void|\SEMFOX\Wrapper
         */
        protected $oWBLSEMFOXWrapper = null;

        /**
         * Returns a select finding articles.
         * @param Response $oHit
         * @return string
         */
        protected function getWBLSEMFOXSearchSelect(Response $oHit = null)
        {
            $sReturn = '';

            if ($oHit->searchResults) {
                /** @var oxArticle $oArticle */
                $aNos     = array();
                $oArticle = oxNew('oxarticle');
                $sReturn  = "SELECT OXID FROM {$oArticle->getViewName()} WHERE {$oArticle->getSqlActiveSnippet()} ";

                foreach ($oHit->searchResults as $aHits) {
                    $aHit   = current($aHits);// TODO Config which of the hit?
                    $aNos[] = $aHit->articleNumber;
                } // foreach

                $sReturn .= ' AND ' . $this->getConfig()->getConfigParam('sWBLSEMFOXIDField') .
                    ' IN (' . implode(',', oxDb::getDb()->quoteArray($aNos)) . ')';

                if ($this->_sCustomSorting) {
                    $sReturn .= 'ORDER BY ' . $this->_sCustomSorting;
                } // if
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
                    'apiKey'         => $oConfig->getConfigParam('sWBLSEMFOXAPIKey'),
                    'customerId'     => $oConfig->getConfigParam('sWBLSEMFOXCustomerId'),
                    'requestTimeout' => $oConfig->getConfigParam('sWBLSEMFOXConnectionTimeout')
                )));
            } // if

            return $this->oWBLSEMFOXWrapper;
        } // function

        /**
         * Is the search skipped in this runtime?
         * @param bool $bNewStatus The new status.
         * @return bool The old status.
         */
        public function isWBLSEMFOXSearchSkipped($bNewStatus = false)
        {
            $bOldStatus = $this->bSkipWBLSEMFOXSearch;

            if (func_num_args()) {
                $this->bSkipWBLSEMFOXSearch = $bNewStatus;
            } // if

            return $bOldStatus;
        } // function

        /**
         * Loads only ID's and create Fake objects for cmp_categories.
         *
         * @param string $sSearchStr          Search string
         * @param string $sSearchCat          Search within category
         * @param string $sSearchVendor       Search within vendor
         * @param string $sSearchManufacturer Search within manufacturer
         *
         * @return null;
         */
        public function loadSearchIds($sSearchStr = '', $sSearchCat = '',  $sSearchVendor = '', $sSearchManufacturer = '')
        {
            $bSearched = false;
            $mReturn   = null;

            // SEMFOX uses the string only!
            if ($sSearchStr && !$sSearchCat && !$sSearchVendor && !$sSearchManufacturer && !$this->isWBLSEMFOXSearchSkipped()) {
                try {
                    $oHit = $this->getWBLSEMFOXWrapper()->products->get(array('query' => $sSearchStr));

                    if ($sSelect = $this->getWBLSEMFOXSearchSelect($oHit)) {
                        $bSearched = true;

                        $this->_createIdListFromSql($sSelect);
                    } // if
                } catch (SEMFOXException $oExc) {
                    // Silent catch for the pagination.
                } // carch
            } // if

            if (!$bSearched) {
                $mReturn = parent::loadSearchIds($sSearchStr, $sSearchCat,  $sSearchVendor, $sSearchManufacturer);
            } // if

            return $mReturn;
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
    } // class