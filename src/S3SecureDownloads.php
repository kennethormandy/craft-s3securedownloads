<?php
/**
 * S3 Secure Download plugin for Craft CMS.
 *
 * This plugin will return a signed URL used to allow secure/expiring downloads from an S3 bucket.
 *
 * Download requests are proxied by a controller that ensured there is a valid user session.
 *
 * @author    Jonathan Melville, Kenneth Ormandy
 * @copyright Copyright © 2016–2019 Jonathan Melville, Copyright © 2019 Kenneth Ormandy Inc.
 * @link      https://github.com/kennethormandy/craft-s3securedownloads
 * @since     1.0.0
 */

namespace kennethormandy\s3securedownloads;

use Craft;
use craft\base\Plugin;
use kennethormandy\s3securedownloads\models\Settings;
use kennethormandy\s3securedownloads\services\SignUrl;
use kennethormandy\s3securedownloads\twigextensions\S3SecureDownloadsTwigExtension;

class S3SecureDownloads extends Plugin
{
    public string $schemaVersion = '1.1.0';
    public bool $hasCpSettings = true;

    public static $plugin;
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->setComponents([
        'signUrl' => SignUrl::class,
      ]);

        if (Craft::$app->request->getIsSiteRequest()) {
            // Add in our Twig extension
            $extension = new S3SecureDownloadsTwigExtension();
            Craft::$app->getView()->registerTwigExtension($extension);
            // ?
        // Craft::$app->view->registerTwigExtension($extension);
        }
    }

    /**
     * Add any Twig extensions.
     *
     * @return mixed
     */

    // Protected Methods
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel(): ?craft\base\Model
    {
        return new Settings();
    }

    /**
     * Returns the rendered settings HTML, which will be inserted into the content
     * block on the settings page.
     *
     * @return string The rendered settings HTML
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            's3securedownloads/settings',
            [
                'settings' => $this->getSettings(),
            ]
        );
    }
}
