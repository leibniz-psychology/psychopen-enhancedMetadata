<script>
	$(function () {ldelim}
		$('#enhancedMetadataSettings').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
        {rdelim});

	document.querySelectorAll('.checkNum').forEach(function (el) {ldelim}
		el.addEventListener("input", elem => el.value = (isNaN(el.value)) ? el.value.replace(elem.data, '') : el.value);
        {rdelim})
</script>
<form class="pkp_form"
      id="enhancedMetadataSettings"
      method="POST"
      action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}">
    {csrf}
    {fbvFormArea}
		<div class="section">
			<span class="label">{translate key="plugins.generic.enhanced.metadata.settings.title"}</span>
			<span class="description">{translate key="plugins.generic.enhanced.metadata.settings.desc"}</span>
            {if isset($emFiles)}
				<div class="pkp_notification">
					<div id="pkp_notification_enhanced_metadata" class="notifyInfo">
						<span class="title">{translate key="plugins.generic.enhanced.metadata.settings.files"}</span>
						<ul class="description">
                            {foreach from=$emFiles key=k item=schema}
								<li>{$k|ucfirst}
									<ul>
                                        {foreach from=$schema key=version item=url}
											<li>{translate key="plugins.generic.enhanced.metadata.settings.version"}: {$version} -
												<a href="{$baseUrl}/{$url}" target="_blank" download>{translate key="common.download"}</a>
											</li>
                                        {/foreach}
									</ul>
								</li>
                            {/foreach}
						</ul>
					</div>
				</div>
            {/if}
		</div>
    {fbvFormSection title="plugins.generic.enhanced.metadata.settings.upload.title"}
    {fbvElement type="textarea" id="emFile"  value=$emFile label="plugins.generic.enhanced.metadata.settings.upload.desc"}
    {/fbvFormSection}
    {/fbvFormArea}
    {fbvFormButtons submitText="common.save"}
</form>