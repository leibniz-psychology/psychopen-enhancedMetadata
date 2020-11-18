{**
 * plugins/generic/enhancedMetadata/submissionMetaData.tpl
 *
 * Copyright (c) 2015-2019 University of Pittsburgh
 * Copyright (c) 2014-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Copyright (c) 2020 Ronny Bölter, ZPID
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Enhanced Metadata Submission Form
 *
 *}
{if isset($hideFormElements)}
	<input type="hidden" value="{$hideFormElements|escape|trim}" class="hideFormElements">
{/if}
<link rel="stylesheet" type="text/css" href="{$enhMetaDataStyle}">
<script src="{$enhMetaDataScript}" type="text/javascript" defer></script>
{*{fbvFormSection title="plugins.generic.enhanced.metadata.submission.title" class="enhanced-metadata"}
	<p class="description">{translate key="plugins.generic.enhanced.metadata.submission.description"}</p>
{/fbvFormSection}*}
{fbvFormArea class="enhanced-metadata-form"}
{foreach from=$enhFormFields item=$itm}
    {if $itm['type']=='text' || $itm['type']=='textarea'}
        {include file="`$tmplRes`:components/textfield.tpl"}
    {elseif $itm['type']=='radio' || $itm['type']=='checkbox'}
        {include file="`$tmplRes`:components/checkRadio.tpl"}
    {elseif $itm['type']=='headline'}
        {include file="`$tmplRes`:components/headline.tpl"}
    {/if}
{/foreach}
{/fbvFormArea}
