<?php

namespace Timmoh\MailcoachRssReader\Facades;

use Illuminate\Support\Facades\Facade;

class MailcoachRssReader extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'mailcoach-rss-reader';
    }
}
