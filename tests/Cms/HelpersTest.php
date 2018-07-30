<?php

namespace Kirby\Cms;

use Kirby\Cms\App as Kirby;
use Kirby\Http\Server;

class HelpersTest extends TestCase
{

    public function setUp()
    {
        $kirby = new Kirby([
            'urls' => [
                'index' => 'https://getkirby.com'
            ]
        ]);
    }

    public function testCollectionHelper()
    {
        $app = new App([
            'site' => [
                'children' => [
                    ['slug' => 'test']
                ]
            ],
            'collections' => [
                'test' => function ($pages) {
                    return $pages;
                }
            ]
        ]);

        $collection = collection('test');

        $this->assertCount(1, $collection);
        $this->assertEquals('test', $collection->first()->slug());
    }

    public function testCssHelper()
    {
        $result   = css('assets/css/index.css');
        $expected = '<link href="https://getkirby.com/assets/css/index.css" rel="stylesheet">';

        $this->assertEquals($expected, $result);
    }

    public function testCssHelperWithMediaOption()
    {
        $result   = css('assets/css/index.css', 'print');
        $expected = '<link href="https://getkirby.com/assets/css/index.css" media="print" rel="stylesheet">';

        $this->assertEquals($expected, $result);
    }

    public function testCssHelperWithAttrs()
    {
        $result   = css('assets/css/index.css', ['integrity' => 'nope']);
        $expected = '<link href="https://getkirby.com/assets/css/index.css" integrity="nope" rel="stylesheet">';

        $this->assertEquals($expected, $result);
    }

    public function testDumpHelperOnCli()
    {
        $this->assertEquals("test\n", dump('test', false));
    }

    public function testDumpHelperOnServer()
    {
        Server::$cli = false;
        $this->assertEquals("<pre>test</pre>", dump('test', false));
        Server::$cli = null;
    }

    public function testImageHelper()
    {
        $app = new App([
            'site' => [
                'files' => [
                    ['filename' => 'sitefile.jpg']
                ],
                'children' => [
                    [
                        'slug' => 'test',
                        'files' => [
                            ['filename' => 'pagefile.jpg']
                        ]
                    ]
                ]
            ]
        ]);

        $image = image('test/pagefile.jpg');
        $this->assertInstanceOf(File::class, $image);

        $image = image('/sitefile.jpg');
        $this->assertInstanceOf(File::class, $image);

        // get the first image of the current page
        $app->site()->visit('test');
        $image = image();
        $this->assertInstanceOf(File::class, $image);
    }

    public function testJsHelper()
    {
        $result   = js('assets/js/index.js');
        $expected = '<script src="https://getkirby.com/assets/js/index.js"></script>';

        $this->assertEquals($expected, $result);
    }

    public function testJsHelperWithAsyncOption()
    {
        $result   = js('assets/js/index.js', true);
        $expected = '<script async src="https://getkirby.com/assets/js/index.js"></script>';

        $this->assertEquals($expected, $result);
    }

    public function testJsHelperWithAttrs()
    {
        $result   = js('assets/js/index.js', ['integrity' => 'nope']);
        $expected = '<script integrity="nope" src="https://getkirby.com/assets/js/index.js"></script>';

        $this->assertEquals($expected, $result);
    }

    public function testKirbyHelper()
    {
        $app = new App();
        $this->assertEquals($app, kirby());
    }

    public function testOptionHelper()
    {
        $app = new App([
            'options' => [
                'foo' => 'bar'
            ]
        ]);

        $this->assertEquals('bar', option('foo'));
        $this->assertEquals('fallback', option('does-not-exist', 'fallback'));
    }

    public function testPageHelper()
    {
        $app = new App([
            'site' => [
                'children' => [
                    [
                        'slug' => 'home'
                    ],
                    [
                        'slug' => 'test',
                    ]
                ]
            ]
        ]);

        // get the current page without browsing
        $page = page();
        $this->assertEquals('home', $page->slug());

        // get the current page after changing the current page
        $app->site()->visit('test');
        $page = page();
        $this->assertEquals('test', $page->slug());

        // get a specific page
        $page = page('test');
        $this->assertEquals('test', $page->slug());
    }

    public function testPagesHelper()
    {
        $app = new App([
            'site' => [
                'children' => [
                    [
                        'slug' => 'a'
                    ],
                    [
                        'slug' => 'b',
                    ]
                ]
            ]
        ]);

        $pages = pages('a', 'b');
        $this->assertCount(2, $pages);
    }

    public function testRHelper()
    {
        $this->assertEquals('a', r(1 === 1, 'a', 'b'));
        $this->assertEquals('b', r(1 === 2, 'a', 'b'));
        $this->assertEquals(null, r(1 === 2, 'a'));
    }

    public function testSiteHelper()
    {
        $app  = new App();
        $site = $app->site();

        $this->assertEquals($site, site());
    }

    public function testUrlHelper()
    {
        $app = new App([
            'urls' => [
                'index' => $url = 'https://getkirby.com'
            ]
        ]);

        $this->assertEquals($url . '/test', url('test'));
        $this->assertEquals($url . '/test', u('test'));
    }

}
