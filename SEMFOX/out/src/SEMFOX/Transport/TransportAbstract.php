<?php
    namespace SEMFOX\Transport;

    /**
     * Abstract fullfilling the basic API.
     * @author     blange <code@wbl-konzept.de>
     * @cateogry   vendor
     * @package    SEMFOX
     * @subpackage Transport
     * @version    $id$
     */
    abstract class TransportAbstract implements TransportInterface
    {
        protected $aConfig = array();

        public function __construct(array $aConfig = array())
        {
            $this->aConfig = $aConfig;
        } // function

        protected function getConfig()
        {
            return $this->aConfig;
        } // function

        /**
         * Returns the config value with the given name or the default value.
         * @param string $sName    The config name.
         * @param mixed  $mDefault The default value.
         * @return mixed
         */
        protected function getConfigValue($sName, $mDefault = null)
        {
            return @$this->aConfig[$sName] ?: $mDefault;
        } // function

        public function getType()
        {
            return $this->sType;
        } // function

        public function setType($sType)
        {
            $this->sType = $sType;

            return $this;
        } // function
    } // class