<?php

namespace Timmoh\MailcoachRssReader\Support\Replacers;

use Illuminate\Support\Carbon;
use SimplePie;
use SimplePie_Item;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Support\Replacers\Replacer;
use Illuminate\Support\Str;
use Timmoh\MailcoachRssReader\Facades\MailcoachRssReader;

class RssReplacer implements Replacer {

    public function helpText(): array {
        return [
            'RSSBLOCK:{URL}' => __('This url will display the html of the campaign'),
            'RSSBLOCKEND'    => __('End of the RSS Block'),

            //'RSSRECENT'         => __('Generates links for the 5 posts published previous to those included in your current campaign.'),
            //'RSSRECENT:{COUNT}' => __('Generates links for the posts published previous to those included in your current campaign. Replace {COUNT} with the number of posts you’d like to display.'),

            'RSSFEEDTITLE'       => __('Displays the title of the RSS feed.'),
            'RSSFEEDURL'         => __('Displays the URL for the feed as a whole, if provided.'),
            'RSSFEEDIMAGE'         => __('Displays the logo for the feed, if provided.'),
            'RSSFEEDDESCRIPTION' => __('Displays the feed description, if provided.'),

            'RSSITEMSBLOCK'         => __('The stuff in between those tags gets replicated for each post from the 5 posts published previous your feed.'),
            'RSSITEMSBLOCK:{COUNT}' => __('The stuff in between those tags gets replicated for each post from your feed. Replace {COUNT} with the number of posts you’d like to display.'),
            'RSSITEMSBLOCKEND'      => __('Displays the title of the RSS item.'),

            'RSSITEMTITLE'         => __('Displays the title of the RSS item.'),
            'RSSITEMURL'           => __('Displays the URL for the RSS item.'),
            'RSSITEMDATE'          => __('Displays the publish date of the RSS item. You can also include optional date formatting.'),
            'RSSITEMDATE:#FORMAT#' => __('Displays the publish date of the RSS item. You can also include optional date formatting like d/m/y.'),
            'RSSITEMAUTHOR'        => __('Displays the name of the author for the RSS item, if provided.'),
            'RSSITEMCATEGORIES'    => __('Displays a comma-separated list of the categories (tags and “posted in”) for the RSS item.'),
            'RSSITEMCONTENT'       => __('Displays a short summary of the RSS item content in HTML format.'),
            'RSSITEMDESCRIPTION'   => __('Displays the full content for the RSS item in HTML format, if provided.'),
            'RSSITEMTHUMBNAIL'         => __('Pulls in images from the RSS item. Images are pulled in and displayed at their original size. They can’t be formatted or resized within the campaign.'),

        ];

    }

    private function findRssItemsBlock(string $text, SimplePie $rss) {
        print_r($rss);

        $regex = '::RSSITEMSBLOCK:?([0-9]*)::(.*)::RSSITEMSBLOCKEND::';
        if (preg_match('/' . $regex . '/', $text, $rssItemBlock) === false) {
            return $text;
        }
        if (empty($rssItemBlock[1]) || !is_int($rssItemBlock[1])) {
            $itemCount = 5;
        } else {
            $itemCount = (int)$rssItemBlock;
        }
        $textBefore   = Str::before($text, '::RSSITEMSBLOCK:');
        $textAfter    = Str::afterLast($text, '::RSSITEMSBLOCKEND::');
        $textInner    = $rssItemBlock[2];
        $textNewInner = '';

        if ($rss->get_item_quantity() > 0) {
            for ($i = 0; $i < $itemCount; $i++) {
                /**
                 * @var SimplePie_Item
                 */
                $rssItem = $rss->get_item($i);
                if (!is_null($rssItem)) {
                    $textNewInner .= $this->replaceRssItem($textInner, $rssItem);
                }

            }
            $text = $textBefore . $textNewInner . $textAfter;
        } else {
            $text = $textBefore . $textInner . $textAfter;
        }

        return $text;
    }

    private function replaceRssItem(string $text, SimplePie_Item $rssItem) {
        $text = str_ireplace('::RSSITEMTITLE::', $rssItem->get_title(), $text);
        $text = str_ireplace('::RSSITEMURL::', $rssItem->get_permalink(), $text);
        $text = str_ireplace('::RSSITEMAUTHOR::', $rssItem->get_author(), $text);
        $text = str_ireplace('::RSSITEMCATEGORIES::', $rssItem->get_category(), $text);
        $text = str_ireplace('::RSSITEMCONTENT::', $rssItem->get_content(), $text);
        $text = str_ireplace('::RSSITEMDESCRIPTION::', $rssItem->get_description(), $text);
        $text = str_ireplace('::RSSITEMTHUMBNAIL::', $rssItem->get_thumbnail(), $text);
        $text = str_ireplace('::RSSITEMDATE::', $rssItem->get_date(), $text);

        $regexRssItemDateFormat = '::RSSITEMDATE(:#(.*)#)?::';
        preg_match('/' . $regexRssItemDateFormat . '/', $text, $rssItemDates);
        $itemDate = new Carbon($rssItem->get_date());
        foreach ($rssItemDates as $rssItemDate) {
            if (isset($rssItemDate[2]) && empty($rssItemDate[2])) {
                $outputdate = $itemDate->format($rssItemDate[2]);
            } else {
                $outputdate = $rssItem->get_date();
            }
            $text = str_ireplace($rssItemDate[0], $outputdate, $text);
        }

        return $text;
    }

    private function replaceRssFeed(string $text, SimplePie $rss) {
        $text = str_ireplace('::RSSFEEDTITLE::', $rss->get_title(), $text);
        $text = str_ireplace('::RSSFEEDURL::', $rss->get_permalink(), $text);
        $text = str_ireplace('::RSSFEEDDESCRIPTION::', $rss->get_description(), $text);
        $text = str_ireplace('::RSSFEEDIMAGE::', $rss->get_image_url(), $text);


        return $text;
    }

    private function findRssBlock(string $text) {
        $regexUrl = '(https?:\/\/[www\.]?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b[-a-zA-Z0-9()@:%_\+.~#?&\/\/=]*)';
        $regex    = '::RSSBLOCK:' . $regexUrl . '::(.*)::RSSBLOCKEND::';
        if (preg_match('/' . $regex . '/', $text, $rssBlock) === false) {
            return $text;
        }
        if (empty($rssBlock[1])) { //emtpy url
            return $text;
        }
        $url        = $rssBlock[1];
        $textBefore = Str::before($text, '::RSSBLOCK:' . $url . '::');
        $textAfter  = Str::afterLast($text, '::RSSBLOCKEND::');
        $textInner  = $rssBlock[2];
        try {
            $rss       = MailcoachRssReader::read($url);
            $textInner = $this->replaceRssFeed($textInner, $rss);
        } catch (\Exception $e) {
            dd($e);
        }

        $text = $textBefore . $textInner . $textAfter;
        return $text;
    }

    public function replace(string $text, Campaign $campaign): string {
        return $this->findRssBlock($text);
    }
}
