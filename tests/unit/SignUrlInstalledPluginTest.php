<?php 

namespace s3securedownloads\tests;

use \Codeception\Test\Unit;
use kennethormandy\s3securedownloads\S3SecureDownloads;

use craft\elements\Asset;
use UnitTester;
use Craft;

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
    
    protected function _checkUrlBasics($result) {
      codecept_debug('');
      codecept_debug($result);
      codecept_debug('');

      $this->assertTrue(is_string($result));
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

      $this->assertStringNotContainsString('UNSIGNED-PAYLOAD', $result);
    }
}
