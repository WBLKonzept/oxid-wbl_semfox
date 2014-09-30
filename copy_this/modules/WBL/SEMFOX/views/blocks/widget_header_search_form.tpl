[{$smarty.block.parent}]
[{if $oView->showSearch()}]
	[{assign var='sModuleCSS' value=$oViewConf->getModuleUrl('WBL_SEMFOX', 'out/src/css/unibox.min.css')}]
	[{assign var='sModuleJS' value=$oViewConf->getModuleUrl('WBL_SEMFOX', 'out/src/js/unibox.min.js')}]

	[{oxstyle include=$sModuleCSS}]
	[{oxscript include=$sModuleJS}]

	[{capture name='sSEMFOXSuggestJS'}]
		var settings = {
			suggestUrl                : 'http://semfox.com:[{$oConfig->getConfigParam('sWBLSEMFOXPort')}]/queries/suggest?apiKey=apiKey=[{$oConfig->getConfigParam('sWBLSEMFOXAPIKey')}]&customerId=[{$oConfig->getConfigParam('sWBLSEMFOXCustomerId')}]&query=',
			queryVisualizationHeadline: 'Ihre Suche Visualisiert',
			enterCallback             : function (text, link) {
				console.log(text);
			},
			instantVisualFeedback     : 'none',
			highlight                 : true,
			extraHtml                 : '##price## | ' +
				'Stück: <form action="http://emmasenkel.de/warenkorb.php" style="display: inline-block;">' +
				'<input type="hidden" name="pid" value="##articleNumber##">' +
				'<input type="number" name="quantity" min="1" max="99" value="1">' +
				'<input type="submit" value="in den Warenkorb"></form> | ' +
				'<a href="http://emmasenkel.de/merkzettel?product=##articleNumber##">Auf den Merkzettel</a>',
			throttleTime              : [{$oConfig->getConfigParam('sWBLSEMFOXSuggestThrottleTime')}]
		};

		$('#searchParam').unibox(settings);
	[{/capture}]
	[{oxscript add=$smarty.capture.sSEMFOXSuggestJS}]
[{/if}]