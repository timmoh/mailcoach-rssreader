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

    public function test_ReadRss() {
        /** @var SimplePie $rss */
        $rss = MailcoachRssReaderFacade::read($this->xmlUrl);

        $this->assertEquals('FeedForAll Sample Feed', $rss->get_title());
        $this->assertObjectHasAttribute('term', $rss->get_category());
        $this->assertEquals(9, $rss->get_item_quantity());
    }
}
