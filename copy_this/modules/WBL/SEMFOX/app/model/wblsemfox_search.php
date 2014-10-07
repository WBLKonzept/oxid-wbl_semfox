<?php
    /**
     * ./modules/WBL/SEMFOX/app/model/wblsemfox_search.php
     * @author     blange <code@wbl-konzept.de>
     * @category   modules
     * @package    WBL_SEMFOX
     * @subpackage app/model
     * @version    $id$
     */

    use SEMFOX\Wrapper,
        SEMFOX\Transport\Exception as SEMFOXException;

    require_once __DIR__ . DIRECTORY_SEPARATOR . 'wblsemfox_logger.php';

    /**
     * The search module for OXID.
     * @author     blange <code@wbl-konzept.de>
     * @category   modules
     * @package    WBL_SEMFOX
     * @subpackage app/model
     * @version    $id$
     */
    class WBLSEMFOX_Search extends WBLSEMFOX_Search_parent {
        /**
         * Should SEMFOX be used for the search?
         * @var bool
         */
        protected $bWithWBLSEMFOXSearch = true;

        /**
         * The last SEMFOX search hit.
         * @var stdClass|null
         */
        static protected $oLastWBLSEMFOXHit = null;

        /**
         * The SEMFOX-Wrapper or void.
         * @var void|\SEMFOX\Wrapper
         */
        protected $oWBLSEMFOXWrapper = null;

        /**
         * Sets the page for the search and returns the limit additionally.
         * @return array The first value is the limit and the second value is the offset.
         */
        protected function getAndSetOffsetAndLimitForWBLSEMFOX()
        {
            $oConfig = $this->getConfig();

            // sets active page like the oxid standard.
            $this->iActPage = (int) $oConfig->getParameter('pgNr');
            $this->iActPage = ($this->iActPage < 0) ? 0 : $this->iActPage;

            $iNrOfCatArticles = (int) $oConfig->getConfigParam('iNrofCatArticles');
            $iNrOfCatArticles = $iNrOfCatArticles ? $iNrOfCatArticles : 10; // PHP 5.3 Style.

            return array($iNrOfCatArticles, $this->iActPage * $iNrOfCatArticles);
        } // function

        /**
         * Returns the last SEMFOX Hit.
         * @return null|stdClass
         */
        static public function getLastWBLSEMFOXHit() {
            return static::$oLastWBLSEMFOXHit;
        } // function

        /**
         * Returns a list of articles according to search parameters. Returns matched
         *
         * @param mixed $mSearchParamForQuery query parameter
         * @param mixed $mInitialSearchCat initial category to seearch in
         * @param mixed $mInitialSearchVendor initial vendor to seearch for
         * @param mixed $mInitialSearchManufacturer initial Manufacturer to seearch for
         * @param mixed $mSortBy sort by
         * @return oxArticleList
         * @todo Remove the deleted items.
         */
        public function getSearchArticles($mSearchParamForQuery = false,  $mInitialSearchCat = false,
            $mInitialSearchVendor = false,  $mInitialSearchManufacturer = false,  $mSortBy = false)
        {
            $oList = null;

            if ($mSearchParamForQuery && $this->withWBLSEMFOXSearch()) {
                list($iLimit, $iOffset) = $this->getAndSetOffsetAndLimitForWBLSEMFOX();

                try {
                    $oHit = $this->getWBLSEMFOXWrapper()->products->get(array(
                        'query'  => $mSearchParamForQuery,
                        'offset' => $iOffset,
                        'limit'  => $iLimit
                    ));

                    static::setLastWBLSEMFOXHit($oHit);

                    if ($sQuery = $this->getWBLSEMFOXSearchSelect($oHit, $mSortBy)) {
                        /** @var oxArticleList $oList */
                        $oList = oxNew('oxarticlelist');

                        $oList->setSqlLimit($iOffset, $iLimit);
                        $oList->selectString($sQuery);
                    } // if
                } catch (SEMFOXException $oExc) {
                    /** @var WBLSEMFOX_Logger $oLogger */
                    $oLogger = class_exists('oxRegistry') ? oxRegistry::get('WBLSEMFOX_Logger') : oxNew('WBLSEMFOX_Logger');
                    $oLogger->logErrror($oExc);
                } // catch
            } // if

            if (!$oList) {
                $oList = parent::getSearchArticles(
                    $mSearchParamForQuery, $mInitialSearchCat, $mInitialSearchVendor, $mInitialSearchManufacturer, $mSortBy
                );
            } // if

            return $oList;
        } // function

        /**
         * Returns the amount of articles according to search parameters.
         *
         * @param mixed $mSearchParamForQuery       query parameter
         * @param mixed $mInitialSearchCat          initial category to seearch in
         * @param mixed $mInitialSearchVendor       initial vendor to seearch for
         * @param mixed $mInitialSearchManufacturer initial Manufacturer to seearch for
         * @return int
         */
        public function getSearchArticleCount($mSearchParamForQuery = false,  $mInitialSearchCat = false,
            $mInitialSearchVendor = false,  $mInitialSearchManufacturer = false)
        {
            if (($this->withWBLSEMFOXSearch()) && ($oHit = static::getLastWBLSEMFOXHit())) {
                $iReturn = (int) @ $oHit->resultsAvailable;
            } else {
                $iReturn = parent::getSearchArticleCount($mSearchParamForQuery, $mInitialSearchCat, $mInitialSearchVendor, $mInitialSearchManufacturer);
            } // else

            return $iReturn;
        } // function

        /**
         * Returns a select finding articles.
         * @param stdClass $oHit
         * @param string|void $mSQLSorting
         * @return string
         * @todo Complete refactoring of this method in a second step, because preventing this query is a primary target!
         */
        protected function getWBLSEMFOXSearchSelect(stdClass $oHit = null, $mSQLSorting = null)
        {
            if (!$oHit) {
                $oHit = static::getLastWBLSEMFOXHit();
            } // if

            $sReturn = '';

            if ($oHit->searchResults) {
                /** @var oxArticle $oArticle */
                $aNos     = array();
                $oArticle = oxNew('oxarticle');
                $oDb      = getDb();
                $sReturn  = $oArticle->buildSelectString() . ' AND ' . $oArticle->getSqlActiveSnippet();

                foreach ($oHit->searchResults as $aHits) {
                    $aHit = current($aHits);// TODO Config which of the hit?
                    $aNos[] = $oDb->quote($aHit->articleNumber);
                } // foreach

                $sReturn .= ' AND ' . $this->getConfig()->getConfigParam('sWBLSEMFOXIDField') . ' IN (' . implode(',', $aNos) . ')';

                if ($mSQLSorting) {
                    $sReturn .= 'ORDER BY ' . $mSQLSorting;
                } // if
            } // if

            return $sReturn;
        } // function

        /**
         * Lazy-Loads the SEMFOX-Wrapper.
         * @return \SEMFOX\Wrapper
         * @todo Setter!
         */
        protected function getWBLSEMFOXWrapper()
        {
            if (!$this->oWBLSEMFOXWrapper) {
                $oConfig = $this->getConfig();
                $this->oWBLSEMFOXWrapper = new Wrapper(array(
                    'apiKey' => $oConfig->getConfigParam('sWBLSEMFOXAPIKey'),
                    'customerId' => $oConfig->getConfigParam('sWBLSEMFOXCustomerId'),
                    'restPort' => $oConfig->getConfigParam('sWBLSEMFOXPort'),
                ));
            } // if

            return $this->oWBLSEMFOXWrapper;
        } // function

        /**
         * Sets the last SEMFOX hit.
         * @param stdClass $oHit
         * @return stdClass
         */
        static public function setLastWBLSEMFOXHit(stdClass $oHit)
        {
            return static::$oLastWBLSEMFOXHit = $oHit;
        } // function

        /**
         * Should SEMFOX be used with the search?
         * @param bool $bNewStatus The new status.*
         * @return bool The old status.
         */
        public function withWBLSEMFOXSearch($bNewStatus = true)
        {
            $bOldStatus = $this->bWithWBLSEMFOXSearch;

            if (func_num_args()) {
                $this->bWithWBLSEMFOXSearch = $bNewStatus;
            } // if

            return $bOldStatus;
        } // function
    } // class