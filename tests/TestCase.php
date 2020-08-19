<?php

namespace Timmoh\MailcoachRssReader\Tests;

use CreateMailcoachTables;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Mailcoach\MailcoachServiceProvider;
use Spatie\TestTime\TestTime;
use Symfony\Component\Process\Process;
use Timmoh\MailcoachRssReader\MailcoachRssReader;
use Timmoh\MailcoachRssReader\MailcoachRssReaderServiceProvider;

class TestCase extends Orchestra {
    /** @var Process */
    protected  $rssHostProcess;
    /**
     * @var string
     */
    protected $xmlUrl;


    public function __construct($name = null, array $data = [], $dataName = '') {
        parent::__construct($name, $data, $dataName);
    }

    public function setUp(): void {
        parent::setUp();

        $this->withFactories(__DIR__ . '/../database/factories');
        //mailcoach Factories
        $this->withFactories(__DIR__ . '/../vendor/spatie/laravel-mailcoach/database/factories');

        $this->withoutExceptionHandling();
        $this->startRssProcess();
        TestTime::freeze();
    }

    public function tearDown(): void {
        parent::tearDown();
        $this->stopRssProcess();
    }

    protected function getPackageProviders($app) {
        return [
            MailcoachServiceProvider::class,
            MailcoachRssReaderServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app) {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite',
            [
                'driver'   => 'sqlite',
                'database' => ':memory:',
                'prefix'   => '',
            ]);

        include_once __DIR__ . '/../vendor/spatie/laravel-mailcoach/database/migrations/create_mailcoach_tables.php.stub';
        (new CreateMailcoachTables())->up();

    }

    protected function getPackageAliases($app) {
        return [
            'MailcoachRssReader' => MailcoachRssReader::class,
        ];
    }


    public function assertMatchesHtmlSnapshotWithoutWhitespace(string $content)
    {
        $contentWithoutWhitespace = preg_replace('/\s/', '', $content);

        $contentWithoutWhitespace = str_replace(PHP_EOL, '', $contentWithoutWhitespace);

        $this->assertMatchesHtmlSnapshot($contentWithoutWhitespace);
    }



    public function startRssProcess() {
        $host = 'localhost:8123';
        $this->xmlUrl = 'http://'.$host.'/rss.xml';
        $this->rssHostProcess = new Process(['php', '-S', $host, '-t', './tests/resources']);
        $this->rssHostProcess->start();
        usleep(500000);
    }

    public function stopRssProcess(){
        $this->rssHostProcess->stop();
    }
}
