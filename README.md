# Mailcoach Custom Placeholder

RSS Feed Reader & Replacer for Spatie's awesome Mailcoach (https://mailcoach.app/)
Fetch, parse and add automatically RSS Feed to your campaign 

## Installation

You can install the package via composer:

```bash
composer require timmoh/mailcoach-api-client
```

### Publish Resources:
All Resources:
```bash
php artisan vendor:publish --tag=mailcoach-rss-reader-config
```

## Usage

Add RssReplacer::class to config/mailcoach.php
```php
'replacers' => [
    ...
     \Timmoh\MailcoachRssReader\Support\Replacers\RssReplacer::class,
    ...
],
```


### XML-TEMPLATE
```xml
<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0" 
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:media="http://search.yahoo.com/mrss/"
	>
	<channel>
		<title>::RSSFEEDTITLE::</title>
		<link>::RSSFEEDURL::</link>
		<description>::RSSFEEDDESCRIPTION::</description>
		<item>
			<title>::RSSITEMTITLE::</title>
			<link>::RSSITEMURL::</link>
			<pubDate>::RSSITEMDATE::</pubDate>
			<author>::RSSITEMAUTHOR::</author>
			<category>::RSSITEMCATEGORIES::</category>
			<description>::RSSITEMDESCRIPTION::</description>
			<thumbnail>::RSSITEMTHUMBNAILURL::</thumbnail>
		</item>
	</channel>
</rss>
```

### Example
Used XML:
```xml
<item>
    <title>My Rss item title</title>
    <description>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam</description>
    <thumbnail>http://www.foo.bar/mythumb.jpg</thumbnail>
    <link>http://www.foo.bar/xyz.htm</link>
    <comments>http://www.foo.bar/forum</comments>
    <pubDate>Tue, 19 Oct 2004 11:09:11 -0400</pubDate>
    <creator>Mr Super</creator>
</item>
```
Replacer Code:
```html
<div>::RSSBLOCK|https://www.xyz.de/zxy.xml|::::RSSITEMSBLOCK|1::<span class="title"><a href="::RSSITEMURL::">::RSSITEMTITLE::</a></span><span class="date">::RSSITEMDATE:|y-m-d h:i|::</span><img src="::RSSITEMTHUMBNAILURL::"><span class="description">::RSSITEMDESCRIPTION::</span>::RSSITEMSBLOCKEND::::RSSBLOCKEND::</div>
```
Output:
```html
<div>
    <span class="title"><a href="http://www.foo.bar/xyz.htm">My Rss item title</a></span>
    <span class="date">2004-10-19 11:09</span>
    <img src="http://www.foo.bar/mythumb.jpg">
    <span class="content">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam</span>
</div>
```


### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email timo@doersching.net instead of using the issue tracker.

## Credits

- [Timo Dörsching](https://github.com/timmoh)
- [All Contributors](../../contributors)

This package was inspired by [the Laravel Feed Reader](https://github.com/vedmant/laravel-feed-reader) by [Vedmant](https://github.com/vedmant).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
