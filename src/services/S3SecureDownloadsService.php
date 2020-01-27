<?php

namespace kennethormandy\s3securedownloads\services;

use kennethormandy\s3securedownloads\S3SecureDownloads;

use Craft;
use craft\base\Component;
use craft\elements\Entry;
use craft\elements\Asset;
use craft\base\Volume;

use yii\base\Exception;

class S3SecureDownloadsService extends Component
{
	public function getSignedUrl( $asset_id ) {

		if (empty($asset_id)) {
			throw new Exception('No asset defined');
		}

		$asset = Asset::find()->uid($asset_id)->one();
		$fileName = $asset->filename;

		$sourceType = $asset->volume;
		$assetSettings = $sourceType->getAttributes();

		$bucketName = Craft::parseEnv($assetSettings['bucket']);

		// Add slash to end of path, since subfolder may not have it
		// https://stackoverflow.com/a/9339669/864799
		$urlPrefix = rtrim( $assetSettings['subfolder'], "/" ) . "/";
		
		$baseAssetPath = $urlPrefix . $asset['folderPath'] . $fileName;
		$keyId = Craft::parseEnv($assetSettings['keyId']);

		$secretKey = Craft::parseEnv($assetSettings['secret']);
		$pluginSettings = S3SecureDownloads::$plugin->getSettings();
		$linkExpirationTime = $pluginSettings->linkExpirationTime;
		$forceDownload = $pluginSettings->forceFileDownload;

		$expires = time()+$linkExpirationTime;

		// S3 Signed URL creation
		$headers = array();
		
		if ($forceDownload) {
			$headers["response-content-disposition"] = "attachment; filename=" . $fileName;
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

		return $final_url . "AWSAccessKeyId=$keyId&Signature=$signature&Expires=$expires";

	}
}
