<?php

namespace Timmoh\MailcoachRssReader\Tests\Unit;

use SimplePie;
use Timmoh\MailcoachRssReader\MailcoachRssReader;
use Timmoh\MailcoachRssReader\Facades\MailcoachRssReader as MailcoachRssReaderFacade;
use Timmoh\MailcoachRssReader\MailcoachRssReaderServiceProvider;

use Timmoh\MailcoachRssReader\Tests\TestCase;
use Symfony\Component\Process\Process;

class MailcoachRssReaderTest extends TestCase {
    public function test_Instance() {
        $this->assertInstanceOf(MailcoachRssReader::class, $this->app->make('mailcoach-rss-reader'));
    }

    /**
     * @test
     */
    public function readRss() {
        /** @var SimplePie $rss */
        $rss = MailcoachRssReaderFacade::read($this->xmlUrl);

        $items = $this->xml->getElementsByTagName("item");
        $this->assertEquals($this->xml->getElementsByTagName("title")->item(0)->nodeValue, $rss->get_title());
        $this->assertObjectHasAttribute('term', $rss->get_category());
        $this->assertEquals($this->xml->getElementsByTagName("category")->item(0)->nodeValue, $rss->get_category()->get_term());
        $this->assertEquals(count($items), $rss->get_item_quantity());
    }
}
