<?php
class OneLoginEmbeddedApi {
	// Get this from https://app.onelogin.com/embedding
	const SECURITY_TOKEN = "";

	private function _curl_xml($endpoint) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$endpoint);
		curl_setopt($ch, CURLOPT_FAILONERROR,1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		$xml = curl_exec($ch);			 
		curl_close($ch);
		return $xml;
	}

	private function _get_endpoint($email) {
		$endpoint = 'https://app.onelogin.com/client/apps/embed2';
		$endpoint .= '?token=' . self::SECURITY_TOKEN;
		$endpoint .= '&email=' . $email;
		return $endpoint;
	}

	public function get_apps_for_user($email) {
		$endpoint = $this->_get_endpoint($email);
		$xml = $this->_curl_xml($endpoint);

		return new OneLoginApps($xml);
	}
}

class OneLoginApps {
	public $apps = array();

	public function __construct($xml) {
		$sxmle = new SimpleXMLElement($xml);

		foreach($sxmle as $appSXMLE){
			array_push($this->apps, new OneLoginApp($appSXMLE));
		}
	} 

	public function toHtml($apps_template, $app_template) {
		$template_parts = explode("{{apps}}", $apps_template);
		$html = $template_parts[0];
		foreach ($this->apps as $app) {
			$html .= $app->toHtml($app_template);
		}
		$html .= $template_parts[1];

		return $html;
	}
}

class OneLoginApp {
	public $id;
	public $icon;
	public $name;
	public $provisioned;
	public $extension_required;

	public function __construct($sxmle) {
		$this->id = (string)$sxmle->id;
		$this->icon = (string)$sxmle->icon;
		$this->name = (string)$sxmle->name;
		$this->provisioned = (boolean)$sxmle->provisioned;
		$this->extension_required = (boolean)$sxmle->extension_required;
	}

	public function toHtml($template) {
		$html = str_replace("{{id}}", $this->id, $template);
		$html = str_replace("{{icon}}", $this->icon, $html);
		$html = str_replace("{{name}}", $this->name, $html);
		$html = str_replace("{{provisioned-class}}", ($this->provisioned ? "provisioned" : "not-provisioned"), $html);
		$html = str_replace("{{extension-required-class}}", ($this->extension_required ? "extension-required" : "not-extension-required"), $html);

		return $html;
	}
}