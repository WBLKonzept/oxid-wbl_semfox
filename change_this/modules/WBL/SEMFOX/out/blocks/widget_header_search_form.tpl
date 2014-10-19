[{$smarty.block.parent}]

[{if $oView->showSearch()}]
	[{assign var='oConfig'        value=$oViewConf->getConfig()}]
	[{assign var='sModuleCSS'     value=$oViewConf->getBaseDir()|cat:'/modules/WBL/SEMFOX/out/src/css/unibox.min.css'}]
	[{assign var='sModuleJS'      value=$oViewConf->getBaseDir()|cat:'/modules/WBL/SEMFOX/out/src/js/unibox.min.js'}]
	[{assign var='sEnterCallback' value=$oConfig->getConfigParam('sWBLSEMFOXSuggestEnterCallback')}]

	[{oxstyle include=$sModuleCSS}]
	[{oxscript include=$sModuleJS}]

	[{capture name='sSEMFOXSuggestJS'}]
		[{include assign='sSEMFOXExtraHTML' file="custom/semfox-suggest.tpl"}]

		var settings = {
			[{* Is there template output and does it not contain the template name itself indicating that the template is missing *}]
			[{if $sSEMFOXExtraHTML && $sSEMFOXExtraHTML|strpos:'custom/semfox-suggest.tpl' === false}]
				[{* You should prevent the simple ' in your HTML Codes! *}]
				extraHtml : '[{$sSEMFOXExtraHTML|strip|replace:"'":"\'"}]',
			[{/if}]
			suggestUrl                : '[{$oViewConf->getBaseDir()}]/modules/WBL/SEMFOX/suggest-proxy.php?query=',
			queryVisualizationHeadline: '[{$oConfig->getConfigParam('sWBLSEMFOXQueryVisualizationHeadline')|default:"Ihre Suche Visualisiert"}]',
			enterCallback             : function (text, link) {
				[{if $sEnterCallback}]
					[{$sEnterCallback}]
				[{else}]
					if (link) {
						window.location = link;
					} else {
						$("#searchParam").closest("form").trigger("submit");
					}
				[{/if}]
			},
			instantVisualFeedback     : '[{$oConfig->getConfigParam('sWBLSEMFOXQueryInstantFeedbackPos')|default:"none"}]',
			highlight                 : [{if $oConfig->getConfigParam('bWBLSEMFOXHighlight')}]true[{else}]false[{/if}],
			throttleTime              : [{$oConfig->getConfigParam('sWBLSEMFOXSuggestThrottleTime')|default:50}]
		};

		$('#searchParam').unibox(settings);
	[{/capture}]
	[{oxscript add=$smarty.capture.sSEMFOXSuggestJS}]
[{/if}]