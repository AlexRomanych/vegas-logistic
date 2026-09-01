<?php

namespace App\Classes;


use App\Models\Manufacture\Cells\Assembly\AssemblyTask;

class AssemblySize
{
    private int $widthDetail = 0;
    private int $lengthDetail = 0;
    private int $heightDetail = 0;
    private int $amountDetail = 1;

    /** @noinspection PhpDuplicateSwitchCaseBodyInspection */
    public function __construct(
        private readonly string $sector,
        private readonly string $scope,
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

        // __ Отдаем в миллиметрах
        switch ($sector):
            case AssemblyTask::ASSEMBLY_TASK_SECTOR_LATEX:
                $this->widthDetail  = $this->width * 10;
                $this->lengthDetail = $this->length * 10;
                $this->heightDetail = 0;
                //$this->heightDetail = $this->height * 10;
                $this->amountDetail = 1;
                break;
            case AssemblyTask::ASSEMBLY_TASK_SECTOR_COCONUT:
                $this->widthDetail  = $this->width * 10;
                $this->lengthDetail = $this->length * 10;
                $this->heightDetail = 0;
                //$this->heightDetail = $this->height * 10;
                $this->amountDetail = 1;
                break;
            case AssemblyTask::ASSEMBLY_TASK_SECTOR_LAYER:
                $this->widthDetail  = $this->width * 10;
                $this->lengthDetail = $this->length * 10;
                $this->heightDetail = $this->height * 10;
                $this->amountDetail = 1;
                break;
            case AssemblyTask::ASSEMBLY_TASK_SECTOR_FOAM_SIDE:
                $this->widthDetail  = $this->width * 10;
                $this->lengthDetail = $this->length * 10;
                $this->heightDetail = $this->height * 10;
                $this->amountDetail = 2;
                break;
            case AssemblyTask::ASSEMBLY_TASK_SECTOR_FOAM_LAYER:
                $this->widthDetail  = $this->width * 10;
                $this->lengthDetail = $this->length * 10;
                $this->heightDetail = $this->height * 10;
                $this->amountDetail = 2;
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


    public function getSector(): string
    {
        return $this->sector;
    }

    public function getScope(): string
    {
        return $this->scope;
    }
}
