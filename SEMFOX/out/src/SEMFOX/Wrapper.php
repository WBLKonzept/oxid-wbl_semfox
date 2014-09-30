<?php
    namespace SEMFOX;

    use SEMFOX\Transport\HTTP\File as HTTPFile,
        SEMFOX\Transport\TransportInterface;

    /**
     * Basic wrapper for connecting to SEMFOX.
     * @author   blange <code@wbl-konzept.de>
     * @cateogry vendor
     * @package  SEMFOX
     * @version  $id$
     */
    class Wrapper
    {
        protected $aConfig = array();

        protected $aRequestedPath = '';

        protected $aTransportTypes = array();

        public function __construct(array $aConfig = array())
        {
            $this->aConfig = $aConfig;
        } // function

        public function __call($sName, array $aArgs = array())
        {
            /** @var \Request $oRequestType */
            $aRequestArguments = array();
            $oRequestType      = new Request($this->getConfig());

            $oRequestType->setTransport($this->getTransportType(strtoupper($sName)));

            if (is_string($aArgs[0])) {
                $aRequestArguments['path'] = array_shift($aArgs);
            } else {
                $aRequestArguments['path'] = $this->aRequestedPath;
            }

            return $oRequestType(array_merge($aArgs[0], $aRequestArguments));
        } // function

        public function __get($sName)
        {
            $oReturn = clone $this;

            $oReturn->aRequestedPath[] = $sName;

            return $oReturn;
        } // function

        protected function getConfig()
        {
            return $this->aConfig;
        } // function

        protected function getTransportType($sType)
        {
            if (!(($oReturn = @$this->aTransportTypes[$sType]) instanceof TransportInterface)) {
                $this->aTransportTypes[$sType] = new HTTPFile($this->getConfig());

                $this->aTransportTypes[$sType]->setType($sType);
            } // if

            return $this->aTransportTypes[$sType];
        } // function

        public function setTransportType(TransportInterface $oTransport, $sType)
        {
            $this->aTransportTypes[$sType] = $oTransport;

            return $this;
        } // function
    } // class