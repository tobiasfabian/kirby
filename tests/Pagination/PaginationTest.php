<?php

namespace Kirby\Pagination;

use PHPUnit\Framework\TestCase;

class PaginationTest extends TestCase
{

    public function testDefaultPage()
    {
        $pagination = new Pagination();
        $this->assertEquals(1, $pagination->page());
    }

    public function testPageSetter()
    {
        $pagination = new Pagination();
        $this->assertInstanceOf(Pagination::class, $pagination->page(12));
    }

    public function testPageGetter()
    {
        $pagination = new Pagination();
        $pagination->page(12);
        $this->assertEquals(12, $pagination->page());
    }

    public function testTotalDefault()
    {
        $pagination = new Pagination();
        $this->assertEquals(0, $pagination->total());
    }

    public function testTotalSetter()
    {
        $pagination = new Pagination();
        $this->assertInstanceOf(Pagination::class, $pagination->total(12));
    }

    public function testTotalGetter()
    {
        $pagination = new Pagination();
        $pagination->total(12);
        $this->assertEquals(12, $pagination->total());
    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid total number of items: -1
     */
    public function testTotalInvalid()
    {
        $pagination = new Pagination();
        $pagination->total(-1);
    }

    public function testLimitDefault()
    {
        $pagination = new Pagination();
        $this->assertEquals(10, $pagination->limit());
    }

    public function testLimitSetter()
    {
        $pagination = new Pagination();
        $this->assertInstanceOf(Pagination::class, $pagination->limit(100));
    }

    public function testLimitGetter()
    {
        $pagination = new Pagination();
        $pagination->limit(100);
        $this->assertEquals(100, $pagination->limit());
    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid pagination limit: -1
     */
    public function testLimitInvalid()
    {
        $pagination = new Pagination();
        $pagination->limit(-1);
    }

    public function testStart()
    {
        $pagination = new Pagination([
            'total' => 42
        ]);

        $this->assertEquals(1, $pagination->start());

        // go to the second page
        $pagination->page(2);
        $this->assertEquals(11, $pagination->start());

        // set a different limit
        $pagination->limit(20);
        $this->assertEquals(21, $pagination->start());
    }

    public function testEnd()
    {
        $pagination = new Pagination([
            'total' => 42
        ]);

        $this->assertEquals(10, $pagination->end());

        // go to the second page
        $pagination->page(2);
        $this->assertEquals(20, $pagination->end());

        // set a different limit
        $pagination->limit(20);
        $this->assertEquals(40, $pagination->end());
    }

    public function testEndWithOneItem()
    {
        $pagination = new Pagination([
            'total' => 1
        ]);

        $this->assertEquals(1, $pagination->end());
    }

    public function testPages()
    {
        $pagination = new Pagination();
        $this->assertEquals(0, $pagination->pages());

        $pagination = new Pagination(['total' => 1]);
        $this->assertEquals(1, $pagination->pages());

        $pagination = new Pagination(['total' => 10]);
        $this->assertEquals(1, $pagination->pages());

        $pagination = new Pagination(['total' => 11]);
        $this->assertEquals(2, $pagination->pages());

        $pagination = new Pagination(['total' => 11, 'limit' => 5]);
        $this->assertEquals(3, $pagination->pages());
    }

    public function testFirstPage()
    {
        $pagination = new Pagination();
        $this->assertEquals(0, $pagination->firstPage());

        $pagination = new Pagination(['total' => 42]);
        $this->assertEquals(1, $pagination->firstPage());
    }

    public function testLastPage()
    {
        $pagination = new Pagination();
        $this->assertEquals(0, $pagination->lastPage());

        $pagination = new Pagination(['total' => 42]);
        $this->assertEquals(5, $pagination->lastPage());
    }

    public function testOffset()
    {
        $pagination = new Pagination();
        $this->assertEquals(0, $pagination->offset());

        $pagination = new Pagination(['total' => 42, 'page' => 2]);
        $this->assertEquals(10, $pagination->offset());

        $pagination = new Pagination(['total' => 42, 'page' => 2, 'limit' => 20]);
        $this->assertEquals(20, $pagination->offset());
    }

    public function testHasPages()
    {
        $pagination = new Pagination();
        $this->assertFalse($pagination->hasPages());

        $pagination = new Pagination(['total' => 1]);
        $this->assertFalse($pagination->hasPages());

        $pagination = new Pagination(['total' => 11]);
        $this->assertTrue($pagination->hasPages());
    }

    public function testHasPrevPage()
    {
        $pagination = new Pagination();
        $this->assertFalse($pagination->hasPrevPage());

        $pagination = new Pagination(['total' => 42, 'page' => 2]);
        $this->assertTrue($pagination->hasPrevPage());
    }

    public function testHasNextPage()
    {
        $pagination = new Pagination();
        $this->assertFalse($pagination->hasNextPage());

        $pagination = new Pagination(['total' => 42, 'page' => 5]);
        $this->assertFalse($pagination->hasNextPage());

        $pagination = new Pagination(['total' => 42, 'page' => 2]);
        $this->assertTrue($pagination->hasNextPage());
    }

    public function testIsFirstPage()
    {
        $pagination = new Pagination();
        $this->assertFalse($pagination->isFirstPage());

        $pagination = new Pagination(['total' => 42]);
        $this->assertTrue($pagination->isFirstPage());

        $pagination = new Pagination(['total' => 42, 'page' => 2]);
        $this->assertFalse($pagination->isFirstPage());
    }

    public function testIsLastPage()
    {
        $pagination = new Pagination();
        $this->assertFalse($pagination->isLastPage());

        $pagination = new Pagination(['total' => 42]);
        $this->assertFalse($pagination->isLastPage());

        $pagination = new Pagination(['total' => 42, 'page' => 5]);
        $this->assertTrue($pagination->isLastPage());
    }

    public function testToArray()
    {
        $pagination = new Pagination();
        $keys = [
            'page',
            'firstPage',
            'lastPage',
            'pages',
            'offset',
            'limit',
            'total',
            'start',
            'end',
        ];

        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $pagination->toArray());
        }
    }
}
