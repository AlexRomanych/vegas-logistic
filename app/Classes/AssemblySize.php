<?php

namespace App\Classes;


use App\Models\Manufacture\Cells\Assembly\AssemblyTask;

class AssemblySize
{
    // __ Торец ППУ
    private const FOAM_TOR_TITLE_WIDTH = 'ШиринаТорец';
    //private const TOR_TITLE_WIDTH = 'ТорецШирина';
    private const FOAM_TOR_TITLE_LENGTH = 'ТорецДлина';
    private const FOAM_TOR_TITLE_HEIGHT = 'ВысотаБорт';
    //private const TOR_TITLE_HEIGHT = 'ТорецВысота';
    private const FOAM_TOR_TITLE_AMOUNT = 'ТорецКоличество';

    // __ Бок ППУ
    private const FOAM_SIDE_TITLE_WIDTH = 'ШиринаБок';
    //private const SIDE_TITLE_WIDTH = 'БокШирина';
    private const FOAM_SIDE_TITLE_LENGTH = 'БокДлина';
    private const FOAM_SIDE_TITLE_HEIGHT = 'ВысотаБорт';
    //private const SIDE_TITLE_HEIGHT = 'БокВысота';
    private const FOAM_SIDE_TITLE_AMOUNT = 'БокКоличество';

    // __ Настил ППУ
    private const FOAM_LAYER_TITLE_HEIGHT = 'ТолщинаНастила';
    private const FOAM_LAYER_TITLE_WIDTH = 'НастШирина';
    private const FOAM_LAYER_TITLE_WIDTH_2 = 'НастШирина2';
    private const FOAM_LAYER_TITLE_LENGTH = 'НастДлина';
    private const FOAM_LAYER_TITLE_LENGTH_2 = 'НастДлина2';
    private const FOAM_LAYER_TITLE_AMOUNT = 'НастилКоличество';
    private const FOAM_LAYER_TITLE_AMOUNT_2 = 'НастилКоличество2';

    // __ Тонкий Настил
    private const THIN_LAYER_TITLE_WIDTH = 'НастилШирина';
    private const THIN_LAYER_TITLE_LENGTH = 'НастилДлина';
    private const THIN_LAYER_TITLE_HEIGHT = 'ТолщинаНастила';
    private const THIN_LAYER_TITLE_AMOUNT = 'КоличествоДеталей';


    private int $widthDetail = 0;
    private int $lengthDetail = 0;
    private int $heightDetail = 0;
    private int $amountDetail = 1;
    private array $details = [];

    public function __construct(
        private readonly string $sector,
        private readonly string|null $scope,
        private int|null $width = 0,
        private int|null $length = 0,
        private int|null $height = 0,
        //private readonly string $scope,
        //private readonly string $scope,
    )
    {
        if (is_null($this->width)) {
            $this->width = 0;
        }

        if (is_null($this->length)) {
            $this->length = 0;
        }

        if (is_null($this->height)) {
            $this->height = 0;
        }


        $this->details = [];

        // __ Отдаем в миллиметрах
        switch ($sector):
            case AssemblyTask::ASSEMBLY_TASK_SECTOR_LATEX:  // __ Латекс
                $this->widthDetail  = $this->width * 10;
                $this->lengthDetail = $this->length * 10;
                $this->heightDetail = 0;
                //$this->heightDetail = $this->height * 10;
                $this->amountDetail = 1;

                $this->details[] = [
                    'width'  => $this->width * 10,
                    'length' => $this->length * 10,
                    'height' => 0,
                    //'height' => $this->height * 10,
                    'amount' => 1,
                ];

                break;

            case AssemblyTask::ASSEMBLY_TASK_SECTOR_COCONUT: // __ Кокос
                $this->widthDetail  = $this->width * 10;
                $this->lengthDetail = $this->length * 10;
                $this->heightDetail = 0;
                //$this->heightDetail = $this->height * 10;
                $this->amountDetail = 1;

                $this->details[] = [
                    'width'  => $this->width * 10,
                    'length' => $this->length * 10,
                    'height' => 0,
                    //'height' => $this->height * 10,
                    'amount' => 1,
                ];

                break;

            case AssemblyTask::ASSEMBLY_TASK_SECTOR_LAYER:  // __ Тонкий настил
                $scope = $this->parseScope();

                if (!is_null($scope)) {
                    // __ тонкий Настил
                    if (
                        isset($scope[self::THIN_LAYER_TITLE_WIDTH]) &&
                        isset($scope[self::THIN_LAYER_TITLE_LENGTH]) &&
                        //isset($scope[self::THIN_LAYER_TITLE_HEIGHT]) && // __ не учавствует в расчетах
                        isset($scope[self::THIN_LAYER_TITLE_AMOUNT])
                    ) {
                        $this->details[] = [
                            'width'  => $scope[self::THIN_LAYER_TITLE_WIDTH] * 10,
                            'length' => $scope[self::THIN_LAYER_TITLE_LENGTH] * 10,
                            'height' => 0,
                            //'height' => $scope[self::THIN_LAYER_TITLE_HEIGHT] * 10,
                            'amount' => $scope[self::THIN_LAYER_TITLE_AMOUNT],
                        ];
                        break;
                    }
                }

                $this->widthDetail  = $this->width * 10;
                $this->lengthDetail = $this->length * 10;
                //$this->heightDetail = $this->height * 10;
                $this->amountDetail = 1;

                $this->details[] = [
                    'width'  => $this->width * 10,
                    'length' => $this->length * 10,
                    'height' => 0,
                    //'height' => $this->height * 10,
                    'amount' => 1,
                ];

                break;

            case AssemblyTask::ASSEMBLY_TASK_SECTOR_FOAM_LAYER: // __ ППУ Настил
                // --- 11.11.2026

                $scope = $this->parseScope();

                if (!is_null($scope)) {
                    // __ Настил ППУ

                    $this->details[] = [
                        'width'  => isset($scope[self::FOAM_LAYER_TITLE_WIDTH]) ? $scope[self::FOAM_LAYER_TITLE_WIDTH] * 10 : 0,
                        'length' => isset($scope[self::FOAM_LAYER_TITLE_LENGTH]) ? $scope[self::FOAM_LAYER_TITLE_LENGTH] * 10 : 0,
                        'height' => isset($scope[self::FOAM_LAYER_TITLE_HEIGHT]) ? $scope[self::FOAM_LAYER_TITLE_HEIGHT] * 10 : 0,
                        'amount' => $scope[self::FOAM_LAYER_TITLE_AMOUNT] ?? 0,
                    ];

                    // __ Настил ППУ 2 (Возможный дополнительный второй настил)
                    if (
                        isset($scope[self::FOAM_LAYER_TITLE_AMOUNT])
                        && isset($scope[self::FOAM_LAYER_TITLE_AMOUNT_2])
                        && $scope[self::FOAM_LAYER_TITLE_AMOUNT_2] > 0
                    ) {
                        $this->details[] = [
                            'width'  => isset($scope[self::FOAM_LAYER_TITLE_WIDTH_2]) ? $scope[self::FOAM_LAYER_TITLE_WIDTH_2] * 10 : 0,
                            'length' => isset($scope[self::FOAM_LAYER_TITLE_LENGTH_2]) ? $scope[self::FOAM_LAYER_TITLE_LENGTH_2] * 10 : 0,
                            'height' => isset($scope[self::FOAM_LAYER_TITLE_HEIGHT]) ? $scope[self::FOAM_LAYER_TITLE_HEIGHT] * 10 : 0,
                            'amount' => $scope[self::FOAM_LAYER_TITLE_AMOUNT_2],
                        ];
                    }

                    break;
                }


                //if (!is_null($scope)) {
                //    // __ Настил ППУ
                //    if (
                //        isset($scope[self::FOAM_LAYER_TITLE_WIDTH]) &&
                //        isset($scope[self::FOAM_LAYER_TITLE_LENGTH]) &&
                //        isset($scope[self::FOAM_LAYER_TITLE_HEIGHT]) &&
                //        isset($scope[self::FOAM_LAYER_TITLE_AMOUNT])
                //    ) {
                //        $this->details[] = [
                //            'width'  => $scope[self::FOAM_LAYER_TITLE_WIDTH] * 10,
                //            'length' => $scope[self::FOAM_LAYER_TITLE_LENGTH] * 10,
                //            'height' => $scope[self::FOAM_LAYER_TITLE_HEIGHT] * 10,
                //            'amount' => $scope[self::FOAM_LAYER_TITLE_AMOUNT],
                //        ];
                //
                //        // __ Настил ППУ 2 (Возможный дополнительный второй настил)
                //        if (
                //            isset($scope[self::FOAM_LAYER_TITLE_WIDTH_2]) &&
                //            isset($scope[self::FOAM_LAYER_TITLE_LENGTH_2]) &&
                //            isset($scope[self::FOAM_LAYER_TITLE_HEIGHT]) &&
                //            isset($scope[self::FOAM_LAYER_TITLE_AMOUNT_2])
                //        ) {
                //            $this->details[] = [
                //                'width'  => $scope[self::FOAM_LAYER_TITLE_WIDTH_2] * 10,
                //                'length' => $scope[self::FOAM_LAYER_TITLE_LENGTH_2] * 10,
                //                'height' => $scope[self::FOAM_LAYER_TITLE_HEIGHT] * 10,
                //                'amount' => $scope[self::FOAM_LAYER_TITLE_AMOUNT_2],
                //            ];
                //        }
                //
                //        break;
                //    }
                //}

                //$defaultHeight = 0;   // __ Высота Детальки по умолчанию
                //$defaultHeight = 10;   // __ Высота Детальки по умолчанию

                //$this->widthDetail  = 0;
                //$this->lengthDetail = 0;
                //$this->heightDetail = 0;

                //$this->widthDetail  = $this->width * 10;
                //$this->lengthDetail = $this->length * 10;
                //$this->heightDetail = $defaultHeight * 10;

                // __ Настил
                $this->details[] = [
                    'width'  => 0,
                    'length' => 0,
                    'height' => 0,
                    'amount' => 1,
                ];

                //$this->details[] = [
                //    'width'  => $this->width * 10,
                //    'length' => $this->length * 10,
                //    'height' => $defaultHeight * 10,
                //    'amount' => 1,
                //];

                break;

            case AssemblyTask::ASSEMBLY_TASK_SECTOR_FOAM_SIDE:  // __ ППУ Борта
                $scope = $this->parseScope();

                if (!is_null($scope)) {
                    //$findTor  = false;
                    //$findSide = false;

                    // __ Короткий Борт (Торец)
                    $this->details[] = [
                        'width'  => isset($scope[self::FOAM_TOR_TITLE_WIDTH]) ? $scope[self::FOAM_TOR_TITLE_WIDTH] * 10 : 0,
                        'length' => isset($scope[self::FOAM_TOR_TITLE_LENGTH]) ? $scope[self::FOAM_TOR_TITLE_LENGTH] * 10 : 0,
                        'height' => isset($scope[self::FOAM_TOR_TITLE_HEIGHT]) ? $scope[self::FOAM_TOR_TITLE_HEIGHT] * 10 : 0,
                        'amount' => $scope[self::FOAM_TOR_TITLE_AMOUNT] ?? 2,
                    ];

                    //if (
                    //    isset($scope[self::FOAM_TOR_TITLE_WIDTH]) &&
                    //    isset($scope[self::FOAM_TOR_TITLE_LENGTH]) &&
                    //    isset($scope[self::FOAM_TOR_TITLE_HEIGHT]) &&
                    //    isset($scope[self::FOAM_TOR_TITLE_AMOUNT])
                    //) {
                    //    $this->details[] = [
                    //        'width'  => $scope[self::FOAM_TOR_TITLE_WIDTH] * 10,
                    //        'length' => $scope[self::FOAM_TOR_TITLE_LENGTH] * 10,
                    //        'height' => $scope[self::FOAM_TOR_TITLE_HEIGHT] * 10,
                    //        'amount' => $scope[self::FOAM_TOR_TITLE_AMOUNT],
                    //    ];
                    //    $findTor         = true;
                    //}

                    // __ Длинный Борт (Бок)
                    $this->details[] = [
                        'width'  => isset($scope[self::FOAM_SIDE_TITLE_WIDTH]) ? $scope[self::FOAM_SIDE_TITLE_WIDTH] * 10 : 0,
                        'length' => isset($scope[self::FOAM_SIDE_TITLE_LENGTH]) ? $scope[self::FOAM_SIDE_TITLE_LENGTH] * 10 : 0,
                        'height' => isset($scope[self::FOAM_SIDE_TITLE_HEIGHT]) ? $scope[self::FOAM_SIDE_TITLE_HEIGHT] * 10 : 0,
                        'amount' => $scope[self::FOAM_SIDE_TITLE_AMOUNT] ?? 2,
                    ];


                    //if (
                    //    isset($scope[self::FOAM_SIDE_TITLE_WIDTH]) &&
                    //    isset($scope[self::FOAM_SIDE_TITLE_LENGTH]) &&
                    //    isset($scope[self::FOAM_SIDE_TITLE_HEIGHT]) &&
                    //    isset($scope[self::FOAM_SIDE_TITLE_AMOUNT])
                    //) {
                    //    $this->details[] = [
                    //        'width'  => $scope[self::FOAM_SIDE_TITLE_WIDTH] * 10,
                    //        'length' => $scope[self::FOAM_SIDE_TITLE_LENGTH] * 10,
                    //        'height' => $scope[self::FOAM_SIDE_TITLE_HEIGHT] * 10,
                    //        'amount' => $scope[self::FOAM_SIDE_TITLE_AMOUNT],
                    //    ];
                    //    $findSide        = true;
                    //}

                    //if ($findTor || $findSide) {
                    //    break;
                    //}

                    break;
                }

                //$this->widthDetail  = $this->width * 10;
                //$this->lengthDetail = $this->length * 10;
                //$this->heightDetail = $this->height * 10;
                //$this->amountDetail = 2;

                $offSet = 0;   // __ Ширина Детальки по умолчанию
                //$offSet = 10;   // __ Ширина Детальки по умолчанию

                // __ Длинный Борт
                $this->details[] = [
                    'width'  => 0,
                    'length' => 0,
                    'height' => 0,
                    'amount' => 2,
                ];

                //$this->details[] = [
                //    'width'  => $offSet * 10,
                //    'length' => $this->length * 10,
                //    'height' => $this->height * 10,
                //    'amount' => 2,
                //];

                // __ Короткий Борт
                $this->details[] = [
                    'width'  => 0,
                    'length' => 0,
                    'height' => 0,
                    'amount' => 2,
                ];

                //$this->details[] = [
                //    'width'  => $offSet * 10,
                //    'length' => ($this->width - 2 * $offSet) * 10,
                //    'height' => $this->height * 10,
                //    'amount' => 2,
                //];

                break;

        endswitch;
    }


    public function getWidth(): int
    {
        return $this->widthDetail;
    }

    public function getLength(): int
    {
        return $this->lengthDetail;
    }

    public function getHeight(): int
    {
        return $this->heightDetail;
    }

    public function getAmount(): int
    {
        return $this->amountDetail;
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    public function getSector(): string
    {
        return $this->sector;
    }

    public function getScope(): string
    {
        return $this->scope;
    }


    // __ Парсим Scope
    private function parseScope(): ?array
    {
        if (!is_null($this->scope)) {
            $scope = json_decode($this->scope, true);

            // __ Преображаем в плоский массив key => value
            $scope = array_column($scope, 'v', 'n');

            // __ Или
            //foreach ($this->scope as $item) {
            //    if (isset($item['n'], $item['v'])) {
            //        $scope[$item['n']] = $item['v'];
            //    }
            //}

            return $scope;
        }

        return null;
    }
}
