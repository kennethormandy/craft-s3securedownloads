<?php
/**
 * S3 Secure Downloads plugin for Craft CMS
 *
 * S3 Secure Downloads Twig Extension
 *
 *
 * @author    Jonathan Melville
 * @copyright Copyright (c) 2016 Jonathan Melville
 * @link      http://jonathanmelville.com
 * @package   S3SecureDownloads
 * @since     1.0.0
 */

namespace Craft;

use Twig_Extension;
use Twig_Filter_Method;

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
        return array(
            'getSignedUrl' => new \Twig_Function_Method($this, 'getSignedUrl'),
        );
    }

    /**
     *
     * @return string
     */
    public function getSignedUrl($entry_id = null)
    {

        return UrlHelper::getActionUrl('s3SecureDownloads/downloadProxy/getFile', array('id' => $entry_id));
    }
}