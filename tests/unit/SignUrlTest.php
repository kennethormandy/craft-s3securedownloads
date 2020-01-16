<?php 
namespace kennethormandy\s3securedownloads\tests\unit;

use \Codeception\Test\Unit;
use kennethormandy\s3securedownloads\services\SignUrl;
// use kennethormandy\s3securedownloads\tests\fixtures\TestAssetFixture;
use craft\test\fixtures\elements\AssetFixture;

use UnitTester;
use Craft;

class TestAssetFixture extends AssetFixture
{
    /**
     * {@inheritdoc}
     */
    public $dataFile = __DIR__.'/../_data/test-asset.php';
}


class SignUrlTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public $service;

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

      codecept_debug($assets);

      $res = $this->service->getSignedUrl($assets[0]);

      codecept_debug($res);

      // $this->assertTrue($res['success']);
      // $this->assertContains('url', $res);
      // $this->assertContains('mbIn', $res);
      // $this->assertTrue($res['mbIn'] > 0);
    }
}
