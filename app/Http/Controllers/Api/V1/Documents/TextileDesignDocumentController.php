<?php

namespace App\Http\Controllers\Api\V1\Documents;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Documents\Textile\TextileDesignResource;
use App\Models\Manufacture\Documents\TextileDesignDocument;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
//use Illuminate\Http\JsonResponse;

class TextileDesignDocumentController extends Controller
{
    private const TEXTILE_DOCUMENT_FOLDER = 'constructor_pdf';

    /**
     * ___ Загрузка и привязка файла
     * @param Request $request
     * @return string
     */
    public function uploadDocument(Request $request)
    {
        try {
            $request->validate([
                'kdch' => 'required|string',
                'file' => 'required|mimes:pdf|max:100000', // Ограничим 100мб
            ]);

            $kdch = $request->input('kdch');

            // Сохраняем файл в приватный диск (не public), чтобы защитить чертежи
            $path = $request->file('file')->storeAs(
                self::TEXTILE_DOCUMENT_FOLDER,
                "kdch_{$kdch}." . $request->file('file')->getClientOriginalExtension()
            );

            // __ Обновляем или создаем запись в базе
            $document = TextileDesignDocument::query()->updateOrCreate(
                ['kdch' => $kdch],
                ['file_path' => $path]
            );
            //return response()->json(['success' => true, 'document' => $document]);

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
    public function getTextileDesignDocumentByIdBlob(string $id)
    {
        try {
            $validator = Validator::make([
                'id' => $id
            ], [
                'id' => 'required|numeric|exists:textile_design_documents,id'
            ]);
            $validated = $validator->validate(); // __Пробрасываем исключение

            $doc = TextileDesignDocument::query()->where('id', $validated['id'])->firstOrFail();

            if (!Storage::exists($doc->file_path)) {
                throw new Exception('Файл для ' . $doc->kdch . ' не найден на сервере');
                //abort(404, 'Файл физически не найден на сервере');
            }

             // __ Отдаем файл как inline-поток, чтобы браузер открыл его, а не скачивал
            return response()->file(Storage::path($doc->file_path), [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $doc->kdch . '.pdf"'
            ]);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     *   ___ Получаем КДЧ по номеру
     * @param string $kdch
     * @return TextileDesignResource|string
     */
    public function getTextileDesignDocumentByKdch(string $kdch)
    {
        try {
            $validator = Validator::make([
                'kdch' => $kdch
            ], [
                'kdch' => 'required|string'
            ]);
            $validated = $validator->validate(); // __Пробрасываем исключение

            $doc = TextileDesignDocument::query()->where('kdch', $validated['kdch'])->first();
            if (is_null($doc)) {
                return response() -> json(['data' => null]);
            }

            return new TextileDesignResource($doc);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Возвращаем список КДЧ
     * @return AnonymousResourceCollection|string
     */
    public function getDocuments()
    {
        try {
            $docs = TextileDesignDocument::all();
            return TextileDesignResource::collection($docs);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }

    }


    /**
     * ___ Удаляем файл
     * @param Request $request
     * @return string
     */
    public function deleteTextileDesignDocumentById(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|numeric|exists:textile_design_documents,id'
            ]);

            $textileDocument = TextileDesignDocument::query()->where('id', $validated['id'])->firstOrFail();
            $textileDocument->delete();

            return EndPointStaticRequestAnswer::ok('Файл успешно удален');
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }
}
