<?php
    namespace SEMFOX\Transport;

    /**
     * The basic API for a transport class.
     * @author     blange <code@wbl-konzept.de>
     * @cateogry   vendor
     * @package    SEMFOX
     * @subpackage Transport
     * @version    $id$
     */
    interface TransportInterface
    {
        const TYPE_DELETE = 'DELETE';

        const TYPE_GET = 'GET';

        const TYPE_PUT = 'PUT';

        const TYPE_POST = 'POST';

        public function processRequest(array $aArguments = array()); // function
    } // interface