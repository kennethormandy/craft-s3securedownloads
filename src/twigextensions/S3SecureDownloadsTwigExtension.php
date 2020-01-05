<?php
/**
 * S3 Secure Downloads plugin for Craft CMS
 *
 * S3 Secure Downloads Twig Extension
 *
 *
 * @author    Jonathan Melville, Kenneth Ormandy
 * @copyright Copyright © 2016–2019 Jonathan Melville, Copyright © 2019 Kenneth Ormandy Inc.
 * @link      https://github.com/kennethormandy/craft-s3securedownloads
 * @package   S3SecureDownloads
 * @since     1.0.0
 */

namespace kennethormandy\s3securedownloads\twigextensions;
use kennethormandy\s3securedownloads\S3SecureDownloads;
use kennethormandy\s3securedownloads\services;

use craft\helpers\UrlHelper;

class S3SecureDownloadsTwigExtension extends \Twig_Extension
{
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'S3SecureDownlaods';
    }

    /**
     * Returns an array of Twig functions, used in Twig templates via:
     *
     *      {% set this = someFunction('something') %}
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('getSignedUrl', [$this, 'getSignedUrl']),
        ];
    }

    /**
     *
     * @return string
     */
    public function getSignedUrl($entry_id = null)
    {
        $url = UrlHelper::actionUrl('s3securedownloads/download-proxy/get-file', array('id' => $entry_id));
        return $url;
    }
}
