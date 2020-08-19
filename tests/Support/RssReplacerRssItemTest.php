<?php

namespace Timmoh\MailcoachRssReader\Tests\Support;

use SimplePie;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Snapshots\MatchesSnapshots;
use Timmoh\MailcoachRssReader\Facades\MailcoachRssReader as MailcoachRssReaderFacade;
use Timmoh\MailcoachRssReader\Tests\TestCase;
use Symfony\Component\Process\Process;

class RssReplacerRssItemTest extends ReplaceTestCase {
    use MatchesSnapshots;

    public function test_rssitem_title() {
        $html = "::RSSBLOCK:".$this->xmlUrl."::::RSSITEMSBLOCK:1::::RSSITEMTITLE::::RSSITEMSBLOCKEND::::RSSBLOCKEND::";
        $expectedContent = "RSS Solutions for Restaurants";
        $expectedHtml = '<html><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd" ><body><p>' . $expectedContent . '</p></body></html>';

        /** @var \Spatie\Mailcoach\Models\Campaign */
        $campaign = factory(Campaign::class)->create([
            'html' => $html,
        ]);

        $this->execute($campaign);
        $campaign->refresh();
        $this->assertMatchesHtmlSnapshotWithoutWhitespace($expectedHtml);
    }

    public function test_rsstwoitem_title() {
        $html = "::RSSBLOCK:".$this->xmlUrl."::::RSSITEMSBLOCK:2::::RSSITEMTITLE::::RSSITEMSBLOCKEND::::RSSBLOCKEND::";
        $expectedContent = "RSS Solutions for RestaurantsRSS Solutions for Schools and Colleges";
        $expectedHtml = '<html><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd" ><body><p>' . $expectedContent . '</p></body></html>';

        /** @var \Spatie\Mailcoach\Models\Campaign */
        $campaign = factory(Campaign::class)->create([
            'html' => $html,
        ]);

        $this->execute($campaign);
        $campaign->refresh();
        $this->assertMatchesHtmlSnapshotWithoutWhitespace($expectedHtml);
    }

    public function test_rssitem_url() {
        $html = "::RSSBLOCK:".$this->xmlUrl."::::RSSITEMSBLOCK:1::::RSSITEMURL::::RSSITEMSBLOCKEND::::RSSBLOCKEND::";
        $expectedContent = "http://www.foo.bar/restaurant.htm";
        $expectedHtml = '<html><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd" ><body><p>' . $expectedContent . '</p></body></html>';

        /** @var \Spatie\Mailcoach\Models\Campaign */
        $campaign = factory(Campaign::class)->create([
            'html' => $html,
        ]);

        $this->execute($campaign);
        $campaign->refresh();
        $this->assertMatchesHtmlSnapshotWithoutWhitespace($expectedHtml);
    }

    public function test_rssitem_author() {
        $html = "::RSSBLOCK:".$this->xmlUrl."::::RSSITEMSBLOCK:1::::RSSITEMAUTHOR::::RSSITEMSBLOCKEND::::RSSBLOCKEND::";
        $expectedContent = "Mr Super";
        $expectedHtml = '<html><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd" ><body><p>' . $expectedContent . '</p></body></html>';

        /** @var \Spatie\Mailcoach\Models\Campaign */
        $campaign = factory(Campaign::class)->create([
            'html' => $html,
        ]);

        $this->execute($campaign);
        $campaign->refresh();
        $this->assertMatchesHtmlSnapshotWithoutWhitespace($expectedHtml);
    }

    public function test_rssitem_categories() {
        $html = "::RSSBLOCK:".$this->xmlUrl."::::RSSITEMSBLOCK:1::::RSSITEMCATEGORIES::::RSSITEMSBLOCKEND::::RSSBLOCKEND::";
        $expectedContent = "Computers/Software/Internet/Site Management/Content Management";
        $expectedHtml = '<html><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd" ><body><p>' . $expectedContent . '</p></body></html>';

        /** @var \Spatie\Mailcoach\Models\Campaign */
        $campaign = factory(Campaign::class)->create([
            'html' => $html,
        ]);

        $this->execute($campaign);
        $campaign->refresh();
        $this->assertMatchesHtmlSnapshotWithoutWhitespace($expectedHtml);
    }

    public function test_rssitem_content() {
        $html = "::RSSBLOCK:".$this->xmlUrl."::::RSSITEMSBLOCK:1::::RSSITEMCONTENT::::RSSITEMSBLOCKEND::::RSSBLOCKEND::";
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

    public function test_rssitem_description() {
        $html = "::RSSBLOCK:".$this->xmlUrl."::::RSSITEMSBLOCK:1::::RSSITEMDESCRIPTION::::RSSITEMSBLOCKEND::::RSSBLOCKEND::";
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

    public function test_rssitem_thumbnail() {
        $html = "::RSSBLOCK:".$this->xmlUrl."::::RSSITEMSBLOCK:1::::RSSITEMTHUMBNAIL::::RSSITEMSBLOCKEND::::RSSBLOCKEND::";
        $expectedContent = "http://www.foo.bar/mythumb.jpg";
        $expectedHtml = '<html><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd" ><body><p>' . $expectedContent . '</p></body></html>';

        /** @var \Spatie\Mailcoach\Models\Campaign */
        $campaign = factory(Campaign::class)->create([
            'html' => $html,
        ]);

        $this->execute($campaign);
        $campaign->refresh();
        $this->assertMatchesHtmlSnapshotWithoutWhitespace($expectedHtml);
    }

    public function test_rssitem_date() {
        $html = "::RSSBLOCK:".$this->xmlUrl."::::RSSITEMSBLOCK:1::::RSSITEMDATE::::RSSITEMSBLOCKEND::::RSSBLOCKEND::";
        $expectedContent = "Tue, 19 Oct 2004 11:09:11 -0400";
        $expectedHtml = '<html><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd" ><body><p>' . $expectedContent . '</p></body></html>';

        /** @var \Spatie\Mailcoach\Models\Campaign */
        $campaign = factory(Campaign::class)->create([
            'html' => $html,
        ]);

        $this->execute($campaign);
        $campaign->refresh();
        $this->assertMatchesHtmlSnapshotWithoutWhitespace($expectedHtml);
    }

    public function test_rssitem_dateformated_ymd() {
        $html = "::RSSBLOCK:".$this->xmlUrl."::::RSSITEMSBLOCK:1::::RSSITEMDATE:#y-m-d#::::RSSITEMSBLOCKEND::::RSSBLOCKEND::";
        $expectedContent = "2004-10-19";
        $expectedHtml = '<html><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd" ><body><p>' . $expectedContent . '</p></body></html>';

        /** @var \Spatie\Mailcoach\Models\Campaign */
        $campaign = factory(Campaign::class)->create([
            'html' => $html,
        ]);

        $this->execute($campaign);
        $campaign->refresh();
        $this->assertMatchesHtmlSnapshotWithoutWhitespace($expectedHtml);
    }

    public function test_rssitem_dateformated_ymdhi() {
        $html = "::RSSBLOCK:".$this->xmlUrl."::::RSSITEMSBLOCK:1::::RSSITEMDATE:#y-m-d h:i#::::RSSITEMSBLOCKEND::::RSSBLOCKEND::";
        $expectedContent = "2004-10-19 11:09";
        $expectedHtml = '<html><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd" ><body><p>' . $expectedContent . '</p></body></html>';

        /** @var \Spatie\Mailcoach\Models\Campaign */
        $campaign = factory(Campaign::class)->create([
            'html' => $html,
        ]);

        $this->execute($campaign);
        $campaign->refresh();
        $this->assertMatchesHtmlSnapshotWithoutWhitespace($expectedHtml);
    }

    public function test_rssitem_fullhtml() {
        $html = '<div>::RSSBLOCK:'.$this->xmlUrl.'::::RSSITEMSBLOCK:1::<span class="title"><a href="::RSSITEMURL::">::RSSITEMTITLE::</a></span><span class="date">::RSSITEMDATE:#y-m-d h:i#::</span><p class="description">::RSSITEMDESCRIPTION::</p><p class="content">::RSSITEMCONTENT::</p>::RSSITEMSBLOCKEND::::RSSBLOCKEND::</div>';
        $expectedContent = '<div><span class="title"><a href="http://www.foo.bar/restaurant.htm">RSS Solutions for Restaurants</a></span><span class="date">2004-10-19 11:09</span><p class="description">RSS is a fascinating technology. The uses for RSS are expanding daily. Take a closer look at how various industries are using the
            benefits of RSS in their businesses.</p><p class="content">RSS is a fascinating technology. The uses for RSS are expanding daily. Take a closer look at how various industries are using the
            benefits of RSS in their businesses.</p></div>';
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
