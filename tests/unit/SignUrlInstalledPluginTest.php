<?php

namespace s3securedownloads\tests;

use Codeception\Test\Unit;
use Craft;
use craft\elements\Asset;
use kennethormandy\s3securedownloads\S3SecureDownloads;
use UnitTester;

class SignUrlInstalledPluginTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    protected function _checkUrlBasics($result)
    {
        codecept_debug('');
        codecept_debug($result);
        codecept_debug('');

        $this->assertInternalType('string', $result);
        $this->assertStringContainsString('https://', $result);
        $this->assertStringContainsString('amazonaws.com', $result);
        $this->assertStringContainsString('Expires=86', $result);
    }

    public function testSignUrl()
    {
        $hardCodedVolumeHandle = 'volumeS3';
        $assetQuery = Asset::find()->volume($hardCodedVolumeHandle)->kind('image');
        $asset = $assetQuery->one();

        if (!isset($asset)) {
            codecept_debug('⚠️ No image asset in a volume with the handle `volumeS3`');
        }

        $filename = $asset->getFilename();
        $volumeSettings = $asset->getVolume()->getSettings();
        $awsRegion = Craft::parseEnv($volumeSettings['region']);
        $awsSecret = Craft::parseEnv($volumeSettings['secret']);

        $this->assertTrue(isset($asset));
        $result = S3SecureDownloads::$plugin->signUrl->getSignedUrl($asset->uid);

        $this->_checkUrlBasics($result);

        // Contains filename
        $this->assertStringContainsString($filename, $result);

        // Contains AWS region
        $this->assertStringContainsString($awsRegion, $result);

        // Doesn’t have the volume’s AWS secret
        $this->assertTrue(isset($awsSecret));
        $this->assertStringNotContainsString($awsSecret, $result);
    }

    public function testAssetFolderPath()
    {
        $hardCodedFolderId = 5;
        $hardCodedVolumeHandle = 'volumeS3';
        $assetQuery = Asset::find()
        ->volume($hardCodedVolumeHandle)
        ->folderId($hardCodedFolderId);
        $asset = $assetQuery->one();

        $this->assertTrue(isset($asset->folderPath));
        $result = S3SecureDownloads::$plugin->signUrl->getSignedUrl($asset->uid);

        $this->_checkUrlBasics($result);
        $this->assertStringContainsString($asset->folderPath, $result);
    }

    public function testAssetSubfolderVolume()
    {
        $hardCodedVolumeHandle = 'volumeS3Subfolder';
        $assetQuery = Asset::find()
        ->volume($hardCodedVolumeHandle);
        $asset = $assetQuery->one();

        // Shouldn’t have folderPath. A folderPath is folder in the volume,
        // and this is the bucket subfolder as its own volume
        $this->assertTrue($asset->folderPath === '');
        $result = S3SecureDownloads::$plugin->signUrl->getSignedUrl($asset->uid);

        $this->_checkUrlBasics($result);
        $this->assertStringContainsString($asset->folderPath, $result);
    }

    public function testPayloadSignedV4()
    {
        $hardCodedFolderId = 5;
        $hardCodedVolumeHandle = 'volumeS3';
        $assetQuery = Asset::find()
        ->volume($hardCodedVolumeHandle)
        ->folderId($hardCodedFolderId);
        $asset = $assetQuery->one();

        $result = S3SecureDownloads::$plugin->signUrl->getSignedUrl($asset->uid);

        // Header is required
        $this->assertStringContainsString('X-Amz-Content-Sha256', $result, 'x-amz-content-sha256 header is required');

        // Documentation:
      // https://docs.aws.amazon.com/AmazonS3/latest/API/sig-v4-header-based-auth.html#example-signature-calculations
      //
      // These examples show how you could pre-sign the URL without the SDK.
      // I tried modifying them to take the existing URL from the SDK and then
      // sign it with the SHA256 header using the file contents the contents
      // …but what is the point? You are checking the file that this moment matches
      // the file a moment later.
        //   Might have some meaning if the asset already has the SHA256
        // hash as part of its metadata.
        // Some examples:
      //   https://gist.github.com/kelvinmo/d78be66c4f36415a6b80
        //   https://gist.github.com/anthonyeden/4448695ad531016ec12bcdacc9d91cb8

      // …so, no longer using this test:
      // $this->assertStringNotContainsString('UNSIGNED-PAYLOAD', $result, 'Working on this');
      // That seems to be the correct response if we don’t have the payload in advance (ex.
      // the user just uploaded it).
    }
}
