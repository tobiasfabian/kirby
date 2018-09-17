<?php

namespace Kirby\Cms;

use Kirby\Toolkit\F;

class SitePropsTest extends TestCase
{

    public function testUrl()
    {
        $site = new Site([
            'url' => $url = 'https://getkirby.com'
        ]);

        $this->assertEquals($url, $site->url());
    }

    public function testToString()
    {
        $site = new Site(['url' => 'https://getkirby.com']);
        $this->assertEquals('https://getkirby.com', $site->toString('{{ site.url }}'));
    }

    public function testModified()
    {
        $app = new App([
            'roots' => [
                'index'   => $index = __DIR__ . '/fixtures/SitePropsTest/modified',
                'content' => $index
            ]
        ]);

        // create the site file
        F::write($file = $index . '/site.txt', 'test');

        $modified = filemtime($file);
        $site     = $app->site();

        $this->assertEquals($modified, $site->modified());

        // default date handler
        $format = 'd.m.Y';
        $this->assertEquals(date($format, $modified), $site->modified($format));

        // custom date handler
        $format = '%d.%m.%Y';
        $this->assertEquals(strftime($format, $modified), $site->modified($format, 'strftime'));

        Dir::remove($index);
    }

    public function testModifiedInMultilangInstallation()
    {

        $app = new App([
            'roots' => [
                'index'   => $index = __DIR__ . '/fixtures/SitePropsTest/modified',
                'content' => $index
            ],
            'languages' => [
                [
                    'code'    => 'en',
                    'default' => true,
                    'name'    => 'English'
                ],
                [
                    'code'    => 'de',
                    'name'    => 'Deutsch'
                ]
            ]
        ]);

        // create the english site
        F::write($file = $index . '/site.en.txt', 'test');

        $modified = filemtime($file);
        $site     = $app->site();

        $this->assertEquals($modified, $site->modified());

        sleep(1);

        // create the german site
        F::write($file = $index . '/site.de.txt', 'test');

        // change the language
        $app->language = $app->language('de');

        $modified = filemtime($file);
        $site     = $app->site();

        $this->assertEquals($modified, $site->modified());

        Dir::remove($index);

    }

}
