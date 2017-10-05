<?php

class Duration {
    /** @var integer $h */
    public $h;

    /** @var integer $m */
    public $m;

    /**
     * Duration constructor.
     * @param int $h
     * @param int $m
     */
    public function __construct(int $h, int $m)
    {
        $this->h = $h;
        $this->m = $m;
    }
}