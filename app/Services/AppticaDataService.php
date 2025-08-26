<?php

namespace App\Services;

use App\Models\AppTopPositionByCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class AppticaDataService
{
    private string $baseUrl = 'https://api.apptica.com/package/top_history/1421444/1';
    private string $apiKey = 'fVN5Q9KVOlOHDx9mOsKPAQsFBlEhBOwguLkNEDTZvKzJzT3l';

    /**
     * Загружает данные о позициях приложения для каждой категории из Apptica API за указанный период и сохраняет в БД
     *
     * @param Carbon $from Дата от
     * @param Carbon $to Дата до
     * @return bool true, если данные успешно загружены или отсутствуют; false при ощибке API или БД
     */
    public function fetchAndSave(Carbon $from, Carbon $to): bool
    {
        $data = $this->fetchFromApi($from, $to);
        if (!$data) {
            return false;
        }

        $dataLoad = $this->parseData($data['data']);
        if (empty($dataLoad)) {
            return true;
        }

        return $this->saveToDatabase($dataLoad);
    }

    /**
     * Получает данные от Apptica API за определенный период в виде массива
     *
     * @param Carbon $from Дата от
     * @param Carbon $to Дата до
     * @return array|null Данные от Apptica API в виде массива, либо null
     */
    private function fetchFromApi(Carbon $from, Carbon $to): ?array
    {
        $response = Http::timeout(15)->get($this->baseUrl, [
            'date_from' => $from->format('Y-m-d'),
            'date_to' => $to->format('Y-m-d'),
            'B4NKGg' => $this->apiKey,
        ]);

        if ($response->failed()) {
            return null;
        }

        return $response->json();
    }

    /**
     * Преобразует сырые данные из Apptica API в плоский массив для сохранения в БД
     * Результат — массив записей в формате:
     * [
     *     ['date' => ..., 'category_id' => ..., 'position' => ...],
     *     ['date' => ..., 'category_id' => ..., 'position' => ...],
     *     ...
     * ]
     *
     * @param array $data Сырые данные из Apptica API
     * @return array Итоговый массив
     */
    private function parseData(array $data): array
    {
        $result = [];

        foreach ($data as $categoryId => $subcategories) {
            $bestPosition = [];

            foreach ($subcategories as $subcategoryData) {
                foreach ($subcategoryData as $date => $position) {

                    if (!array_key_exists($date, $bestPosition)) {
                        $bestPosition[$date] = $position;
                        continue;
                    }

                    if (!is_numeric($position)) {
                        continue;
                    }

                    if ($bestPosition[$date] === null) {
                        $bestPosition[$date] = $position;
                        continue;
                    }

                    if ($position < $bestPosition[$date]) {
                        $bestPosition[$date] = $position;
                    }

                }
            }

            foreach ($bestPosition as $date => $position) {
                $result[] = [
                    'date' => $date,
                    'category_id' => $categoryId,
                    'position' => $position,
                ];
            }
        }

        return $result;
    }

    /**
     * Сохраняет prepared data в БД
     *
     * @param array $data
     * @return bool true если успешно, false если нет
     */
    private function saveToDatabase(array $data): bool
    {
        try {
            AppTopPositionByCategory::insert($data);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Возвращает позиции преложения по категорям в указанную дату
     *
     * @param string $date Дата в формате Y-m-d
     * @return array Массив в формате ['category' => 'position']
     */
    public function getPositionsByCategoryOnDate(string $date): array
    {
        return AppTopPositionByCategory::query()
            ->where('date', $date)
            ->pluck('position', 'category_id as category')
            ->toArray();
    }
}
