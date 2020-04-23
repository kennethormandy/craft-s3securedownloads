<?php

namespace kennethormandy\s3securedownloads\services;

use kennethormandy\s3securedownloads\S3SecureDownloads;
use kennethormandy\s3securedownloads\events\SignUrlEvent;

use Craft;
use craft\base\Component;
use craft\elements\Entry;
use craft\elements\Asset;
use craft\base\Volume;
use craft\awss3\Volume as AwsVolume;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

use yii\base\Exception;

class SignUrl extends Component
{
	public const EVENT_BEFORE_SIGN_URL = 'EVENT_BEFORE_SIGN_URL';
	public const EVENT_AFTER_SIGN_URL = 'EVENT_AFTER_SIGN_URL';

	public function getSignedUrl( $asset_uid )
	{
		$url = false;

		if (empty($asset_uid)) {
			throw new Exception('No asset defined');
		}
		
		$asset = Asset::find()->uid($asset_uid)->one();

		if ($this->hasEventHandlers(self::EVENT_BEFORE_SIGN_URL)) {
			$event = new SignUrlEvent([ 'asset' => $asset ]);
			$this->trigger(self::EVENT_BEFORE_SIGN_URL, $event);
		}
		
		$volume = $asset->getVolume();
		
		$client = new S3Client([
		    'credentials' => [
		        'key'    => Craft::parseEnv($volume->keyId),
		        'secret' => Craft::parseEnv($volume->secret)
		    ],
		    'region' => Craft::parseEnv($volume->region),
		    'version' => 'latest',
		]);

		// // example.png
		// codecept_debug($asset->getUri());
		// 
		// // https://s3.us-west-2.amazonaws.com/craft-s3securedownloads/example.png
		// codecept_debug('$asset->getUrl()');

		// TODO I think I’m missing the “string to sign”
		// step, ie. there are some parts similar to the
		// existing v2 implementation for v4
		// https://docs.aws.amazon.com/AmazonS3/latest/API/sig-v4-header-based-auth.html#example-signature-calculations
		
		// TODO Right now the setting uses the old format (86400ms)
		// but "+24 hours" seems like it would give the same result,
		// and is a lot clearer in settings and code
		$pluginSettings = S3SecureDownloads::$plugin->getSettings();
		$linkExpirationTime = $pluginSettings->linkExpirationTime;
		$expires = time()+$linkExpirationTime;
		
		codecept_debug('$expires');
		codecept_debug($expires);
		codecept_debug('');

		$bucket = Craft::parseEnv($volume->getSettings()['bucket']);
		$keyname = $this->_getAssetPathWithSubfolder($asset);
		$getObjectOptions = [
			'Bucket' => $bucket,
			
			// If there’s a subfolder, need it here for the key,
			// otherwise you get a key is missing error
			'Key' => $keyname
		];

		if (isset($pluginSettings->forceDownload) && $pluginSettings->forceDownload) {
			// https://docs.aws.amazon.com/AmazonS3/latest/dev/RetrieveObjSingleOpPHP.html
			$getObjectOptions['ResponseContentDisposition'] = "attachment; filename=" . $this->_getAssetPath($asset);
		}

		// TODO
		$getObjectOptions['X-Amz-Content-Sha256'] = 'whatever';
		
		$command = $client->getCommand('GetObject', $getObjectOptions);
		
		try {
				$request = $client->createPresignedRequest($command, $expires);
				codecept_debug('$request');
				codecept_debug($request);
				codecept_debug('');

				$url = (string) $request->getUri();
		} catch (S3Exception $exception) {
				$url = false;
		}
		
		codecept_debug('$url');
		codecept_debug($url);
		codecept_debug(' ');

		// if(!isset($url) || !$url) {
		// 	// If new signing approach didn’t work… 
		// 	$url = $this->_manuallyBuildSignedUrl($asset);			
		// }

		if ($this->hasEventHandlers(self::EVENT_AFTER_SIGN_URL)) {
			$event = new SignUrlEvent([ 'asset' => $asset ]);
			$this->trigger(self::EVENT_AFTER_SIGN_URL, $event);
		}
		
		return $url;
	}
	
	private function _getAssetPath( $asset )
	{
		$filename = $asset->filename;
		if ($asset->folderPath) {
			$filename = $asset->folderPath . $asset->filename;
		}
		
		return $filename;
	}

	private function _getAssetPathWithSubfolder( $asset )
	{
		$filename = $this->_getAssetPath($asset);
		
		$volume = $asset->getVolume();
		$subfolder = $volume->subfolder;
		
		// Add slash to end of path, since subfolder may not have it
		// https://stackoverflow.com/a/9339669/864799
		// TODO Could replace some of this with Craft normalizePath()
		// https://docs.craftcms.com/api/v3/craft-helpers-filehelper.html#public-methods
		$urlPrefix = '';
		if ($subfolder) {
			$urlPrefix = rtrim( $subfolder, "/" ) . "/";			
		}
		
		return $urlPrefix . $filename;
	}
	
	private function _manuallyBuildSignedUrl( $asset )
	{

		$baseAssetPath = $this->_getAssetPathWithSubfolder($asset);
		$sourceType = $asset->volume;
		$assetSettings = $sourceType->getAttributes();
		$awsSettings = isset($assetSettings['settings']) ? $assetSettings['settings'] : $assetSettings;
		$bucketName = Craft::parseEnv($awsSettings['bucket']);

		$keyId = Craft::parseEnv($awsSettings['keyId']);

		$secretKey = Craft::parseEnv($awsSettings['secret']);
		$pluginSettings = S3SecureDownloads::$plugin->getSettings();
		$linkExpirationTime = $pluginSettings->linkExpirationTime;
		$forceDownload = $pluginSettings->forceFileDownload;

		$expires = time()+$linkExpirationTime;

		// S3 Signed URL creation
		$headers = array();
		
		if ($forceDownload) {
			$headers["response-content-disposition"] = "attachment; filename=" . $this->_getAssetPath($asset);
		}

		$resource = str_replace( array( '%2F', '%2B' ), array( '/', '+' ), rawurlencode( $baseAssetPath ) );

		// Remove possible leading slash
		if ($resource[0] == '/') {
			$resource = ltrim($resource, $resource[0]);
		}
		
		$string_to_sign = "GET\n\n\n{$expires}\n/{$bucketName}/{$resource}";

		if ($assetSettings['hasUrls']) {
			
			$base_url = Craft::parseEnv($assetSettings['url']);

			// Remove possible duplicate trailing slash
			$base_url = rtrim( $base_url, "/" );

			$final_url = "{$base_url}/{$resource}?";
		} else {
			$final_url = "https://{$bucketName}.s3.amazonaws.com/{$resource}?";
		}

		$append_char = '?';
		foreach ( $headers as $header => $value ) {
			$final_url .= $header . '=' . urlencode( $value ) . '&';
			$string_to_sign .= $append_char . $header . '=' . $value;
			$append_char = '&';
		}

		$signature = urlencode( base64_encode( hash_hmac( 'sha1', $string_to_sign, $secretKey, true ) ) );

		$final_url = $final_url . "AWSAccessKeyId=$keyId&Signature=$signature&Expires=$expires";

		return $final_url;
	}
}
