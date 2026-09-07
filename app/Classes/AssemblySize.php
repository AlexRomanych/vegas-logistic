<?php

namespace App\Classes;


use App\Models\Manufacture\Cells\Assembly\AssemblyTask;

class AssemblySize
{
    private const TOR_TITLE_WIDTH = 'ТорецШирина';
    private const TOR_TITLE_LENGTH = 'ТорецДлина';
    private const TOR_TITLE_HEIGHT = 'ТорецВысота';
    private const TOR_TITLE_AMOUNT = 'ТорецКоличество';
    private const SIDE_TITLE_WIDTH = 'БокШирина';
    private const SIDE_TITLE_LENGTH = 'БокДлина';
    private const SIDE_TITLE_HEIGHT = 'БокВысота';
    private const SIDE_TITLE_AMOUNT = 'БокКоличество';
    private const LAYER_TITLE_WIDTH = 'НастилШирина';
    private const LAYER_TITLE_LENGTH = 'НастилДлина';
    private const LAYER_TITLE_HEIGHT = 'НастилВысота';
    private const LAYER_TITLE_AMOUNT = 'НастилКоличество';


    private int $widthDetail = 0;
    private int $lengthDetail = 0;
    private int $heightDetail = 0;
    private int $amountDetail = 1;
    private array $details = [];

    public function __construct(
        private readonly string $sector,
        private string|null $scope,
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
                $scope = $this->parseScope();

                if (!is_null($scope)) {

                    // __ Настил
                    if (
                        isset($scope[self::LAYER_TITLE_WIDTH]) &&
                        isset($scope[self::LAYER_TITLE_LENGTH]) &&
                        isset($scope[self::LAYER_TITLE_HEIGHT]) &&
                        isset($scope[self::LAYER_TITLE_AMOUNT])
                    ) {
                        $this->details[] = [
                            'width'  => $scope[self::LAYER_TITLE_WIDTH] * 10,
                            'length' => $scope[self::LAYER_TITLE_LENGTH] * 10,
                            'height' => $scope[self::LAYER_TITLE_HEIGHT] * 10,
                            'amount' => $scope[self::LAYER_TITLE_AMOUNT],
                        ];
                        break;
                    }
                }

                $defaultHeight = 10;   // __ Высота Детальки по умолчанию

                $this->widthDetail  = $this->width * 10;
                $this->lengthDetail = $this->length * 10;
                $this->heightDetail = $defaultHeight * 10;
                $this->amountDetail = 2;

                // __ Настил
                $this->details[] = [
                    'width'  => $this->width * 10,
                    'length' => $this->length * 10,
                    'height' => $defaultHeight * 10,
                    'amount' => 1,
                ];

                break;

            case AssemblyTask::ASSEMBLY_TASK_SECTOR_FOAM_SIDE:  // __ ППУ Борта
                $scope = $this->parseScope();

                if (!is_null($scope)) {
                    $findTor  = false;
                    $findSide = false;

                    // __ Короткий Борт (Торец)
                    if (
                        isset($scope[self::TOR_TITLE_WIDTH]) &&
                        isset($scope[self::TOR_TITLE_LENGTH]) &&
                        isset($scope[self::TOR_TITLE_HEIGHT]) &&
                        isset($scope[self::TOR_TITLE_AMOUNT])
                    ) {
                        $this->details[] = [
                            'width'  => $scope[self::TOR_TITLE_WIDTH] * 10,
                            'length' => $scope[self::TOR_TITLE_LENGTH] * 10,
                            'height' => $scope[self::TOR_TITLE_HEIGHT] * 10,
                            'amount' => $scope[self::TOR_TITLE_AMOUNT],
                        ];
                        $findTor         = true;
                    }

                    // __ Длинный Борт (Бок)
                    if (
                        isset($scope[self::SIDE_TITLE_WIDTH]) &&
                        isset($scope[self::SIDE_TITLE_LENGTH]) &&
                        isset($scope[self::SIDE_TITLE_HEIGHT]) &&
                        isset($scope[self::SIDE_TITLE_AMOUNT])
                    ) {
                        $this->details[] = [
                            'width'  => $scope[self::SIDE_TITLE_WIDTH] * 10,
                            'length' => $scope[self::SIDE_TITLE_LENGTH] * 10,
                            'height' => $scope[self::SIDE_TITLE_HEIGHT] * 10,
                            'amount' => $scope[self::SIDE_TITLE_AMOUNT],
                        ];
                        $findSide        = true;
                    }

                    if ($findTor || $findSide) {
                        break;
                    }
                }

                $this->widthDetail  = $this->width * 10;
                $this->lengthDetail = $this->length * 10;
                $this->heightDetail = $this->height * 10;
                $this->amountDetail = 2;

                $offSet = 10;   // __ Ширина Детальки по умолчанию

                // __ Длинный Борт
                $this->details[] = [
                    'width'  => $offSet * 10,
                    'length' => $this->length * 10,
                    'height' => $this->height * 10,
                    'amount' => 2,
                ];

                // __ Короткий Борт
                $this->details[] = [
                    'width'  => $offSet * 10,
                    'length' => ($this->width - 2 * $offSet) * 10,
                    'height' => $this->height * 10,
                    'amount' => 2,
                ];

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


    private function parseScope()
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
