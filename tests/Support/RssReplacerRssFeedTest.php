<?php

namespace Timmoh\MailcoachRssReader\Tests\Support;

use SimplePie;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Snapshots\MatchesSnapshots;
use Timmoh\MailcoachRssReader\Facades\MailcoachRssReader as MailcoachRssReaderFacade;
use Timmoh\MailcoachRssReader\Tests\TestCase;
use Symfony\Component\Process\Process;

class RssReplacerTest extends ReplaceTestCase {
    use MatchesSnapshots;

    public function test_rssblock_without_url_shouldnt_replaced() {
        $html = "TextBefore::RSSBLOCK::TextInner::RSSBLOCKEND::TextAfter";
        $expectedContent = $html;
        $expectedHtml = '<html><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd" ><body><p>' . $expectedContent . '</p></body></html>';


        /** @var \Spatie\Mailcoach\Models\Campaign */
        $campaign = factory(Campaign::class)->create([
            'html' => $html,
        ]);

        $this->execute($campaign);
        $campaign->refresh();
        $replacedhtml = $campaign->email_html;
        $this->assertMatchesHtmlSnapshotWithoutWhitespace($expectedHtml);
    }

    public function test_rssblock_without_url_should_replaced() {
        $html = "::RSSBLOCK:".$this->xmlUrl."::TextInner::RSSBLOCKEND::";
        $expectedContent = "http://www.foo.bar/ffalogo48x48.gif";
        $expectedHtml = '<html><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd" ><body><p>' . $expectedContent . '</p></body></html>';

        /** @var \Spatie\Mailcoach\Models\Campaign */
        $campaign = factory(Campaign::class)->create([
            'html' => $html,
        ]);

        $this->replacerClasses = [\Timmoh\MailcoachRssReader\Support\Replacers\RssReplacer::class];
        $this->execute($campaign);
        $campaign->refresh();
        $this->assertMatchesHtmlSnapshotWithoutWhitespace($expectedHtml);
    }
    public function test_rss_title() {
        $html = "::RSSBLOCK:".$this->xmlUrl."::::RSSFEEDTITLE::::RSSBLOCKEND::";
        $expectedContent = "FeedForAll Sample Feed";
        $expectedHtml = '<html><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd" ><body><p>' . $expectedContent . '</p></body></html>';

        /** @var \Spatie\Mailcoach\Models\Campaign */
        $campaign = factory(Campaign::class)->create([
            'html' => $html,
        ]);

        $this->execute($campaign);
        $campaign->refresh();
        $this->assertMatchesHtmlSnapshotWithoutWhitespace($expectedHtml);
    }

    public function test_rss_description() {
        $html = "::RSSBLOCK:".$this->xmlUrl."::::RSSFEEDTITLE::::RSSFEEDDESCRIPTION::";
        $expectedContent = "RSS is a fascinating technology. The uses for RSS are expanding daily. Take a closer look at how various industries are using the
            benefits of RSS in their businesses.";
        $expectedHtml = '<html><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd" ><body><p>' . $expectedContent . '</p></body></html>';

        /** @var \Spatie\Mailcoach\Models\Campaign */
        $campaign = factory(Campaign::class)->create([
            'html' => $html,
        ]);

        $this->execute($campaign);
        $campaign->refresh();
        $this->assertMatchesHtmlSnapshotWithoutWhitespace($expectedHtml);
    }

    public function test_rssitem_logo() {
        $html = "::RSSBLOCK:".$this->xmlUrl."::::RSSITEMSBLOCK:1::::RSSFEEDIMAGE::::RSSITEMSBLOCKEND::::RSSBLOCKEND::";
        $expectedContent = "http://www.foo.bar/ffalogo48x48.gif";
        $expectedHtml = '<html><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd" ><body><p>' . $expectedContent . '</p></body></html>';

        /** @var \Spatie\Mailcoach\Models\Campaign */
        $campaign = factory(Campaign::class)->create([
            'html' => $html,
        ]);

        $this->execute($campaign);
        $campaign->refresh();
        $this->assertMatchesHtmlSnapshotWithoutWhitespace($expectedHtml);
    }

    public function test_rss_feedurl() {
        $html = "::RSSBLOCK:".$this->xmlUrl."::::RSSFEEDURL::::RSSBLOCKEND::";
        $expectedContent = "http://www.foo.bar/industry-solutions.htm";
        $expectedHtml = '<html><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd" ><body><p>' . $expectedContent . '</p></body></html>';

        /** @var \Spatie\Mailcoach\Models\Campaign */
        $campaign = factory(Campaign::class)->create([
            'html' => $html,
        ]);

        $this->execute($campaign);
        $campaign->refresh();
        $this->assertMatchesHtmlSnapshotWithoutWhitespace($expectedHtml);
    }


}
