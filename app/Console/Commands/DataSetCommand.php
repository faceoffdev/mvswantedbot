<?php

namespace App\Console\Commands;

use App\Models\Person;
use App\Services\DataSetService;
use Illuminate\Console\Command;
use JsonMachine\JsonMachine;

class DataSetCommand extends Command
{
    private const ITEMS_CHUNK_LIMIT = 1000;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dataset:load';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Загрузить данные из сервиса data.gov.ua';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $records = JsonMachine::fromFile((new DataSetService())->getUrlDownloadData());
        $data = [];

        foreach ($records as $record) {
            $data[] = [
                'id'            => $record['ID'],
                'ovd'           => $record['OVD'],
                'category'      => $record['CATEGORY'],
                'first_name_u'  => $record['FIRST_NAME_U'],
                'last_name_u'   => $record['LAST_NAME_U'],
                'middle_name_u' => $record['MIDDLE_NAME_U'],
                'first_name_r'  => $record['FIRST_NAME_R'],
                'last_name_r'   => $record['LAST_NAME_R'],
                'middle_name_r' => $record['MIDDLE_NAME_R'],
                'first_name_e'  => $record['FIRST_NAME_E'],
                'last_name_e'   => $record['LAST_NAME_E'],
                'birth_date'    => $record['BIRTH_DATE'],
                'sex'           => $record['SEX'],
                'lost_date'     => $record['LOST_DATE'],
                'lost_place'    => $record['LOST_PLACE'],
                'article_crim'  => $record['ARTICLE_CRIM'],
                'restraint'     => $record['RESTRAINT'],
                'contact'       => $record['CONTACT'],
            ];

            if (count($data) > self::ITEMS_CHUNK_LIMIT) {
                $this->saveData($data);

                $data = [];
            }
        }

        $this->saveData($data);
    }

    /**
     * @param array $data
     */
    private function saveData(array $data): void
    {
        Person::insertOnDuplicateKey($data);
    }
}
