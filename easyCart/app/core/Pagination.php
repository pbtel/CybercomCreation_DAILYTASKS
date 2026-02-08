<?php

/**
 * Pagination Helper Class
 * Handles pagination logic and link generation
 */
class Pagination
{
    private $totalItems;
    private $itemsPerPage;
    private $currentPage;
    private $totalPages;
    private $baseUrl;
    private $queryParam;

    public function __construct($totalItems, $itemsPerPage = 24, $currentPage = 1, $baseUrl = '', $queryParam = 'page')
    {
        $this->totalItems = $totalItems;
        $this->itemsPerPage = $itemsPerPage;
        $this->currentPage = (int) $currentPage;
        if ($this->currentPage < 1)
            $this->currentPage = 1;

        $this->totalPages = ceil($this->totalItems / $this->itemsPerPage);
        if ($this->currentPage > $this->totalPages && $this->totalPages > 0) {
            $this->currentPage = $this->totalPages;
        }

        $this->baseUrl = $baseUrl;
        $this->queryParam = $queryParam;
    }

    public function getOffset()
    {
        return ($this->currentPage - 1) * $this->itemsPerPage;
    }

    public function getLimit()
    {
        return $this->itemsPerPage;
    }

    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    public function getTotalPages()
    {
        return $this->totalPages;
    }

    public function isRequired()
    {
        return $this->totalPages > 1;
    }

    /**
     * Generate the page numbers to display based on user requirements
     * Logic:
     * - If total pages <= 7: show all
     * - If total pages > 7:
     *   - If near start: 1,2,3,4,5,6,7 ... L-2,L-1,L
     *   - If in middle: 1,2,3 ... C-2,C-1,C,C+1,C+2 ... L-2,L-1,L
     *   - If near end: 1,2,3 ... L-6,L-5,L-4,L-3,L-2,L-1,L
     */
    public function getPageRange()
    {
        $total = $this->totalPages;
        $current = $this->currentPage;
        $range = [];

        if ($total <= 7) {
            for ($i = 1; $i <= $total; $i++) {
                $range[] = $i;
            }
            return $range;
        }

        // More than 7 pages
        if ($current <= 4) {
            // Near start
            for ($i = 1; $i <= 7; $i++) {
                $range[] = $i;
            }
            $range[] = '...';
            $range[] = $total - 2;
            $range[] = $total - 1;
            $range[] = $total;
        } elseif ($current >= $total - 3) {
            // Near end
            $range[] = 1;
            $range[] = 2;
            $range[] = 3;
            $range[] = '...';
            for ($i = $total - 6; $i <= $total; $i++) {
                $range[] = $i;
            }
        } else {
            // Middle
            $range[] = 1;
            $range[] = 2;
            $range[] = 3;
            $range[] = '...';
            for ($i = $current - 2; $i <= $current + 2; $i++) {
                $range[] = $i;
            }
            $range[] = '...';
            $range[] = $total - 2;
            $range[] = $total - 1;
            $range[] = $total;
        }

        return $range;
    }

    public function getUrl($page)
    {
        // Get current URL parts
        $urlParts = parse_url($_SERVER['REQUEST_URI']);
        $path = $urlParts['path'];
        $query = [];

        if (isset($urlParts['query'])) {
            parse_str($urlParts['query'], $query);
        }

        // Update or set the page parameter
        $query[$this->queryParam] = $page;

        return $path . '?' . http_build_query($query);
    }
}
