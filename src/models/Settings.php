<?php

namespace kennethormandy\s3securedownloads\models;

use Craft;
use craft\base\Model;
use kennethormandy\s3securedownloads\S3SecureDownloads;

/**
 * Settings Model
 * https://craftcms.com/docs/plugins/models.
 *
 * @author    Jonathan Melville, Kenneth Ormandy
 * @link      https://github.com/kennethormandy/craft-s3securedownloads
 * @since     1.1.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    public $linkExpirationTime = '86400';
    public $forceFileDownload = 1;
    public $requireLoggedInUser = 1;

    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        return
        [
            // TODO Causing issues saving settings
            // [['linkExpirationTime'], 'required'],
            // ...
        ];
    }
}
