<?php

namespace kennethormandy\s3securedownloads\controllers;

use Craft;
use craft\web\Controller;
use kennethormandy\s3securedownloads\S3SecureDownloads;

class DownloadProxyController extends Controller
{
    // If this is false, you’ll get a 503 error instead of the
    // login page, when requireLoggedInUser setting is enabled
    protected $allowAnonymous = true;

    private function _getSetting($setting_name)
    {
        $pluginSettings = S3SecureDownloads::$plugin->getSettings();
        return $pluginSettings[$setting_name];
    }

    public function actionGetFile()
    {
        if ($this->_getSetting('requireLoggedInUser')) {
            $this->requireLogin();
        }

        $entry_id = Craft::$app->request->getParam('uid');

        if (!isset($entry_id)) {
            // TODO Error
        }

        $signedUrl = S3SecureDownloads::$plugin->signUrl->getSignedUrl($entry_id);

        return $this->redirect($signedUrl, 302);
    }
}
