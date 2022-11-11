<?php

namespace kennethormandy\s3securedownloads\controllers;

use Craft;
use craft\web\Controller;
use kennethormandy\s3securedownloads\S3SecureDownloads;

class DownloadProxyController extends Controller
{
    // If this is false, you’ll get a 503 error instead of the
    // login page, when requireLoggedInUser setting is enabled
    protected array|int|bool $allowAnonymous = true;

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
        $options = [];
        $forceDownloadFilename = Craft::$app->request->getParam('filename');

        if (!isset($entry_id)) {
            // TODO Error
        }

        if (isset($forceDownloadFilename) and $forceDownloadFilename) {
            $options['filename'] = $forceDownloadFilename;
        }

        $signedUrl = S3SecureDownloads::$plugin->signUrl->getSignedUrl($entry_id, $options);

        return $this->redirect($signedUrl, 302);
    }
}
