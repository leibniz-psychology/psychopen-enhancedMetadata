<?php


class EMDataService
{

	function getJsonScheme($name, $plugin)
	{
		$settings = $plugin->getSetting(Application::getRequest()->getContext()->getId(), 'settings');
		if (isset($settings))
			$settings = json_decode($settings, true);
		if (isset($settings)) {
			$versions = $settings[$name];
			if (isset($versions)) {
				$latest = $versions[max(array_keys($versions))];
				$fileManager = new PublicFileManager();
				if ($fileManager->fileExists($latest)) {
					$file = $fileManager->readFileFromPath($latest);
					return json_decode($file, true);
				}
			}
		}
		return null;
	}


	function getFieldValues($node, $data)
	{
		$res = [];
		switch ($node['type']) {
			case 'select':
			case 'radio':
				$res = [$data[$node['name']]];
				break;
			default:
				if (isset($node['fields']))
					foreach ($node['fields'] as $field)
						$res = array_merge($res, explode(PHP_EOL, $data[$field['name']]));
		}
		return $res;
	}

	function getNameParam($data)
	{
		$res = [];
		foreach ($data as $itm) {
			switch ($itm['type']) {
				case 'select':
				case 'radio':
					$res[] = $itm['name'];
					break;
				default:
					if (isset($itm['fields']))
						foreach ($itm['fields'] as $field)
							$res[] = $field['name'];
			}
		}
		return array_unique($res);
	}

}