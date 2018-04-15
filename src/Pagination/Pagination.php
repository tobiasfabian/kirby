<?php

namespace Kirby\Pagination;

use Exception;

/**
 * @package   Kirby Pagination
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   MIT
 */
class Pagination
{

    /**
     * The current page
     *
     * @var integer
     */
    protected $page;

    /**
     * Total number of items
     *
     * @var integer
     */
    protected $total;

    /**
     * The number of items per page
     *
     * @var integer
     */
    protected $limit;

    /**
     * Creates a new pagination object
     * with the given parameters
     *
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->page($params['page'] ?? 1);
        $this->limit($params['limit'] ?? 20);
        $this->total($params['total'] ?? 0);
    }

    /**
     * Getter and setter for the current page
     *
     * @param  int|null $page
     * @return int|Pagination
     */
    public function page(int $page = null)
    {
        if ($page === null) {
            return $this->page;
        }

        $this->page = $page;
        return $this;
    }

    /**
     * Getter and setter for the total number of items
     *
     * @param  int|null       $total
     * @return int|Pagination
     */
    public function total(int $total = null)
    {
        if ($total === null) {
            return $this->total;
        }

        if ($total < 0) {
            throw new Exception('Invalid total number of items: ' . $total);
        }

        $this->total = $total;
        return $this;
    }

    /**
     * Getter and setter for the number of items per page
     *
     * @param  int|null       $limit
     * @return int|Pagination
     */
    public function limit(int $limit = null)
    {
        if ($limit === null) {
            return $this->limit;
        }

        if ($limit < 1) {
            throw new Exception('Invalid pagination limit: ' . $limit);
        }

        $this->limit = $limit;
        return $this;
    }

    /**
     * Returns the index of the first item on the page
     *
     * @return int
     */
    public function start(): int
    {
        return ($this->page() - 1) * $this->limit() + 1;
    }

    /**
     * Returns the index of the last item on the page
     *
     * @return int
     */
    public function end(): int
    {
        $value = ($this->start() - 1) + $this->limit();

        if ($value <= $this->total()) {
            return $value;
        }

        return $this->total();
    }

    /**
     * Returns the total number of pages
     *
     * @return int
     */
    public function pages(): int
    {
        if ($this->total() === 0) {
            return 0;
        }
        return ceil($this->total() / $this->limit());
    }

    /**
     * Returns the first page
     *
     * @return int
     */
    public function firstPage(): int
    {
        return $this->total() === 0 ? 0 : 1;
    }

    /**
     * Returns the last page
     *
     * @return int
     */
    public function lastPage(): int
    {
        return $this->pages();
    }

    /**
     * Returns the offset (i.e. for db queries)
     *
     * @return int
     */
    public function offset(): int
    {
        return $this->start() - 1;
    }

    /**
     * Checks if the given page exists
     *
     * @return boolean
     */
    public function hasPage(int $page): bool
    {
        if ($page <= 0) {
            return false;
        }

        if ($page > $this->pages()) {
            return false;
        }

        return true;
    }

    /**
     * Checks if there are any pages at all
     *
     * @return boolean
     */
    public function hasPages(): bool
    {
        return $this->total() > $this->limit();
    }

    /**
     * Checks if there's a previous page
     *
     * @return boolean
     */
    public function hasPrevPage(): bool
    {
        return $this->page() > 1;
    }

    /**
     * Returns the previous page
     *
     * @return int|null
     */
    public function prevPage()
    {
        return $this->hasPrevPage() ? $this->page() - 1 : null;
    }

    /**
     * Checks if there's a next page
     *
     * @return boolean
     */
    public function hasNextPage(): bool
    {
        return $this->end() < $this->total();
    }

    /**
     * Returns the next page
     *
     * @return int|null
     */
    public function nextPage()
    {
        return $this->hasNextPage() ? $this->page() + 1 : null;
    }

    /**
     * Checks if the current page is the first page
     *
     * @return boolean
     */
    public function isFirstPage(): bool
    {
        return $this->page() === $this->firstPage();
    }

    /**
     * Checks if the current page is the last page
     *
     * @return boolean
     */
    public function isLastPage(): bool
    {
        return $this->page() === $this->lastPage();
    }

    /**
     * Returns an array with all properties
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'page'      => $this->page(),
            'firstPage' => $this->firstPage(),
            'lastPage'  => $this->lastPage(),
            'pages'     => $this->pages(),
            'offset'    => $this->offset(),
            'limit'     => $this->limit(),
            'total'     => $this->total(),
            'start'     => $this->start(),
            'end'       => $this->end(),
        ];
    }
}
