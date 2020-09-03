<?php

namespace Timmoh\MailcoachRssReader\Tests\Support;

use Carbon\Carbon;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Snapshots\MatchesSnapshots;

class RssReplacerRssItemTest extends ReplaceTestCase
{
    use MatchesSnapshots;

    /**
     * @test
     */
    public function rssitem_title()
    {
        $html = "::RSSBLOCK|" . $this->xmlUrl . "|::::RSSITEMSBLOCK|1::::RSSITEMTITLE::::RSSITEMSBLOCKEND::::RSSBLOCKEND::";
        $expectedContent = $this->xml->getElementsByTagName("item")->item(0)->getElementsByTagName("title")->item(0)->nodeValue;
        $expectedHtml = $this->htmlbody($expectedContent);

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
    public function rsstwoitem_title()
    {
        $html = "::RSSBLOCK|" . $this->xmlUrl . "|::::RSSITEMSBLOCK|2::::RSSITEMTITLE::::RSSITEMSBLOCKEND::::RSSBLOCKEND::";
        $expectedContent = $this->xml->getElementsByTagName("item")
                ->item(0)
                ->getElementsByTagName("title")
                ->item(0)->nodeValue . $this->xml->getElementsByTagName("item")->item(1)->getElementsByTagName("title")->item(0)->nodeValue;
        $expectedHtml = $this->htmlbody($expectedContent);

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
    public function rssitem_url()
    {
        $html = "::RSSBLOCK|" . $this->xmlUrl . "|::::RSSITEMSBLOCK|1::::RSSITEMURL::::RSSITEMSBLOCKEND::::RSSBLOCKEND::";
        $expectedContent = $this->xml->getElementsByTagName("item")->item(0)->getElementsByTagName("link")->item(0)->nodeValue;
        $expectedHtml = $this->htmlbody($expectedContent);

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
    public function rssitem_author()
    {
        $html = "::RSSBLOCK|" . $this->xmlUrl . "|::::RSSITEMSBLOCK|1::::RSSITEMAUTHOR::::RSSITEMSBLOCKEND::::RSSBLOCKEND::";
        $expectedContent = $this->xml->getElementsByTagName("item")->item(0)->getElementsByTagName("author")->item(0)->nodeValue;
        $expectedHtml = $this->htmlbody($expectedContent);

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
    public function rssitem_categories()
    {
        $html = "::RSSBLOCK|" . $this->xmlUrl . "|::::RSSITEMSBLOCK|1::::RSSITEMCATEGORIES::::RSSITEMSBLOCKEND::::RSSBLOCKEND::";
        $expectedContent = $this->xml->getElementsByTagName("item")->item(0)->getElementsByTagName("category")->item(0)->nodeValue;
        $expectedHtml = $this->htmlbody($expectedContent);

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
    public function rssitem_description()
    {
        $html = "::RSSBLOCK|" . $this->xmlUrl . "|::::RSSITEMSBLOCK|1::::RSSITEMDESCRIPTION::::RSSITEMSBLOCKEND::::RSSBLOCKEND::";
        $expectedContent = $this->xml->getElementsByTagName("item")->item(0)->getElementsByTagName("description")->item(0)->nodeValue;
        $expectedHtml = $this->htmlbody($expectedContent);

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
    public function rssitem_thumbnail()
    {
        $html = "::RSSBLOCK|" . $this->xmlUrl . "|::::RSSITEMSBLOCK|1::::RSSITEMTHUMBNAILURL::::RSSITEMSBLOCKEND::::RSSBLOCKEND::";
        $expectedContent = $this->xml->getElementsByTagName("item")->item(0)->getElementsByTagName("enclosure")->item(0)->getAttribute('url');
        $expectedHtml = $this->htmlbody($expectedContent);

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
    public function rssitem_dateformated_ymd()
    {
        $dateFormat = "Y-m-d";
        $html = "::RSSBLOCK|" . $this->xmlUrl . "|::::RSSITEMSBLOCK|1::::RSSITEMDATE|" . $dateFormat . "|::::RSSITEMSBLOCKEND::::RSSBLOCKEND::";
        $date = Carbon::parse($this->xml->getElementsByTagName("item")->item(0)->getElementsByTagName("pubDate")->item(0)->nodeValue);
        $expectedContent = $date->format($dateFormat);
        $expectedHtml = $this->htmlbody($expectedContent);

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
    public function rssitem_dateformated_ymdhi()
    {
        $dateFormat = "Y-m-d h:i";
        $html = "::RSSBLOCK|" . $this->xmlUrl . "|::::RSSITEMSBLOCK|1::::RSSITEMDATE|" . $dateFormat . "|::::RSSITEMSBLOCKEND::::RSSBLOCKEND::";
        $date = Carbon::parse($this->xml->getElementsByTagName("item")->item(0)->getElementsByTagName("pubDate")->item(0)->nodeValue);
        $expectedContent = $date->format($dateFormat);
        $expectedHtml = $this->htmlbody($expectedContent);

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
    public function rssitem_fullhtml()
    {
        $dateformat = 'Y-m-d h:i';
        $html = '3rd::RSSBLOCK|' . $this->xmlUrl . '|::::RSSITEMSBLOCK|1::<span class="title"><a href="::RSSITEMURL::">::RSSITEMTITLE::</a></span><span class="date">::RSSITEMDATE|' . $dateformat . '|::</span><span class="description">::RSSITEMDESCRIPTION::</span><span class="author">::RSSITEMAUTHOR::</span>::RSSITEMSBLOCKEND::::RSSBLOCKEND::';

        //axpected values
        $title = $this->xml->getElementsByTagName("item")->item(0)->getElementsByTagName("title")->item(0)->nodeValue;
        $url = $this->xml->getElementsByTagName("item")->item(0)->getElementsByTagName("link")->item(0)->nodeValue;
        $date = Carbon::parse($this->xml->getElementsByTagName("item")->item(0)->getElementsByTagName("pubDate")->item(0)->nodeValue);
        $description = $this->xml->getElementsByTagName("item")->item(0)->getElementsByTagName("description")->item(0)->nodeValue;
        $author = $this->xml->getElementsByTagName("item")->item(0)->getElementsByTagName("author")->item(0)->nodeValue;

        $expectedContent = '<span class="title"><a href="'.$url.'">' . $title . '</a></span><span class="date">' . $date->format($dateformat) . '</span><span class="description">' . $description . '</span><span class="author">' . $author . '</span>';
        $expectedHtml = $this->htmlbody($expectedContent);

        /** @var \Spatie\Mailcoach\Models\Campaign */
        $campaign = factory(Campaign::class)->create([
            'html' => $html,
        ]);

        $this->execute($campaign);
        $campaign->refresh();
        $this->assertHtmlWithoutWhitespace($expectedHtml, $campaign->email_html);
    }
}
