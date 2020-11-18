<?php
/* TODO remove noinspection*/
/** @noinspection PhpUnusedParameterInspection */
/** @noinspection PhpUnused */
import('lib.pkp.classes.plugins.GenericPlugin');
import('lib.pkp.classes.form.validation.FormValidatorLength');


/**
 * Class EnhancedMetadataPlugin
 *
 * To make this work in 3.1.2.x add parent::execute(); in execute function(L137) of \lib\pkp\controllers\wizard\fileUpload\form\SubmissionFilesMetadataForm.inc.php
 */
class EnhancedMetadataPlugin extends GenericPlugin
{

	private $emDataService;

	/**
	 * EnhancedMetadataPlugin constructor.
	 */
	public function __construct()
	{
		import('plugins.generic.enhancedMetadata.classes.EMDataService');
		$this->emDataService = new EMDataService();
		parent::__construct();
	}


	/**
	 * @return string plugin name
	 */
	public function getDisplayName()
	{
		return __('plugins.generic.enhanced.metadata.title');
	}

	/**
	 * @return string plugin description
	 */
	public function getDescription()
	{
		return __('plugins.generic.enhanced.metadata.desc');
	}

	// TODO remove no ispection

	/** @noinspection PhpParamsInspection */
	public function register($category, $path, $mainContextId = NULL)
	{
		$success = parent::register($category, $path);
		$request = PKPApplication::getRequest();
		$context = $request->getContext();
		$user = $request->getUser();
		if(isset($user) && isset($context)) {
			$accessViaRole = $user->hasRole(array(ROLE_ID_AUTHOR, ROLE_ID_MANAGER), $context->getId());
			if ($success && $this->getEnabled() && $accessViaRole) {
				// Add metadata fields to submission
				HookRegistry::register('Templates::Submission::SubmissionMetadataForm::AdditionalMetadata', array($this, 'metadataFormDisplay'));
				HookRegistry::register('supplementaryfilemetadataform::display', array($this, 'metadataFormDisplay'));
				HookRegistry::register('authorform::display', array($this, 'metadataFormDisplay'));
				// Hook for initData
				HookRegistry::register('submissionsubmitstep3form::initdata', array($this, 'metadataFormInit'));
				HookRegistry::register('issueentrysubmissionreviewform::initdata', array($this, 'metadataFormInit'));
				HookRegistry::register('quicksubmitform::initdata', array($this, 'metadataFormInit'));
				HookRegistry::register('authorform::initdata', array($this, 'metadataFormInit'));
				// Hook for readUserVars
				HookRegistry::register('submissionsubmitstep3form::readuservars', array($this, 'addUserVars'));
				HookRegistry::register('issueentrysubmissionreviewform::readuservars', array($this, 'addUserVars'));
				HookRegistry::register('quicksubmitform::readuservars', array($this, 'addUserVars'));
				HookRegistry::register('authorform::readuservars', array($this, 'addUserVars'));
				HookRegistry::register('supplementaryfilemetadataform::readuservars', array($this, 'addUserVars'));
				// Hook for form validation
				// TODO Validate for all forms
				HookRegistry::register('submissionsubmitstep3form::validate', array($this, 'submissionMetadataValidate'));
				// Hook for form execute
				HookRegistry::register('submissionsubmitstep3form::execute', array($this, 'metadataFormExecute'));
				HookRegistry::register('issueentrysubmissionreviewform::execute', array($this, 'metadataFormExecute'));
				HookRegistry::register('quicksubmitform::execute', array($this, 'metadataFormExecute'));
				HookRegistry::register('authorform::execute', array($this, 'metadataFormExecute'));
				HookRegistry::register('supplementaryfilemetadataform::execute', array($this, 'metadataFormExecute'));
				// Hook for save into db
				HookRegistry::register('articledao::getAdditionalFieldNames', array($this, 'addAdditionalFieldNames'));
				HookRegistry::register('authordao::getAdditionalFieldNames', array($this, 'addAdditionalFieldNames'));
				HookRegistry::register('supplementaryfiledaodelegate::getLocaleFieldNames', array($this, 'addAdditionalFieldNames'));
				// View Hooks
				// Submission Add Reviewer
				HookRegistry::register('advancedsearchreviewerform::display', array($this, 'metadataFormDisplay'));
			}
		}
		return $success;
	}

	/**
	 * @param $hookName
	 * @param $params
	 * @return bool
	 * @throws SmartyException
	 */
	function metadataFormDisplay($hookName, $params)
	{
		$request = PKPApplication::getRequest();
		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('enhMetaDataStyle', $request->getBaseUrl() . '/' . $this->getPluginPath() . '/style/enhancedMetadata.css');
		$templateMgr->assign('enhMetaDataScript', $request->getBaseUrl() . '/' . $this->getPluginPath() . '/js/enhancedMetadata.js');
		$templateMgr->assign("tmplRes", $this->getTemplateResource());
		/*
		 * TODO doesn't work
		 * templateMgr->addStyleSheet('enhMetaDataStyle',$request->getBaseUrl() . '/' . $this->getPluginPath() . '/style/enhancedMetadata.css');
		 * $templateMgr->addJavaScript('enhMetadata',$request->getBaseUrl() . '/' . $this->getPluginPath() . '/js/enhancedMetadata.js', ['inline' => true]);
		*/
		$form =& $params[0];
		if (!is_array($form)) {
			switch (get_class($form)) {
				case 'SupplementaryFileMetadataForm':
					$this->metadataFormInit($hookName, $params);
					$templateMgr->registerFilter("output", array($this, 'addViewFilter'));
					break;
				case "AuthorForm":
					$templateMgr->registerFilter("output", array($this, 'addViewFilter'));
					break;
				case 'AdvancedSearchReviewerForm':
					error_log('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
					$submission = $form->getSubmission();
					$jsonSchema = $this->emDataService->getJsonScheme('submission', $this);
					$jsonData = $submission->getData('enh_' . $jsonSchema['form'] . '_' . $jsonSchema['version']);
					if (isset($jsonData))
						$jsonData = json_decode($jsonData, true);
					$viewArray = [];
					if ($jsonSchema && isset($jsonSchema['items']) && isset($jsonData)) {
						foreach ($jsonSchema['items'] as $item) {
							if (isset($item['viewForms'])) {
								$content = $this->emDataService->getFieldValues($item, $jsonData);
								foreach ($item['viewForms'] as $formItem) {
									if ($formItem['form'] && $formItem['form'] == 'AdvancedSearchReviewerForm') {
										$viewArray[] = [
											'title' => $formItem['title'],
											'type' => $formItem['notifyType'],
											'list' => $formItem['list'],
											'content' => $content];
									}
								}
							}
						}
					}
					$form->setData('enhViewArray', $viewArray);
					$templateMgr->registerFilter("output", array($this, 'addViewFilter'));
					break;
			}
		} else {
			$smarty =& $params[1];
			$output =& $params[2];
			$output .= $smarty->fetch($this->getTemplateResource('submissionMetaData.tpl'));
		}
		return false;
	}

	/**
	 * @param $hookName
	 * @param $params
	 * @return bool
	 */
	function metadataFormInit($hookName, $params)
	{
		$form =& $params[0];
		$formObject = null;
		$jsonSchema = null;
		$this->getFormObjectAndJSON($form, $formObject, $jsonSchema);
		if (isset($jsonSchema) && isset($jsonSchema['items'])) {
			$form->setData('enhFormFields', $jsonSchema['items']);
			$version = $jsonSchema['version'];
			if (isset($formObject) && $version && intval($version) && $version > 0) {
				$json = null;
				do {
					$json = $formObject->getData('enh_' . $jsonSchema['form'] . '_' . $version--);
				} while ($json == null && $version > 0);
				if ($json) {
					$form->setData('enhMetaDataJson', json_decode($json, true));
				}
			}
			if (get_class($form) == "AuthorForm" && isset($jsonSchema['hideOJSDefaultRoles']) && $jsonSchema['hideOJSDefaultRoles'] == true) {
				$form->setData('hideFormElements', json_encode(["userGroupId"]));
			}
		}
		return false;
	}

	/**
	 * @param $hookName
	 * @param $params
	 * @return bool
	 */
	function addUserVars($hookName, $params)
	{
		$form =& $params[0];
		$userVars =& $params[1];
		$jsonSchema = null;
		$this->getFormObjectAndJSON($form, $formObject, $jsonSchema);
		if (isset($jsonSchema) && isset($jsonSchema['items'])) {
			$names = $this->emDataService->getNameParam($jsonSchema['items']);
			$this->addChecks($form, $jsonSchema['items']);
			foreach ($names as $name)
				$userVars[] = $name;
		}
		return false;
	}

	function submissionMetadataValidate($hookName, $params)
	{
		$form =& $params[0];
		$jsonSchema = $this->emDataService->getJsonScheme('submission', $this);
		if ($jsonSchema && $jsonSchema['items']) {
			$names = $this->emDataService->getNameParam($jsonSchema['items']);
			$enhData = [];
			foreach ($names as $name) {
				$enhData[$name] = $form->getData($name);
			}
			$form->setData('enhMetaDataJson', $enhData);
			$form->setData('enhFormFields', $jsonSchema['items']);
		}
	}

	/**
	 * @param $hookName
	 * @param $params
	 * @return bool
	 */
	function metadataFormExecute($hookName, $params)
	{
		$form =& $params[0];
		$formObject = null;
		$jsonSchema = null;
		$this->getFormObjectAndJSON($form, $formObject, $jsonSchema);
		if (isset($formObject) && isset($jsonSchema) && isset($jsonSchema['items'])) {
			$enhData = [];
			$names = $this->emDataService->getNameParam($jsonSchema['items']);
			foreach ($names as $name) {
				$enhData[$name] = $form->getData($name);
			}
			$formObject->setData('enh_' . $jsonSchema['form'] . '_' . $jsonSchema['version'], json_encode($enhData));
		}
		return false;
	}

	/**
	 * @param $hookName
	 * @param $params
	 * @return bool
	 */
	function addAdditionalFieldNames($hookName, $params)
	{
		$form =& $params[0];
		$jsonSchema = null;
		switch (get_class($form)) {
			case 'ArticleDAO':
				$jsonSchema = $this->emDataService->getJsonScheme('submission', $this);
				break;
			case 'AuthorDAO':
				$jsonSchema = $this->emDataService->getJsonScheme('author', $this);
				break;
			case 'SupplementaryFileDAODelegate':
				$jsonSchema = $this->emDataService->getJsonScheme('supplementary', $this);
				break;
		}
		if (isset($jsonSchema)) {
			$fields =& $params[1];
			$fields[] = 'enh_' . $jsonSchema['form'] . '_' . $jsonSchema['version'];
		}
		return false;
	}


	function addViewFilter($output, $templateMgr)
	{
		if (preg_match('/<div id="advancedReviewerSearch" class="pkp_form pkp_form_advancedReviewerSearch">/', $output, $matches, PREG_OFFSET_CAPTURE)) {
			$match = $matches[0][0];
			$offset = $matches[0][1];
			$newOutput = substr($output, 0, $offset + strlen($match));
			$newOutput .= $templateMgr->fetch($this->getTemplateResource('viewMetaData.tpl'));
			$newOutput .= substr($output, $offset + strlen($match));
			$output = $newOutput;
			$templateMgr->unregisterFilter('output', array($this, 'addViewFilter'));
		} else if (preg_match('/<fieldset\s*id="\s*fileMetaData\s*"\s*>/', $output, $matches, PREG_OFFSET_CAPTURE)) {
			$match = $matches[0][0];
			$offset = $matches[0][1];
			$newOutput = substr($output, 0, $offset);
			$newOutput .= $templateMgr->fetch($this->getTemplateResource('submissionMetaData.tpl'));
			$newOutput .= substr($output, $offset);
			$output = $newOutput;
			$templateMgr->unregisterFilter('output', array($this, 'addViewFilter'));
		} else if (preg_match('/<p><span class="formRequired">/', $output, $matches, PREG_OFFSET_CAPTURE)) {
			$match = $matches[0][0];
			$offset = $matches[0][1];
			$newOutput = substr($output, 0, $offset);
			$newOutput .= $templateMgr->fetch($this->getTemplateResource('submissionMetaData.tpl'));
			$newOutput .= substr($output, $offset);
			$output = $newOutput;
			$templateMgr->unregisterFilter('output', array($this, 'addViewFilter'));
		}
		return $output;
	}


	function addChecks($form, $data)
	{
		foreach ($data as $itm) {
			if ($itm['fields'])
				foreach ($itm['fields'] as $field)
					switch ($itm['type']) {
						case 'text':
						case 'textarea';
							/* TODO Message */
							$form->addCheck(new FormValidatorLength($form, $field['name'] . '[en_US]', 'optional', 'user.register.form.passwordLengthRestriction', '<=', $field['maxLength']));
							break;
					}

		}
	}


	/**
	 * Add settings button to plugin
	 * @param $request
	 * @param array $verb
	 * @return array
	 */
	public function getActions($request, $verb)
	{
		$router = $request->getRouter();
		import('lib.pkp.classes.linkAction.request.AjaxModal');
		return array_merge(
			$this->getEnabled() ? array(
				new LinkAction(
					'settings',
					new AjaxModal(
						$router->url($request, null, null, 'manage', null, array('verb' => 'settings', 'plugin' => $this->getName(), 'category' => 'generic')),
						$this->getDisplayName()
					),
					__('manager.plugins.settings'),
					null
				),
			) : array(),
			parent::getActions($request, $verb)
		);
	}

	/**
	 * Manage Settings
	 * @param array $args
	 * @param PKPRequest $request
	 * @return JSONMessage
	 */
	public function manage($args, $request)
	{
		switch ($request->getUserVar('verb')) {
			case 'settings':
				$this->import('EnhancedMetadataSettingsForm');
				$form = new EnhancedMetadataSettingsForm($this);
				if (!$request->getUserVar('save')) {
					$form->initData();
					return new JSONMessage(true, $form->fetch($request));
				}
				$form->readInputData();
				if ($form->validate()) {
					$form->execute();
					return new JSONMessage(true);
				}
		}
		return parent::manage($args, $request);
	}

	/**
	 * @param $form
	 * @param $formObject
	 * @param $jsonSchema
	 */
	private function getFormObjectAndJSON($form, &$formObject, &$jsonSchema): void
	{
		if (isset($form))
			switch (get_class($form)) {
				case 'SubmissionSubmitStep3Form':
				case 'QuickSubmitForm':
					$formObject = $form->submission;
					$jsonSchema = $this->emDataService->getJsonScheme('submission', $this);
					break;
				case 'IssueEntrySubmissionReviewForm':
					$formObject = $form->getSubmission();
					$jsonSchema = $this->emDataService->getJsonScheme('submission', $this);
					break;
				case 'SupplementaryFileMetadataForm':
					$formObject = $form->getSubmissionFile();
					$jsonSchema = $this->emDataService->getJsonScheme('supplement', $this);
					break;
				case "AuthorForm":
					$formObject = $form->getAuthor();
					$jsonSchema = $this->emDataService->getJsonScheme('author', $this);
					break;
			}
	}

}


