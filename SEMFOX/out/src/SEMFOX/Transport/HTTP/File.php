<?php
    namespace SEMFOX\Transport\HTTP;

    use SEMFOX\Transport\TransportAbstract,
        SEMFOX\Transport\Exception as TransportException;

    /**
     * Transport class for opening urls with the fopen-wrapper.
     * @author     blange <code@wbl-konzept.de>
     * @cateogry   vendor
     * @package    SEMFOX
     * @subpackage Transport\HTTP
     * @version    $id$
     */
    class File extends TransportAbstract
    {
        protected function createRequestContext(array $aRequestArgs = array())
        {
            $aOptions = array(
                'http' => array(
                    'method'  => $sMethod = $this->getType(),
                    'timeout' => (int) $this->getConfigValue('requestTimeout', 15)
                )
            );

            if ($sMethod === self::TYPE_POST || $sMethod === self::TYPE_PUT) {
                $sRequestContent = json_encode($aRequestArgs);
                $aRequestArgs    = array();

                $aOptions['http']['header'] =
                    "Content-type: application/json\r\n" .
                    'Content-Length: ' . strlen($sRequestContent) . "\r\n";

                $aOptions['http']['content'] = $sRequestContent;
            } // if

            // TODO: SSL Validation, Timeout.

            $aNeededValues = array('apiKey', 'customerId');

            foreach ($aNeededValues as $sName) {
                if (!$mValue = $this->getConfigValue($sName, '')) {
                    throw new TransportException('Missing config value: ' . $sName);
                } // if

                $aRequestArgs[$sName] = $mValue;
            } // foreach

            return array(stream_context_create($aOptions), $aRequestArgs);
        } // function

        public function processRequest(array $aArguments = null)
        {
            $aPath = $aArguments['path'];
            unset($aArguments['path']);

            list($rContext, $aArguments) = $this->createRequestContext($aArguments);

            $sReturn = @file_get_contents(
                'http://semfox.com:' . $this->getConfigValue('restPort', '8585') . '/' . implode('/', $aPath) . '?' .
                http_build_query($aArguments),
                false,
                $rContext
            );

            if (!$sReturn) {
                throw new TransportException('Last Request did not return output.'); // TODO new name!
            } // if

            return $sReturn;
        } // function
    } // class