<?php

namespace LastCall\Crawler\Test\Helper;

use LastCall\Crawler\Configuration\Configuration;
use LastCall\Crawler\Helper\CrawlerHelper;
use LastCall\Crawler\Configuration\ConfigurationInterface;
use Symfony\Component\Console\Output\NullOutput;

class CrawlerHelperTest extends \PHPUnit_Framework_TestCase {

    /**
     * @expectedException \InvalidArgumentException
     * @expectedException File does not exist: /some/config/that/doesnt/exist.php
     */
    public function testGetInvalidConfigurationFile() {
        $helper = new CrawlerHelper();
        $helper->getConfiguration('/some/config/that/doesnt/exist.php', new NullOutput());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Configuration was not returned.
     */
    public function testGetNotReturnedConfiguration() {
        $file = $this->writeTempConfig("<?php\n");

        $helper = new CrawlerHelper();
        $helper->getConfiguration($file, new NullOutput());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Configuration must implement LastCall\Crawler\Configuration\ConfigurationInterface
     */
    public function testGetInvalidConfiguration() {
        $file = $this->writeTempConfig("<?php\nreturn new stdClass();");
        $helper = new CrawlerHelper();
        $helper->getConfiguration($file, new NullOutput());
    }



    public function testGetConfiguration() {
        $file = $this->writeTempConfig(sprintf("<?php\nreturn new %s('http://google.com');", Configuration::class));
        $helper = new CrawlerHelper();
        $output = new NullOutput();
        $config = $helper->getConfiguration($file, $output);
        $this->assertInstanceOf(ConfigurationInterface::class, $config);
        $this->assertEquals('http://google.com', $config->getBaseUrl());
    }

    public function testGetCrawler() {
        $helper = new CrawlerHelper();
        $config = new Configuration('https://lastcallmedia.com');
        $helper->getCrawler($config);
    }

    private function writeTempConfig($code) {
        $file = tempnam(sys_get_temp_dir(), 'phpunit-crawler-helper-test');
        file_put_contents($file, $code);
        return $file;
    }
}