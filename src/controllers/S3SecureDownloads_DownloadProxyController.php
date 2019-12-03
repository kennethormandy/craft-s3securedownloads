<?php
namespace Craft;

class S3SecureDownloads_DownloadProxyController extends BaseController
{
	protected $allowAnonymous = true;

	public function getSetting($setting_name) 
	{
		$plugin = craft()->plugins->getPlugin('s3SecureDownloads');

		$settings = $plugin->getSettings();

		return $settings->$setting_name;
	}

	public function actionGetFile() {

		if( $this->getSetting("requireLoggedInUser") ) {

			$this->requireLogin();
			
		}


		$entry_id = $_GET['id'];

		$signedUrl = craft()->s3SecureDownloads_url->getSignedUrl( $entry_id );

		$this->redirect( $signedUrl, $terminate = true, $statusCode = 302 );

	}
}
