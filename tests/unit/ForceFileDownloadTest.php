<?php 

namespace s3securedownloads\tests;

use \Codeception\Test\Unit;
use kennethormandy\s3securedownloads\S3SecureDownloads;

use craft\elements\Asset;
use UnitTester;
use Craft;

class forceFileDownloadTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public $volumeHandle = 'volumeS3';

    protected function _before()
    {
    }

    protected function _after()
    {
    }
    
    private function _getHeaders( $url )
    {
      $result = [];
      $headers = get_headers($url);
    
      foreach ($headers as $key => $header) {
        $header = explode(': ', $header);
        if (sizeof($header) >= 2) {
          $result[$header[0]] = $header[1];           
        }
      }

      return $result;
    }
        
    public function testOff()
    {
      $asset = Asset::find()
        ->volume($this->volumeHandle)
        ->kind('pdf')
        ->one();

      $forceFileDownload = 0;

      // Change s3securedownloads\models\Settings value temporarily
      S3SecureDownloads::$plugin
        ->getSettings()['forceFileDownload'] = $forceFileDownload;

      // Result
      // codecept_debug(S3SecureDownloads::$plugin->getSettings());

      $result = S3SecureDownloads::$plugin->signUrl->getSignedUrl($asset->uid);


      $url = S3SecureDownloads::$plugin->signUrl->getSignedUrl($asset->uid);
      $headers = $this->_getHeaders($url);

      codecept_debug('');
      codecept_debug('forceFileDownload off');
      codecept_debug($url);
      codecept_debug('');

      $this->assertTrue(isset($headers));
      $this->assertFalse(isset($headers['Content-Disposition']));
    }
    
    public function testOn()
    {
      $asset = Asset::find()
        ->volume($this->volumeHandle)
        ->kind('pdf')
        ->one();

      $forceFileDownload = 1;

      // Change s3securedownloads\models\Settings value temporarily
      S3SecureDownloads::$plugin
        ->getSettings()['forceFileDownload'] = $forceFileDownload;

      // Result
      // codecept_debug(S3SecureDownloads::$plugin->getSettings());

      $url = S3SecureDownloads::$plugin->signUrl->getSignedUrl($asset->uid);
      $headers = $this->_getHeaders($url);

      codecept_debug('');
      codecept_debug('forceFileDownload on');
      codecept_debug($url);
      codecept_debug('');

      $this->assertTrue(isset($headers));
      $this->assertTrue(isset($headers['Content-Disposition']));
      $this->assertStringContainsString($asset->filename, $headers['Content-Disposition']);
      $this->assertTrue('attachment; filename="' . $asset->filename . '"' === $headers['Content-Disposition']);
    }

}
