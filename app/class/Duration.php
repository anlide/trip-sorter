<?php

class Duration {
    /** @var integer $h */
    public $h;

    /** @var integer $m */
    public $m;

    /** @var integer $m */
    private $cacheMinutes = null;

    /**
     * Duration constructor.
     * @param int $h
     * @param int $m
     */
    public function __construct(int $h, int $m)
    {
        $this->h = intval($h);
        $this->m = intval($m);
    }

    public function getMinutes()
    {
        if ($this->cacheMinutes === null) {
            $this->cacheMinutes = $this->m + $this->h * 60;
        }

        return $this->cacheMinutes;
    }
}