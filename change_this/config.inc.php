<?php
    $this->bNoWBLSEMFOXCSS               = false;
    $this->bWBLSEMFOXWithLogging         = true;
    $this->sWBLSEMFOXAPIKey              = 'TODO';
    $this->sWBLSEMFOXCustomerId          = 'TODO';
    $this->sWBLSEMFOXIDField             = 'oxartnum';
    $this->sWBLSEMFOXSuggestArticleLimit = 8;
    $this->sWBLSEMFOXSuggestSearchLimit  = 5;
    $this->aWBLSEMFOXFieldMapping        = array(
        'getMainCatNameForWBLSEMFOX()' => 'category',
        'getPictureUrl()'              => 'image',
        'oxartnum'                     => 'articleNumber',
        'oxean'                        => 'ean',
        'oxtitle'                      => 'name',
    );