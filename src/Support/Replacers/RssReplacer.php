<?php

namespace Timmoh\MailcoachRssReader\Support\Replacers;

use Illuminate\Support\Carbon;
use SimplePie;
use SimplePie_Enclosure;
use SimplePie_Item;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Support\Replacers\Replacer;
use Illuminate\Support\Str;
use Timmoh\MailcoachRssReader\Facades\MailcoachRssReader;

class RssReplacer implements Replacer {

    public function helpText(): array {
        return [
            'RSSBLOCK|{URL}|' => __('This url will display the html of the campaign'),
            'RSSBLOCKEND'     => __('End of the RSS Block'),

            //TODO Cooming soon
            //'RSSRECENT'         => __('Generates links for the 5 posts published previous to those included in your current campaign.'),
            //'RSSRECENT:{COUNT}' => __('Generates links for the posts published previous to those included in your current campaign. Replace {COUNT} with the number of posts you’d like to display.'),

            'RSSFEEDTITLE'       => __('Displays the title of the RSS feed.'),
            'RSSFEEDURL'         => __('Displays the URL for the feed as a whole, if provided.'),
            'RSSFEEDIMAGE'       => __('Displays the logo for the feed, if provided.'),
            'RSSFEEDDESCRIPTION' => __('Displays the feed description, if provided.'),

            'RSSITEMSBLOCK'         => __('The stuff in between those tags gets replicated for each post from the 5 posts published previous your feed.'),
            'RSSITEMSBLOCK|{COUNT}' => __('The stuff in between those tags gets replicated for each post from your feed. Replace {COUNT} with the number of posts you’d like to display.'),
            'RSSITEMSBLOCKEND'      => __('Displays the title of the RSS item.'),

            'RSSITEMTITLE'          => __('Displays the title of the RSS item.'),
            'RSSITEMURL'            => __('Displays the URL for the RSS item.'),
            'RSSITEMDATE'           => __('Displays the publish date of the RSS item. You can also include optional date formatting.'),
            'RSSITEMDATE|{FORMAT}|' => __('Displays the publish date of the RSS item. You can also include optional date formatting like d/m/y.'),
            'RSSITEMAUTHOR'         => __('Displays the name of the author for the RSS item, if provided.'),
            'RSSITEMCATEGORIES'     => __('Displays a comma-separated list of the categories (tags and “posted in”) for the RSS item.'),
            'RSSITEMCONTENT'        => __('Displays a short summary of the RSS item content in HTML format.'),
            'RSSITEMDESCRIPTION'    => __('Displays the full content for the RSS item in HTML format, if provided.'),
            'RSSITEMTHUMBNAILURL'   => __('Displays the URL for the RSS item thumbnail.'),

        ];

    }

    private function findRssItemsBlock(string $text, SimplePie $rss) {
        $regex = '::RSSITEMSBLOCK\|?([0-9]*)::(.*)::RSSITEMSBLOCKEND::';
        if (preg_match('/' . $regex . '/s', $text, $rssItemBlock) === false) {
            return $text;
        }
        if (count($rssItemBlock) == 0) {
            return $text;
        }
        if (empty($rssItemBlock[1])) {
            $itemCount = 5;
        } else {
            $itemCount = (int)$rssItemBlock[1];
        }
        $textBefore   = Str::before($text, '::RSSITEMSBLOCK');
        $textAfter    = Str::afterLast($text, '::RSSITEMSBLOCKEND::');
        $textInner    = $rssItemBlock[2];
        $textNewInner = '';

        if ($rss->get_item_quantity() == 0) {
            return $text = $textBefore . $textInner . $textAfter;
        }

        foreach ($rss->get_items(0, $itemCount) as $rssItem) {
            /**
             * @var $rssItem SimplePie_Item
             */
            $textNewInner .= $this->replaceRssItem($textInner, $rssItem);
        }
        $text = $textBefore . $textNewInner . $textAfter;

        return $text;
    }

    private function getThumbnail(SimplePie_Item $rssItem,$key=0) {
        $thumbnail = $rssItem->get_thumbnail();
        if(!empty($thumbnail)) {
            return $thumbnail;
        }

        /**
         * @var SimplePie_Enclosure
         */
        $enclosure = $rssItem->get_enclosure($key);
        if(empty($enclosure)) {
            return null;
        }
        $thumbnail = $enclosure->get_thumbnail();
        if(!empty($thumbnail)) {
            return $thumbnail;
        }
        $thumbnail = $enclosure->get_link();
        if(!empty($thumbnail)) {
            return $thumbnail;
        }

        return null;
    }

    private function replaceRssItemDate(string $text, SimplePie_Item $rssItem) {
        $regexRssItemDateFormat = '::RSSITEMDATE(\|(.*)\|)?::';
        if (preg_match('/' . $regexRssItemDateFormat . '/', $text, $rssItemDates)) {
            $itemDate = new Carbon($rssItem->get_date());
            if (isset($rssItemDates[2]) && !empty($rssItemDates[2])) {
                $format     = $rssItemDates[2];
                $outputdate = $itemDate->format($rssItemDates[2]);
            } else {
                $outputdate = $rssItem->get_date();
            }
            $text = str_ireplace($rssItemDates[0], $outputdate, $text);
        }
        return $text;
    }

    private function replaceRssItemAuthor(string $text, SimplePie_Item $rssItem) {
        //$text = str_ireplace('::RSSITEMAUTHOR::', $rssItem->get_author(), $text);
        $text = preg_replace_callback(
            '|(::RSSITEMAUTHOR::)|',
            function ($treffer) use ($rssItem) {
                $author = $rssItem->get_author();
                if (!$author) {
                    return '';
                }
                return $author->get_email();
            },
            $text
        );
        return $text;
    }

    private function replaceRssItem(string $text, SimplePie_Item $rssItem) {
        $text = str_ireplace('::RSSITEMTITLE::', $rssItem->get_title(), $text);
        $text = str_ireplace('::RSSITEMURL::', $rssItem->get_permalink(), $text);

        $text = str_ireplace('::RSSITEMTHUMBNAILURL::', $this->getThumbnail($rssItem), $text);
        $text = str_ireplace('::RSSITEMCATEGORIES::', $rssItem->get_category()->get_term(), $text);
        $text = str_ireplace('::RSSITEMCONTENT::', $rssItem->get_content(true), $text);
        $text = str_ireplace('::RSSITEMDESCRIPTION::', $rssItem->get_description(), $text);

        $text = str_ireplace('::RSSITEMDATE::', $rssItem->get_date(), $text);
        $text = $this->replaceRssItemAuthor($text, $rssItem);
        $text = $this->replaceRssItemDate($text, $rssItem);

        return $text;
    }

    private function replaceRssFeed(string $text, SimplePie $rss) {
        $text = str_ireplace('::RSSFEEDTITLE::', $rss->get_title(), $text);
        $text = str_ireplace('::RSSFEEDURL::', $rss->get_permalink(), $text);
        $text = str_ireplace('::RSSFEEDDESCRIPTION::', $rss->get_description(), $text);
        $text = str_ireplace('::RSSFEEDIMAGE::', $rss->get_image_url(), $text);

        return $text;
    }

    private function extractRssBlock(array $rssBlock, string $text) {
        if (count($rssBlock) != 4) {
            return $rssBlock[0];
        }

        if (empty($rssBlock[1])) { //emtpy url
            return $rssBlock[0];
        }
        $url        = $rssBlock[1];
        $textBefore = Str::before($text, '::RSSBLOCK|' . $url . '|::');
        $textAfter  = Str::afterLast($text, '::RSSBLOCKEND::');
        $textInner  = $rssBlock[3];
        try {
            $rss = MailcoachRssReader::read($url);
            $rss->init();
            $textInner = $this->replaceRssFeed($textInner, $rss);
            $textInner = $this->findRssItemsBlock($textInner, $rss);
        } catch (\Exception $e) {
            dd($e);
        }
        return $text = $textBefore . $textInner . $textAfter;
    }

    private function findRssBlock(string $text) {
        //$regexUrl = 'https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)';
        //$regexUrl = '(https?:\/\/[www\.]?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b[-a-zA-Z0-9()@%_\+.~#?&\/\/=]*)';
        $regexUrl = '(https?:\/\/[www\.]?[-a-zA-Z0-9@:%._\+~#=]{1,256}(\.[a-zA-Z0-9()]{1,6})?\b[-a-zA-Z0-9()@%_\+.~#?&\/\/=]*)';

        //$regexUrl = '(https?:\/\/[www\.]?[-a-zA-Z0-9@%._\+~#=]{1,256}:{0,1}[-a-zA-Z0-9@%._\+~#=]{1,256}(\.[a-zA-Z0-9()]{1,6})?\b[-a-zA-Z0-9()@%_\+.~#?&\/\/=]*)';
        //$regexUrl = '(\b(([\w-]+:\/\/?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|\/))))';
        $regex = '::RSSBLOCK\|' . $regexUrl . '\|::(.*)::RSSBLOCKEND::';
        if (preg_match_all('/' . $regex . '/s', $text, $rssBlocks, PREG_SET_ORDER) === false) {
            return $text;
        }
        foreach ($rssBlocks as $rssBlock) {
            $text = $this->extractRssBlock($rssBlock, $text);
        }
        return $text;
    }

    public function replace(string $text, Campaign $campaign): string {
        return $this->findRssBlock($text);
    }
}
