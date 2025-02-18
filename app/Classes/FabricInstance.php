<?php

namespace App\Classes;


class FabricInstance
{
    private string $fabricName = '';                // Название ПС
    private string $textileShortName = '';          // Сокращенное название ткани
    private string $picture = '';                   // Рисунок стежки
    private array $fillersList = [];                // Список наполнителей ПС

    public function __construct(string $fabric = '')
    {
        if ($fabric === '') return;

        $this->fabricName = $fabric;
        $this->parseFabric();
    }

    /**
     * Парсим ПС
     * @return void
     */
    private function parseFabric(): void
    {

        $tempFabric = $this->fabricName;

        // Заменяем точку на точку с пробелом для ситуаций с точкой без пробела в названии рисунка
        // 'ПС 230Т 300C 30С Vcure (рис.ЛС2)'
        $tempFabric = str_replace('.', '. ', $tempFabric);

        // Убираем двойные пробелы
        while (str_contains($tempFabric, '  ')) {
            $tempFabric = str_replace('  ', ' ', $tempFabric);
        }

//        $this->picture = $tempFabric;
//
//        return;

        // Разбиваем на составляющие по пробелу
        $parseFabric = explode(' ', $tempFabric);

        $picsPos = 0;       // Позиция слова 'рис.'

        // Получаем картинку ткани
        for ($i = 0; $i < count($parseFabric); $i++) {
            if (str_contains(mb_strtolower($parseFabric[$i]), 'рис.')) {
                $this->picture = str_replace(')', '', $parseFabric[$i + 1]);
                $picsPos = $i;
                break;
            }
        }

        // Получаем сокращенное название ткани
        $shortTextileEnd = $picsPos;                // позиция окончания названия ткани
        $shortTextileStart = $shortTextileEnd;      // позиция начала названия ткани

        while (strlen(getDigitPart($parseFabric[$shortTextileStart])) === 0) {
            $shortTextileStart--;                   // Уменьшаем позицию названия ткани, пока не попадем на наполнитель
        }

        $shortTextileStart++;                       // Увеличиваем позицию названия ткани
        for ($i = $shortTextileStart; $i < $shortTextileEnd; $i++) {
            $this->textileShortName .= $parseFabric[$i] . ' ';
        }

        $this->textileShortName = trim($this->textileShortName);

        // Получаем список наполнителей
        for ($i = 1; $i < $shortTextileStart; $i++) {
            $this->fillersList[] = $parseFabric[$i];
        }
    }

    /**
     * Получаем название ткани
     * @return string
     */
    public function getFabricName(): string
    {
        return $this->fabricName;
    }

    /**
     * Получаем сокращенное название ткани
     * @return string
     */
    public function getTextileShortName(): string
    {
        return $this->textileShortName;
    }

    /**
     * Получаем картинку ткани
     * @return string
     */
    public function getPicture(): string
    {
        return $this->picture;
    }

    /**
     * Получаем список наполнителей
     * @return array
     */
    public function getFillersList(): array
    {
        return $this->fillersList;
    }

}
