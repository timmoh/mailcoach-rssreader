<?php

namespace Timmoh\MailcoachRssReader\Tests\Support;

use SimplePie;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Snapshots\MatchesSnapshots;
use Timmoh\MailcoachRssReader\Facades\MailcoachRssReader as MailcoachRssReaderFacade;
use Timmoh\MailcoachRssReader\Tests\TestCase;
use Symfony\Component\Process\Process;

class RssReplacerRssFeed extends ReplaceTestCase {

    use MatchesSnapshots;

    /**
     * @test
     */
    public function rssblock_without_url_shouldnt_replaced() {
        $html            = "TextBefore::RSSBLOCK::TextInner::RSSBLOCKEND::TextAfter";
        $expectedContent = $html;
        $expectedHtml    = $this->htmlbody($expectedContent);
        //$this->assertMatchesHtmlSnapshotWithoutWhitespace($expectedHtml);

        /** @var \Spatie\Mailcoach\Models\Campaign */
        $campaign = factory(Campaign::class)->create([
            'html' => $html,
        ]);

        $this->execute($campaign);
        $campaign->refresh();
        $replacedhtml = $campaign->email_html;

        $this->assertHtmlWithoutWhitespace($expectedHtml, $campaign->email_html);
    }

    /**
     * @test
     */
    public function rssblock_without_url_should_replaced() {
        $html            = "::RSSBLOCK|" . $this->xmlUrl . "|::TextInner::RSSBLOCKEND::";
        $expectedContent = "TextInner";
        $expectedHtml    = $this->htmlbody($expectedContent);
        /** @var \Spatie\Mailcoach\Models\Campaign */
        $campaign = factory(Campaign::class)->create([
            'html' => $html,
        ]);

        $this->replacerClasses = [\Timmoh\MailcoachRssReader\Support\Replacers\RssReplacer::class];
        $this->execute($campaign);
        $campaign->refresh();
        $this->assertHtmlWithoutWhitespace($expectedHtml, $campaign->email_html);
    }

    /**
     * @test
     */
    public function rss_title() {
        $html            = "::RSSBLOCK|" . $this->xmlUrl . "|::::RSSFEEDTITLE::::RSSBLOCKEND::";
        $expectedContent = $this->xml->getElementsByTagName("title")->item(0)->nodeValue;
        $expectedHtml    = $this->htmlbody($expectedContent);

        /** @var \Spatie\Mailcoach\Models\Campaign */
        $campaign = factory(Campaign::class)->create([
            'html' => $html,
        ]);

        $this->execute($campaign);
        $campaign->refresh();
        $this->assertHtmlWithoutWhitespace($expectedHtml, $campaign->email_html);
    }

    /**
     * @test
     */
    public function rss_description() {
        $html            = "::RSSBLOCK|" . $this->xmlUrl . "|::::RSSFEEDDESCRIPTION::::RSSBLOCKEND::";
        $expectedContent = $this->xml->getElementsByTagName("description")->item(0)->nodeValue;
        $expectedHtml    = $this->htmlbody($expectedContent);

        /** @var \Spatie\Mailcoach\Models\Campaign */
        $campaign = factory(Campaign::class)->create([
            'html' => $html,
        ]);

        $this->execute($campaign);
        $campaign->refresh();
        $this->assertHtmlWithoutWhitespace($expectedHtml, $campaign->email_html);
    }

    /**
     * @test
     */
    public function rss_logo() {
        $html            = "::RSSBLOCK|" . $this->xmlUrl . "|::::RSSFEEDIMAGE::::RSSBLOCKEND::";
        $expectedContent = $this->xml->getElementsByTagName("image")->item(0)->getElementsByTagName("url")->item(0)->nodeValue;
        $expectedHtml    = $this->htmlbody($expectedContent);

        /** @var \Spatie\Mailcoach\Models\Campaign */
        $campaign = factory(Campaign::class)->create([
            'html' => $html,
        ]);

        $this->execute($campaign);
        $campaign->refresh();
        $this->assertHtmlWithoutWhitespace($expectedHtml, $campaign->email_html);
    }

    /**
     * @test
     */
    public function rss_feedurl() {
        $html            = "::RSSBLOCK|" . $this->xmlUrl . "|::::RSSFEEDURL::::RSSBLOCKEND::";
        $expectedContent = $this->xml->getElementsByTagName("link")->item(0)->nodeValue;
        $expectedHtml    = $this->htmlbody($expectedContent);

        /** @var \Spatie\Mailcoach\Models\Campaign */
        $campaign = factory(Campaign::class)->create([
            'html' => $html,
        ]);

        $this->execute($campaign);
        $campaign->refresh();
        $this->assertHtmlWithoutWhitespace($expectedHtml, $campaign->email_html);
    }

}
