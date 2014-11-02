CREATE TABLE oxtplblocks_semfox_bkp SELECT * FROM oxtplblocks;
DELETE FROM oxtplblocks WHERE oxblockname = 'widget_header_search_form';
INSERT INTO oxtplblocks (OXID, OXACTIVE, OXSHOPID, OXTEMPLATE, OXBLOCKNAME, OXPOS, OXFILE, OXMODULE) VALUES ('wblsemfoxsearchform', '1', '1', 'widget/header/search.tpl', 'widget_header_search_form', '1', 'widget_header_search_form', 'WBL/SEMFOX');