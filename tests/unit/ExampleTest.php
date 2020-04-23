<?php

namespace s3securedownloads\tests;

use Codeception\Test\Unit;
// use craft\services\Assets;
use craft\elements\Asset;

use UnitTester;
use Craft;

class ExampleTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    // public function getComponentMap()
    // {
    // 
    //     codecept_debug('getComponentMap!');
    // 
    //     return [
    //         [Assets::class, ['getAssets', 'assets']],
    //     ];
    // }

    public function testExample()
    {
        $volumesService = Craft::$app->getVolumes();
        $assetsService = Craft::$app->getAssets();
        
        $folder = $assetsService;
        
        $asset = Asset::find()->one();
        
        codecept_debug('hello');
        codecept_debug($asset->getFilename());
    }
}
