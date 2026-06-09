<?php

namespace App\Http\Controllers\Api\V1\Documents;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Documents\Blocks\BlockDesignResource;
use App\Models\Manufacture\Documents\BlockDesignDocument;
//use App\Models\Manufacture\Documents\TextileDesignDocument;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

//use Illuminate\Http\JsonResponse;

class BlockDesignDocumentController extends Controller
{
    private const BLOCKS_DOCUMENT_FOLDER = 'blocks_pdf';

    /**
     * ___ Загрузка и привязка файла
     * @param Request $request
     * @return string
     */
    public function uploadDocument(Request $request)
    {
        try {
            $request->validate([
                'kdb'  => 'required|string',
                'file' => 'required|mimes:pdf|max:100000', // Ограничим 100мб
            ]);

            $kdb = $request->input('kdb');

            // Сохраняем файл в приватный диск (не public), чтобы защитить чертежи
            $path = $request->file('file')->storeAs(
                self::BLOCKS_DOCUMENT_FOLDER,
                "kdb_{$kdb}." . $request->file('file')->getClientOriginalExtension()
            );

            // __ Обновляем или создаем запись в базе
            $document = BlockDesignDocument::query()->updateOrCreate(
                ['kdb' => $kdb],
                ['file_path' => $path]
            );

            return EndPointStaticRequestAnswer::ok('Файл успешно загружен');
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Стрим PDF во фронтенд
     * @param string $id
     * @return string|BinaryFileResponse
     */
    public function getBlockDesignDocumentByIdBlob(string $id)
    {
        try {
            $validator = Validator::make([
                'id' => $id
            ], [
                'id' => 'required|numeric|exists:block_design_documents,id'
            ]);
            $validated = $validator->validate(); // __Пробрасываем исключение

            $doc = BlockDesignDocument::query()->where('id', $validated['id'])->firstOrFail();

            if (!Storage::exists($doc->file_path)) {
                throw new Exception('Файл для ' . $doc->kdb . ' не найден на сервере');
                //abort(404, 'Файл физически не найден на сервере');
            }

            // __ Отдаем файл как inline-поток, чтобы браузер открыл его, а не скачивал
            return response()->file(Storage::path($doc->file_path), [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $doc->kdb . '.pdf"'
            ]);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     *   ___ Получаем КДБ по номеру
     * @param string $kdb
     * @return BlockDesignResource|JsonResponse|string
     */
    public function getBlockDesignDocumentByKdb(string $kdb)
    {
        try {
            $validator = Validator::make([
                'kdb' => $kdb
            ], [
                'kdb' => 'required|string'
            ]);
            $validated = $validator->validate(); // __Пробрасываем исключение

            $doc = BlockDesignDocument::query()->where('kdb', $validated['kdb'])->first();
            if (is_null($doc)) {
                return response()->json(['data' => null]);
            }

            return new BlockDesignResource($doc);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Возвращаем список КДБ
     * @return AnonymousResourceCollection|string
     */
    public function getDocuments()
    {
        try {
            $docs = BlockDesignDocument::all();
            return BlockDesignResource::collection($docs);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Удаляем файл
     * @param Request $request
     * @return string
     */
    public function deleteBlockDesignDocumentById(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|numeric|exists:block_design_documents,id'
            ]);

            $textileDocument = BlockDesignDocument::query()->where('id', $validated['id'])->firstOrFail();
            $textileDocument->delete();

            return EndPointStaticRequestAnswer::ok('Файл успешно удален');
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }
}
