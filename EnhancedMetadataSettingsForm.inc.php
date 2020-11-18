<?php

import('lib.pkp.classes.form.Form');
import('classes.notification.NotificationManager');

class EnhancedMetadataSettingsForm extends Form
{
	public $plugin;
	private $folderPath;

	/**
	 * EnhancedMetadataSettingsForm constructor.
	 * @param $plugin
	 */
	public function __construct($plugin)
	{
		parent::__construct($plugin->getTemplateResource('settings.tpl'));
		$contextId = Application::getRequest()->getContext()->getId();
		$this->plugin = $plugin;
		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));
		$this->folderPath = Config::getVar('files', 'public_files_dir') . '/journals/' . $contextId . '/enhancedForms';
	}

	/**
	 *
	 */
	public function initData()
	{
		$settings = $this->plugin->getSetting(Application::getRequest()->getContext()->getId(), 'settings');
		if (isset($settings)) {
			$settings = json_decode($settings, true);
			$this->setData('emFiles', $settings);
		}
		parent::initData();
	}

	/**
	 *
	 */
	public function readInputData()
	{
		$this->readUserVars(['emFile']);
		parent::readInputData();
	}

	/**
	 * @param PKPRequest $request
	 * @param null $template
	 * @param bool $display
	 * @return string|null
	 */
	public function fetch($request, $template = null, $display = false)
	{
		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('pluginName', $this->plugin->getName());
		return parent::fetch($request, $template, $display);
	}

	/**
	 * @param mixed ...$args
	 * @return mixed|null
	 */
	public function execute(...$args)
	{
		$contextId = Application::getRequest()->getContext()->getId();
		$notificationMgr = new NotificationManager();
		$fileManager = new PublicFileManager();
		$jsonString = $this->getData('emFile');
		$jsonObj = json_decode($jsonString, true);
		if (isset($jsonObj)) {
			if (!$fileManager->fileExists($this->folderPath))
				$fileManager->mkdir($this->folderPath);
			$filepath = $this->folderPath . '/enh_' . $jsonObj['form'] . '_' . $jsonObj['version'] . '.json';
			if ($fileManager->fileExists($filepath)) {
				$notificationMgr->createTrivialNotification(
					Application::getRequest()->getUser()->getId(),
					NOTIFICATION_TYPE_ERROR,
					['contents' => __('plugins.generic.enhanced.metadata.settings.upload.error')]
				);
			} else {
				$fileManager->writeFile($filepath, $jsonString);
				$settings = $this->plugin->getSetting($contextId, 'settings');
				if (isset($settings)) {
					$settings = json_decode($settings, true);
				}
				$settings[$jsonObj['form']][$jsonObj['version']] = $filepath;
				$this->plugin->updateSetting($contextId, 'settings', json_encode($settings));
				$notificationMgr->createTrivialNotification(
					Application::getRequest()->getUser()->getId(),
					NOTIFICATION_TYPE_SUCCESS,
					['contents' => __('common.changesSaved')]
				);
			}
		}
		return parent::execute();
	}
}