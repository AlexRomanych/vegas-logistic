<?php

namespace App\Models\Manufacture\Documents;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BlockDesignDocument extends Model
{
    protected $guarded = false;

    /**
     * Хуки жизненного цикла модели
     */
    protected static function booted(): void
    {
        // __ Срабатывает СРАЗУ ПОСЛЕ удаления записи из базы
        static::deleted(function ($document) {
            if ($document->file_path && Storage::exists($document->file_path)) {
                Storage::delete($document->file_path);
            }
        });

        // __ Срабатывает В МОМЕНТ ОБНОВЛЕНИЯ записи (для updateOrCreate)
        static::updating(function ($document) {
            // Если путь к файлу изменился (загрузили новый)
            if ($document->isDirty('file_path')) {
                $oldPath = $document->getOriginal('file_path');
                // Удаляем старый файл, чтобы не плодить мусор
                if ($oldPath && Storage::exists($oldPath)) {
                    Storage::delete($oldPath);
                }
            }
        });
    }
}
