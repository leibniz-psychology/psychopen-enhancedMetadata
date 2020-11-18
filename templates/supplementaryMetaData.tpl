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

{fbvFormSection title="plugins.generic.enhanced.metadata.supplementary.title" class="enhanced-metadata"}
	<p class="description">{translate key="plugins.generic.enhanced.metadata.supplementary.description"}</p>
{/fbvFormSection}
{fbvFormArea id="enhanced-metadata-form"}
	{fbvFormSection title="plugins.generic.enhanced.metadata.supplementary.test" for="enhTest2"}
		{fbvElement type="text" multilingual=true id="enhTest2" name="enhTest2" value=$enhTest2}
	{/fbvFormSection}
{/fbvFormArea}