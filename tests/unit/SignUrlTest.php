<?php 

namespace app\tests\unit;

use \Codeception\Test\Unit;
use kennethormandy\s3securedownloads\services\SignUrl;
use craft\test\fixtures\elements\AssetFixture;

// use app\tests\unit\fixtures\TestAssetFixture;
// use tests\codeception\fixtures\TestAssetFixture;

use UnitTester;
use Craft;

// TODO I can’t get fixtures in the fixture folder to work!
class TestAssetFixture extends AssetFixture
{
    /**
     * {@inheritdoc}
     */
    // If it was in the proper folder, could also be put in:
    // public $dataFile = __DIR__.'/data/test-asset.php';
    public $dataFile = __DIR__.'/../_data/test-asset.php';
}

class SignUrlTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public $service;
    
    public function _fixtures()
    {
      codecept_debug('hello');
      codecept_debug(TestAssetFixture::className());

        return [
            'profiles' => [
                'class' => TestAssetFixture::className(),
                'dataFile' => codecept_data_dir() . 'test-asset.php'
            ],
        ];
    }

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
      $assets = new TestAssetFixture();

      // Seems to work?
      // codecept_debug($assets);

      codecept_debug($assets[0]->getFilename());

      $res = $this->service->getSignedUrl($assets[0]);

      codecept_debug($res);

      // $this->assertTrue($res['success']);
      // $this->assertContains('url', $res);
      // $this->assertContains('mbIn', $res);
      // $this->assertTrue($res['mbIn'] > 0);
    }
}
