<?php

namespace s3securedownloads\tests;

use Codeception\Test\Unit;
use craft\elements\Asset;
use kennethormandy\s3securedownloads\S3SecureDownloads;
use UnitTester;

class ForceFileDownloadTest extends Unit
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

    private function _getHeaders($url)
    {
        $result = [];
        $headers = get_headers($url);

        foreach ($headers as $key => $header) {
            $header = explode(': ', $header);
            if (count($header) >= 2) {
                $result[$header[0]] = $header[1];
            }
        }

        return $result;
    }

    private function _checkContentDisposition($headers, $asset)
    {
        $this->assertTrue(isset($headers));
        $this->assertTrue(isset($headers['Content-Disposition']));
        $this->assertStringContainsString($asset->filename, $headers['Content-Disposition']);
        $this->assertTrue('attachment; filename="' . $asset->filename . '"' === $headers['Content-Disposition']);
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

        $this->_checkContentDisposition($headers, $asset);
    }

    public function testOnFolderPath()
    {
        $hardCodedFolderId = 5;
        $asset = Asset::find()
        ->folderId($hardCodedFolderId)
        ->volume($this->volumeHandle)
        ->one();

        $forceFileDownload = 1;

        // Change s3securedownloads\models\Settings value temporarily
        S3SecureDownloads::$plugin
        ->getSettings()['forceFileDownload'] = $forceFileDownload;

        $url = S3SecureDownloads::$plugin->signUrl->getSignedUrl($asset->uid);
        $headers = $this->_getHeaders($url);

        // Mainly check the default download filename is the same,
        // ex. pdf/example.pdf could give you:
        // `pdf_example.pdf` (folder and filename) or
        // `example.pdf` (filename only)
        // We want filename only
        $this->_checkContentDisposition($headers, $asset);
    }
}
