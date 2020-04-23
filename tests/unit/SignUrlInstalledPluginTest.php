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
      $awsRegion = Craft::parseEnv($volumeSettings['settings']['region']);

      $this->assertTrue(isset($asset));
      $result = S3SecureDownloads::$plugin->signUrl->getSignedUrl($asset->uid);

      codecept_debug('');
      codecept_debug($result);
      codecept_debug('');

      $this->assertTrue(is_string($result));
      $this->assertStringContainsString('https://', $result);
      $this->assertStringContainsString('amazonaws.com', $result);
      $this->assertStringContainsString($filename, $result);
    }
    
    public function testAssetInSubfolder()
    {
      $hardCodedFolderId = 5;
      $hardCodedVolumeHandle = 'volumeS3';
      $assetQuery = Asset::find()
        ->volume($hardCodedVolumeHandle)
        ->folderId($hardCodedFolderId);
      $asset = $assetQuery->one();

      $this->assertTrue(isset($asset->folderPath));

      codecept_debug('$asset->folderPath');
      codecept_debug($asset->folderPath);

      codecept_debug($asset);
      
      $this->assertTrue(isset($asset));
      $result = S3SecureDownloads::$plugin->signUrl->getSignedUrl($asset->uid);

      codecept_debug('');
      codecept_debug($result);
      codecept_debug('');

      $this->assertTrue(is_string($result));
      $this->assertStringContainsString('https://', $result);
      $this->assertStringContainsString('amazonaws.com', $result);

      $this->assertStringContainsString($asset->folderPath, $result);

    }
}
