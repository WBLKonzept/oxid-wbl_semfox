[{$smarty.block.parent}]
[{if $oView->showSearch()}]
	[{assign var='sModuleCSS' value=$oViewConf->getModuleUrl('WBL_SEMFOX', 'out/src/css/unibox.min.css')}]
	[{assign var='sModuleJS' value=$oViewConf->getModuleUrl('WBL_SEMFOX', 'out/src/js/unibox.min.js')}]

	[{oxstyle include=$sModuleCSS}]
	[{oxscript include=$sModuleJS}]
[{/if}]