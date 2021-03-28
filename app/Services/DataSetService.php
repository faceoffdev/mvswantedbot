<?php

namespace App\Services;

use GuzzleHttp\Client;
use PHPHtmlParser\Dom;

class DataSetService
{
    public const BASE_URI = 'https://data.gov.ua';
    public const DATASET_URI = '/dataset/7c51c4a0-104b-4540-a166-e9fc58485c1b';
    public const DOWNLOAD_URL = '/download/';
    public const FILE_NAME = 'data.json';

    private Client $client;

    private Dom $dom;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => self::BASE_URI]);
        $this->dom = new Dom();
    }

    /**
     * Получить URL для загрузки данных
     */
    public function getUrlDownloadData(): string
    {
        $this->loadHtml();

        $href = '';

        foreach ($this->dom->find('.resource-item') as $item) {
            $p = $item->find('p', 0);

            if ($p && stripos($p->innerHtml, 'масив даних') !== false) {
                $href = $item->find('a', 0)->getAttribute('href');
                break;
            }
        }

        return self::BASE_URI . $href . self::DOWNLOAD_URL . self::FILE_NAME;
    }

    /**
     * Загрузить страницу html
     */
    private function loadHtml(): void
    {
        $response = $this->client->request('GET', self::DATASET_URI);

        $this->dom->load($response->getBody()->getContents());
    }
}
