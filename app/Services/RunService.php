<?php

namespace App\Services;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


final class RunService
{

    /**
     * ___ Запускаем парсер файлов Excel в Postgres
     * @return string
     */
    public static function runModelsUpdateParser_Rust(): string
    {
        $os = PHP_OS_FAMILY;

        if ($os === 'Windows') {
            $binaryName = 'excel_parser.exe';
            $env = [
                'SYSTEMROOT'  => getenv('SYSTEMROOT'), // Обычно C:\Windows
                'PATH'        => getenv('PATH'),
                'SystemDrive' => getenv('SystemDrive'),
                // Если используешь SSL/TLS, может понадобиться:
                // 'USERPROFILE' => getenv('USERPROFILE'),
            ];
        } else {
            $binaryName = 'excel_parser';
            $env = []; // В Linux обычно доп. переменные не нужны
        }

        $binaryPath = base_path("bin/{$binaryName}");
        $binDirectory = base_path('bin'); // Директория, где лежит бинарник и .env


        // Нам нужно пробросить системные переменные Windows, чтобы заработал Winsock (сеть)


        // 1. Указываем путь к бинарнику
        // 2. Вторым аргументом конструктора Process передаем рабочую директорию ($binDirectory)
        // 3. Третьим аргументом можно передать массив переменных окружения, если нужно

        // Передаем массив $env третьим аргументом
        $process = new Process([$binaryPath], $binDirectory, $env);
        //$process = new Process([$binaryPath], $binDirectory);


        //$process = new Process([$binaryPath], $binDirectory, [
        //    'DATABASE_URL' => 'твой_url_тут',
        //    'PATH' => getenv('PATH'), // Пробрасываем системные пути
        //    'SYSTEMROOT' => getenv('SYSTEMROOT'), // Критично для Windows!
        //]);


        // Увеличиваем таймаут
        $process->setTimeout(60);

        //$process->mustRun();
        //return $process->getOutput();

        //try {
        //    $process->mustRun();
        //
        //    //$output = $process->getOutput();
        //    //$output = $process->getIncrementalOutput();
        //    //return response()->json([
        //    //    'status' => 'success',
        //    //    'data'   => $output,
        //    //]);
        //    return $process->getOutput();
        //} catch (ProcessFailedException $exception) {
        //    throw $exception;
        //    // Выводим подробности, чтобы видеть Error Output от Rust
        //    //return response()->json([
        //    //    'status'  => 'error',
        //    //    'message' => 'Ошибка Rust: ' . $exception->getProcess()->getErrorOutput(),
        //    //    'debug'   => $exception->getMessage()
        //    //], 500);
        //}

        // Запускаем процесс
        $process->run();

        // Если процесс завершился неудачно
        if (!$process->isSuccessful()) {
            $errorOutput = $process->getErrorOutput(); // Получаем то, что Rust выдал в stderr

            // Rust (через anyhow) обычно пишет "Error: Название ошибки"
            // Чистим строку от технических префиксов anyhow
            $cleanError = str_replace(["Error: ", "\n", "\r"], ["", " ", ""], $errorOutput);

            // Убираем лишние пробелы и берем только первую часть до "Caused by"
            $cleanError = explode('Caused by:', $cleanError)[0];

            throw new \Exception(trim($cleanError));
        }

        return $process->getOutput();

    }


}
