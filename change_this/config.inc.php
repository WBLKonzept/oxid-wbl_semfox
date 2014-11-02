<?php
    $this->bWBLSEMFOXWithLogging  = true;
    $this->sWBLSEMFOXAPIKey       = 'TODO';
    $this->sWBLSEMFOXCustomerId   = 'TODO';
    $tihs->sWBLSEMFOXPort         = '8585';
    $this->sWBLSEMFOXIDField      = 'oxartnum';
    $this->aWBLSEMFOXFieldMapping = array(
        'getMainCatNameForWBLSEMFOX()' => 'category',
        'getPictureUrl()'              => 'image',
        'oxartnum'                     => 'articleNumber',
        'oxean'                        => 'ean',
        'oxtitle'                      => 'name',
    );