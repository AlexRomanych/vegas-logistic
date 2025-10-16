<?php
// ___ Разные логические сервисы или функции ___


namespace App\Services;


use Generator;

class LogicalService
{

    /**
     * Генерирует все возможные перестановки массива нерекурсивным способом.
     * Использует алгоритм лексикографического порядка.
     * @param array $elements Исходный массив элементов.
     * @return array Массив всех перестановок.
     */
    public static function getPermutations(array $elements): array
    {
        $permutations = [];
        $n = count($elements);

        // 1. Сортируем массив для получения первой перестановки (лексикографически первой)
        sort($elements);
        $permutations[] = $elements;

        while (true) {
            // 2. Найти наибольший индекс k такой, что P[k] < P[k+1]
            $k = -1;
            for ($i = $n - 2; $i >= 0; $i--) {
                if ($elements[$i] < $elements[$i + 1]) {
                    $k = $i;
                    break;
                }
            }

            // Если k не найдено, это последняя перестановка. Выход из цикла.
            if ($k === -1) {
                break;
            }

            // 3. Найти наибольший индекс l > k такой, что P[k] < P[l]
            $l = -1;
            for ($i = $n - 1; $i > $k; $i--) {
                if ($elements[$k] < $elements[$i]) {
                    $l = $i;
                    break;
                }
            }

            // 4. Поменять местами P[k] и P[l]
            [$elements[$k], $elements[$l]] = [$elements[$l], $elements[$k]];

             // 5. Обратить последовательность от P[k+1] до конца
            $start = $k + 1;
            $end = $n - 1;
            while ($start < $end) {
                [$elements[$start], $elements[$end]] = [$elements[$end], $elements[$start]];
                $start++;
                $end--;
            }

            // Добавить новую перестановку в результирующий массив
            $permutations[] = $elements;
        }

        return $permutations;
    }



    /**
     * Генератор перестановок: нерекурсивный алгоритм, использующий yield для памяти.
     *
     * @param array $elements Исходный массив элементов.
     * @return Generator Итератор, выдающий каждую перестановку по очереди.
     */
    public static function getPermutationsGenerator(array $elements): Generator
    {
        $n = count($elements);

        // 1. Сортируем массив для получения первой перестановки
        sort($elements);
        yield $elements; // Выдаем первую перестановку

        while (true) {
            // 2. Найти наибольший индекс k такой, что P[k] < P[k+1]
            $k = -1;
            for ($i = $n - 2; $i >= 0; $i--) {
                if ($elements[$i] < $elements[$i + 1]) {
                    $k = $i;
                    break;
                }
            }

            // Если k не найдено, это последняя перестановка. Завершаем генератор.
            if ($k === -1) {
                return;
            }

            // 3. Найти наибольший индекс l > k такой, что P[k] < P[l]
            $l = -1;
            for ($i = $n - 1; $i > $k; $i--) {
                if ($elements[$k] < $elements[$i]) {
                    $l = $i;
                    break;
                }
            }

            // 4. Поменять местами P[k] и P[l]
            [$elements[$k], $elements[$l]] = [$elements[$l], $elements[$k]];

            // 5. Обратить последовательность от P[k+1] до конца (реверс суффикса)
            $start = $k + 1;
            $end = $n - 1;
            while ($start < $end) {
                [$elements[$start], $elements[$end]] = [$elements[$end], $elements[$start]];
                $start++;
                $end--;
            }

            // Выдаем следующую перестановку
            yield $elements;
        }
    }

}
