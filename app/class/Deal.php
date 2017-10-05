<?php

class Deal {
    /** @var string $transport - (train|bus|car|...) */
    private $transport;

    /** @var Location $departure - link */
    private $departure;

    /** @var Location $arrival - link */
    private $arrival;

    /** @var Duration $duration - object */
    private $duration;

    /** @var integer $cost */
    private $cost;

    /** @var integer $discount */
    private $discount;

    /** @var string $reference */
    private $reference;
}