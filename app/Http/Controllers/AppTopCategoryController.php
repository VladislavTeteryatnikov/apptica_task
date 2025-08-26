<?php

namespace App\Http\Controllers;

use App\Services\AppticaDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppTopCategoryController extends Controller
{
    public function __invoke(Request $request, AppticaDataService $service)
    {
        // Првоерка, что передан только один параметр date
        if (count($request->query()) != 1 || !$request->has('date')) {
            return response()->json([
                'status_code' => 400,
                'message' => "fail",
                'error' => "Only parameter 'date' is accepted"
            ], 400);
        }

        // Валидируем date
        $validator = Validator::make($request->query(), [
            'date' => 'required|date|date_format:Y-m-d'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => 'fail',
                'error' => 'Invalid date'
            ], 400);
        }

        // Получаем данные по категорям и позициям для опр. даты
        $date = $request->date;
        $positions = $service->getPositionsByCategoryOnDate($date);

        return response()->json([
            'status_code' => 200,
            'message' => 'ok',
            'data' => $positions
        ]);
    }
}
