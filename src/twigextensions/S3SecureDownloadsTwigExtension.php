<?php
/**
 * S3 Secure Downloads plugin for Craft CMS.
 *
 * S3 Secure Downloads Twig Extension
 *
 *
 * @author    Jonathan Melville, Kenneth Ormandy
 * @copyright Copyright © 2016–2019 Jonathan Melville, Copyright © 2019 Kenneth Ormandy Inc.
 * @link      https://github.com/kennethormandy/craft-s3securedownloads
 * @since     1.0.0
 */

namespace kennethormandy\s3securedownloads\twigextensions;

use craft\elements\Asset;
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
     * Returns an array of Twig functions, used in Twig templates via:.
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
     * @return string
     */
    public function getSignedUrl($asset = null, $options = [])
    {
        $assetUid = $this->_getUidFromId($asset);

        if (isset($options['filename']) && $options['filename']) {
            $params['filename'] = $options['filename'];
        }

        $url = UrlHelper::actionUrl('s3securedownloads/download-proxy/get-file', $params);
        return $url;
    }

    private function _getUidFromId($asset = null)
    {
        if (!is_string($asset) && !is_numeric($asset)) {
            return '';
        }

        $asset = Asset::find()->id($asset)->one();

        return $asset->uid;
    }
}
