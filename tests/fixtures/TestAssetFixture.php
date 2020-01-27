<?php
// Not loading properly from this location


namespace tests\codeception\fixtures\;

// namespace app\tests\fixtures;
// namespace tests\codeception\fixtures;

// https://forum.yiiframework.com/t/using-fixtures-for-unit-tests-solved/73509/2
// namespace app\tests\unit\fixtures;
// namespace app\tests\codeception\unit\fixtures;

// namespace kennethormandy\s3securedownloads\tests\fixtures;
// namespace fixtures;

// namespace app\tests\fixtures;

use craft\test\fixtures\elements\AssetFixture;

class TestAssetFixture extends AssetFixture
{
    /**
     * {@inheritdoc}
     */
    public $dataFile = __DIR__.'/../_data/test-asset.php';
}
