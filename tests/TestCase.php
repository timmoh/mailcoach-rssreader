<?php

namespace Timmoh\MailcoachRssReader\Tests;

use CreateMailcoachTables;
use DOMDocument;
use Illuminate\Foundation\Testing\WithFaker;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Mailcoach\MailcoachServiceProvider;
use Spatie\TestTime\TestTime;
use Symfony\Component\Process\Process;
use Timmoh\MailcoachRssReader\MailcoachRssReader;
use Timmoh\MailcoachRssReader\MailcoachRssReaderServiceProvider;

class TestCase extends Orchestra
{
    use WithFaker;

    /** @var Process */
    protected static $rssHostProcess;

    /**
     * @var string
     */
    protected $xmlUrl;

    /**
     * @var DOMDocument
     */
    protected $xml;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->withFactories(__DIR__ . '/../database/factories');
        //mailcoach Factories
        $this->withFactories(__DIR__ . '/../vendor/spatie/laravel-mailcoach/database/factories');

        $this->withoutExceptionHandling();
        $this->startRssProcess();
        TestTime::freeze();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->stopRssProcess();
    }

    protected function getPackageProviders($app)
    {
        return [
            MailcoachServiceProvider::class,
            MailcoachRssReaderServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set(
            'database.connections.sqlite',
            [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ]
        );

        include_once __DIR__ . '/../vendor/spatie/laravel-mailcoach/database/migrations/create_mailcoach_tables.php.stub';
        (new CreateMailcoachTables())->up();
    }

    protected function getPackageAliases($app)
    {
        return [
            'MailcoachRssReader' => MailcoachRssReader::class,
        ];
    }

    private function stripWhitespace(string $text)
    {
        $contentWithoutWhitespace = preg_replace('/\s/', '', $text);
        $contentWithoutWhitespace = str_replace(PHP_EOL, '', $contentWithoutWhitespace);

        return $contentWithoutWhitespace;
    }

    public function assertHtmlWithoutWhitespace(string $expected, string $actual)
    {
        $expected = $this->stripWhitespace($expected);
        $actual = $this->stripWhitespace($actual);

        $this->assertEquals($expected, $actual);
    }

    public function assertMatchesHtmlSnapshotWithoutWhitespace(string $content)
    {
        $contentWithoutWhitespace = preg_replace('/\s/', '', $content);

        $contentWithoutWhitespace = str_replace(PHP_EOL, '', $contentWithoutWhitespace);

        $this->assertMatchesHtmlSnapshot($contentWithoutWhitespace);
    }

    public function startRssProcess()
    {
        $this->generateXmlFile();
        $host = 'localhost:8123';
        $this->xmlUrl = 'http://'.$host.'/rss_gen.xml';
        self::$rssHostProcess = new Process(['php', '-S', $host, '-t', __DIR__.'/resources']);
        self::$rssHostProcess->start();
        usleep(500000);
    }

    public function stopRssProcess()
    {
        self::$rssHostProcess->stop();
    }

    private function generateXmlFile(int $itemsCount = 10)
    {
        $xml = new DOMDocument('1.0', 'utf-8');
        $xml->formatOutput = true;
        $rss = $xml->createElement('rss');
        $rss->setAttribute('version', '2.0');
        $xml->appendChild($rss);

        $channel = $xml->createElement('channel');
        $rss->appendChild($channel);

        // Head des Feeds
        $head = $xml->createElement('title', $this->faker->text);
        $channel->appendChild($head);

        $head = $xml->createElement('description', $this->faker->text);
        $channel->appendChild($head);

        $head = $xml->createElement('language', 'de');
        $channel->appendChild($head);

        $head = $xml->createElement('link', $this->faker->url);
        $channel->appendChild($head);


        //image
        $image = $xml->createElement('image');
        $channel->appendChild($image);
        $data = $xml->createElement('url', $this->faker->imageUrl());
        $image->appendChild($data);
        $data = $xml->createElement('title', $this->faker->text);
        $image->appendChild($data);
        $data = $xml->createElement('link', $this->faker->url);
        $image->appendChild($data);
        $data = $xml->createElement('width', $this->faker->randomDigit);
        $image->appendChild($data);
        $data = $xml->createElement('height', $this->faker->randomDigit);
        $image->appendChild($data);
        $data = $xml->createElement('description', $this->faker->text);
        $image->appendChild($data);



        $head = $xml->createElement('category', $this->faker->words(1, true));
        $channel->appendChild($head);

        // Aktuelle Zeit, falls time() in MESZ ist, muss 1 Stunde abgezogen werden
        $head = $xml->createElement('lastBuildDate', date("D, j M Y H:i:s ", $this->faker->unixTime()).' GMT');
        $channel->appendChild($head);


        for ($i = 0;$i < $itemsCount;$i++) {
            $head = $xml->createElement('comments', $this->faker->url);
            $channel->appendChild($head);

            $item = $xml->createElement('item');
            $channel->appendChild($item);

            $data = $xml->createElement('title', utf8_encode($this->faker->text(50)));
            $item->appendChild($data);

            $data = $xml->createElement('description', utf8_encode($this->faker->text));
            $item->appendChild($data);


            $data = $xml->createElement('enclosure');
            $data->setAttribute('url', $this->faker->imageUrl());
            $data->setAttribute('type', 'image/jpeg');
            $data->setAttribute('medium', 'image');
            $data->setAttribute('height', 150);
            $data->setAttribute('width', 150);
            $item->appendChild($data);

            $data = $xml->createElement('link', $this->faker->url);
            $item->appendChild($data);

            $data = $xml->createElement('creator', $this->faker->name);
            $item->appendChild($data);

            $data = $xml->createElement('dc', $this->faker->name);
            //$data->
            $item->appendChild($data);


            $head = $xml->createElement('category', $this->faker->words(1, true));
            $item->appendChild($head);

            $author = $xml->createElement('author', $this->faker->email);
            $item->appendChild($author);

            /*$author = $xml->createElement('author');
            $item->appendChild($author);
            $data = $xml->createElement('name', $this->faker->name);
            $author->appendChild($data);
            $data = $xml->createElement('email', $this->faker->email);
            $author->appendChild($data);*/

            $data = $xml->createElement('pubDate', date("D, j M Y H:i:s ", $this->faker->unixTime()).' GMT');
            $item->appendChild($data);

            $data = $xml->createElement('guid', $this->faker->url);
            $item->appendChild($data);
        }
        $this->xml = $xml;
        $xml->save(__DIR__.'/resources/rss_gen.xml');
    }
}
