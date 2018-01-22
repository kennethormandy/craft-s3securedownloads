<?php

namespace Craft;

class S3SecureDownloads_UrlService extends BaseApplicationComponent
{

	public function getSetting( $setting_name ) {
		$plugin = craft()->plugins->getPlugin( 's3SecureDownloads' );
		$settings = $plugin->getSettings();

		return $settings->$setting_name;
	}

	public function getSignedUrl( $entry_id ) {

		$entry = craft()->elements->getElementById( $entry_id, ElementType::Asset );

		$assetUrl = $entry->url;

		$sourceType = craft()->assetSources->getSourceTypeById( $entry->sourceId );
		$assetSettings = $sourceType->getSettings();

		$urlPrefix = $assetSettings->urlPrefix;

		// Remove the mtime query string just in case Craft adds it.
		$baseAssetPath = str_replace( $urlPrefix, "", UrlHelper::stripQueryString($assetUrl) );


		$fileName = $entry->filename;
		$bucketName = $assetSettings->bucket;
		$keyId = $assetSettings->keyId;
		$secretKey = $assetSettings->secret;
		$linkExpirationTime = $this->getSetting( "linkExpirationTime" );
		$forceDownload = $this->getSetting( "forceFileDownload" );



		$expires = time()+$linkExpirationTime;

		// S3 Signed URL creation

		$headers = array();

		if($forceDownload) {
			$headers["response-content-disposition"] = "attachment; filename=" . $fileName;
		}

		$resource = str_replace( array( '%2F', '%2B' ), array( '/', '+' ), rawurlencode( $baseAssetPath ) );

		$string_to_sign = "GET\n\n\n{$expires}\n/{$bucketName}/{$resource}";
		$final_url = "https://{$bucketName}.s3.amazonaws.com/{$resource}?";

		$append_char = '?';
		foreach ( $headers as $header => $value ) {
			$final_url .= $header . '=' . urlencode( $value ) . '&';
			$string_to_sign .= $append_char . $header . '=' . $value;
			$append_char = '&';
		}

		$signature = urlencode( base64_encode( hash_hmac( 'sha1', $string_to_sign, $secretKey, true ) ) );

		return $final_url . "AWSAccessKeyId=$keyId&Expires=$expires&Signature=$signature";


	}
}
