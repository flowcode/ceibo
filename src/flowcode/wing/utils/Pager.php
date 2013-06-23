<?php

namespace flowcode\wing\utils;

/**
 * Description of Pager
 *
 * @author juanma
 */
class Pager {

    private $items = array();
    private $itemCount = 0;
    private $itemsPerPage = 0;
    private $pageCount = 0;
    private $actualPage = 0;

    /**
     * Pager instance.
     * @param array $items Items array.
     * @param integer $itemCount Total items.
     * @param integer $itemsPerPage
     * @param integer $actualPage
     */
    public function __construct($items, $itemCount, $itemsPerPage, $actualPage = 1) {
        $this->items = $items;
        $this->itemsPerPage = $itemsPerPage;
        $this->itemCount = $itemCount;
        $this->actualPage = $actualPage;
        $this->pageCount = ceil($this->itemCount / $this->itemsPerPage);
    }

    public function getItems() {
        return $this->items;
    }

    public function getItemCount() {
        return $this->itemCount;
    }

    public function getPageCount() {
        return $this->pageCount;
    }

    public function getActualPage() {
        return $this->actualPage;
    }

    public function getNextPage() {
        $next = $this->actualPage;
        if ($this->actualPage < $this->pageCount) {
            $next = $this->actualPage + 1;
        }
        return $next;
    }

    public function getPrevPage() {
        $prev = $this->actualPage;
        if ($this->actualPage > 1) {
            $prev = $this->actualPage - 1;
        }
        return $prev;
    }

}

?>
