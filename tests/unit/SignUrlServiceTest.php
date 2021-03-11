<?php

namespace s3securedownloads\tests;

use Codeception\Test\Unit;
use Craft;
use craft\elements\Asset;
use kennethormandy\s3securedownloads\services\SignUrl;
use UnitTester;

// use craft\test\fixtures\elements\AssetFixture;
//
// TODO I can’t get fixtures in the fixture folder to work, but
//      this did get me part of the way there.
// class TestAssetFixture extends AssetFixture
// {
//     public $dataFile = __DIR__ . '/../_data/test-asset.php';
//     // public $depends = [VolumesFixture::class, VolumesFolderFixture::class];
// }

class SignUrlServiceTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public $service;

    // Not sure if this part was doing anything
    // public function _fixtures()
    // {
    //   // codecept_debug(TestAssetFixture::className());
    //
    //   return [
    //     'assets' => [
    //         'class' => TestAssetFixture::class
    //     ]
    //   ];
    // }

    protected function _before()
    {
        parent::_before();

        // TODO Create asset
        $this->service = new SignUrl();
    }

    protected function _after()
    {
    }

    public function testSignUrl()
    {
        $volumeHandle = 'volumeS3';
        $assetQuery = Asset::find()->volume($volumeHandle)->kind('image');
        $asset = $assetQuery->one();

        if (!isset($asset)) {
            codecept_debug('⚠️ No image asset in a volume with the handle `volumeS3`');
        }

        $filename = $asset->getFilename();

        $this->assertTrue(isset($asset));
        $result = $this->service->getSignedUrl($asset->uid);

        codecept_debug('');
        codecept_debug($result);
        codecept_debug('');

        $this->assertInternalType('string', $result);
        $this->assertStringContainsString('https://', $result);
        $this->assertStringContainsString('amazonaws.com', $result);
        $this->assertStringContainsString($filename, $result);
    }
}
