<?php

class Way {
    /** @var Deal[] $deals - it should be array of link (not object) */
    public $deals;

    /** @var float $proce - sum of deal prices (with discount) */
    public $price;

    /** @var integer $minutes - amount of minutes */
    public $duration;

    /**
     * @param Deal $deal - this is link to object
     */
    public function addDeal(Deal &$deal)
    {
        $this->deals[] = $deal;
        $this->price += $deal->cost * ((100 - $deal->discount) / 100);
        $this->duration += $deal->duration->getMinutes();
    }
}