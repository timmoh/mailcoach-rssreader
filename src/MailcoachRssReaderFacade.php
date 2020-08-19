<?php

namespace Timmoh\MailcoachRssReader;

use Illuminate\Support\Facades\Facade;

class MailcoachRssReaderFacade extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'mailcoach-rss-reader';
    }
}
