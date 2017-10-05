<?php

class App {
    /** @var Location[] $locations */
    private $locations;

    /** @var Deal[] $deals */
    private $deals;

    public function __construct()
    {
        // Use leading data from file.
        // Downloading and parse all data here.
        $this->initDataFromFileJson();
    }

    private function initDataFromFileJson()
    {
        $this->locations = [];
        $this->deals = [];
    }
}