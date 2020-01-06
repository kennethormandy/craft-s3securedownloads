<?php
namespace kennethormandy\s3securedownloads\controllers;
use kennethormandy\s3securedownloads\S3SecureDownloads;

use Craft;
use craft\web\Controller;

class DownloadProxyController extends Controller
{
	protected $allowAnonymous = true;

	public function getSetting($setting_name) 
	{
		$pluginSettings = S3SecureDownloads::$plugin->getSettings();
		return $pluginSettings[$setting_name];
	}

	public function actionGetFile() {

		if( $this->getSetting("requireLoggedInUser") ) {
			$this->requireLogin();
		}
		
		$entry_id = $_GET['uid'];
		
		if (!isset($entry_id)) {
			// TODO Error
		}

		$signedUrl = S3SecureDownloads::$plugin->s3securedownloads->getSignedUrl($entry_id);

		return $this->redirect($signedUrl, 302);
	}
}
