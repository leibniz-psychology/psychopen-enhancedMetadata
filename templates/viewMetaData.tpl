{**
 * plugins/generic/orcidProfile/orcidProfile.tpl
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
<div class="pkp_notification">
    {foreach from=$enhViewArray item=$itm}
		<div id="pkp_notification_enhanced_metadata" class="notify{$itm['type']|trim|escape|ucfirst}">
			<span class="title">{$itm['title'][$currentLocale]|escape}</span>
            {if $itm['list']}
				<ul class="description">
                    {foreach from=$itm['content'] item=$data}
						<li>{$data|html_entity_decode}</li>
                    {/foreach}
				</ul>
            {else}
				<span class="description">
					{foreach from=$itm['content'] item=$data}
                        {$data|html_entity_decode}<br />
                    {/foreach}
				</span>
            {/if}
		</div>
    {/foreach}
</div>