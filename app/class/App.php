<?php

class App {
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
        $this->deals = [];
        $content = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/../database/data.json');
        $json = json_decode($content);
        if (!isset($json->deals)) {
            throw new Exception('Wrong format: no deals');
        }
        foreach ($json->deals as $sourceDeal) {
            $deal = new Deal();
            $deal->transport = $sourceDeal->transport;
            $deal->departure = $sourceDeal->departure;
            $deal->arrival = $sourceDeal->arrival;
            $deal->duration = new Duration($sourceDeal->duration->h, $sourceDeal->duration->m);
            $deal->cost = $sourceDeal->cost;
            $deal->discount = $sourceDeal->discount;
            $deal->reference = $sourceDeal->reference;
            $this->deals[] = $deal;
        }
    }
}