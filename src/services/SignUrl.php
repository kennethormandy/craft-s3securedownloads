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

use Etime\Flysystem\Plugin\AWS_S3 as AwsS3Plugin;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use Aws\S3\S3Client;

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
		$adapter = new AwsS3Adapter($client, Craft::parseEnv($volume->bucket), Craft::parseEnv($volume->subfolder));
		$filesystem = new Filesystem($adapter);
		$filesystem->addPlugin(new AwsS3Plugin\PresignedUrl());

		
		
		// Something might be going wrong here, it can’t
		// find the original asset, so it can’t sign the payload,
		// so it adds UNSIGNED-PAYLOAD to the URL
		$assetPath = $this->_getAssetPath($asset);
		$assetFullUrl = 'https://craft-s3securedownloads.s3.us-west-2.amazonaws.com/' . $assetPath;

		// TODO I think I’m missing the “string to sign”
		// step, ie. there are some parts similar to the
		// existing v2 implementation for v4
		// https://docs.aws.amazon.com/AmazonS3/latest/API/sig-v4-header-based-auth.html#example-signature-calculations
		
		// TOOD Maybe just do this directly, without plugin?
		// https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/s3-presigned-url.html
		$url = $filesystem->getPresignedUrl($this->_getAssetPath($asset));

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
		$filename = _getAssetPath($asset);
		
		$volume = $asset->getVolume();
		$subfolder = $volume->subfolder;
		
		// Add slash to end of path, since subfolder may not have it
		// https://stackoverflow.com/a/9339669/864799
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
			$headers["response-content-disposition"] = "attachment; filename=" . $asset->filename;
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
