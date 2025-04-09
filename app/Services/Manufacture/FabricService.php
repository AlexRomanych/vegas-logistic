<?php

namespace App\Services\Manufacture;

// Сервис для Полотен стеганных
use App\Models\Manufacture\Cells\Fabric\Fabric;
use App\Models\Manufacture\Cells\Fabric\FabricMachine;
use App\Models\Manufacture\Cells\Fabric\FabricPicture;
use App\Models\Manufacture\Cells\Fabric\FabricPictureSchema;
use Carbon\Carbon;

//use Illuminate\Support\Collection;

final class FabricService
{
    private static array $fabricsNameCache = [];                       // Кэш ПС по имени
    private static array $fabricPicsNameCache = [];                    // Кэш рисунков ПС по имени
    private static array $fabricMachinesNameCache = [];                // Кэш стегальных машин
    private static array $fabricPicSchemasNameCache = [];              // Кэш схем мгл рисунков

// hr-----------------------------------------------------------------------------
// attract: ПС
    /**
     * Получаем ПС по имени
     * @param string $name
     * @return Fabric|null
     */
    public static function getFabricByName(string $name): ?Fabric
    {

        if (count(self::$fabricsNameCache) === 0) {
            self::cacheFabricsByName();
        }

        if (isset(self::$fabricsNameCache[$name])) {
            return self::$fabricsNameCache[$name];
        }

        return null;
    }

    // Кэшируем ПС: ключ - имя, значение - объект ПС
    private static function cacheFabricsByName(): void
    {
        $fabrics = Fabric::all();

        foreach ($fabrics as $fabric) {
            self::$fabricsNameCache[$fabric->name] = $fabric;
        }

    }

// hr-----------------------------------------------------------------------------
// attract: рисунки ПС

    /**
     * Получаем рисунок по имени
     * @param string $name
     * @return FabricPicture|null
     */
    public static function getFabricPicByName(string $name): ?FabricPicture
    {
        if (count(self::$fabricPicsNameCache) === 0) {
            self::cacheFabricPicsByName();
        }

        if (isset(self::$fabricPicsNameCache[$name])) {
            return self::$fabricPicsNameCache[$name];
        }

        return null;
    }


    /**
     * Кэшируем рисунки: ключ - имя, значение - объект рисунка
     * @return void
     */
    private static function cacheFabricPicsByName(): void
    {
        $fabricPics = FabricPicture::all();

        foreach ($fabricPics as $fabricPic) {
            self::$fabricPicsNameCache[$fabricPic->name] = $fabricPic;
        }
    }

// hr-----------------------------------------------------------------------------
// attract: стегальные машины

    /**
     * Получаем стегальную машину по сокращенному имени
     * @param string $shortName
     * @return FabricMachine|null
     */
    public static function getFabricMachineByShortName(string $shortName): ?FabricMachine
    {
        if (count(self::$fabricMachinesNameCache) === 0) {
            self::cacheFabricMachinesByShortName();
        }

        if (isset(self::$fabricMachinesNameCache[$shortName])) {
            return self::$fabricMachinesNameCache[$shortName];
        }

        return null;
    }


    /**
     * Кэшируем стегальные машины: ключ - сокращенное имя, значение - объект стегальной машины
     * @return void
     */
    private static function cacheFabricMachinesByShortName(): void
    {
        $fabricMachines = FabricMachine::all();
        foreach ($fabricMachines as $fabricMachine) {
            self::$fabricMachinesNameCache[$fabricMachine->short_name] = $fabricMachine;
        }
    }


// hr-----------------------------------------------------------------------------
// attract: схемы игл

    /**
     * Получаем схему по имени
     * @param string $schema
     * @return FabricPictureSchema|null
     */
    public static function getFabricPicSchemaByName(string $schema): ?FabricPictureSchema
    {
        if (count(self::$fabricPicSchemasNameCache) === 0) {
            self::cacheFabricPicSchemasByName();
        }

        if (isset(self::$fabricPicSchemasNameCache[$schema])) {
            return self::$fabricPicSchemasNameCache[$schema];
        }

        return null;
    }


    /**
     * Кэшируем схемы: ключ - имя, значение - объект схемы
     * @return void
     */
    private static function cacheFabricPicSchemasByName(): void
    {
        $picSchemas = FabricPictureSchema::all();
        foreach ($picSchemas as $picSchema) {
            self::$fabricPicSchemasNameCache[$picSchema->schema] = $picSchema;

        }
    }


    /**
     * Получаем название стегальной машины по ее ID
     * @param int $machineId
     * @return string
     */
    public static function getFabricMachineNameById(int $machineId): string
    {
        return match ($machineId) {
            FABRIC_MACHINE_UNKNOWN_ID => FABRIC_MACHINE_UNKNOWN_TITLE,
            FABRIC_MACHINE_AMERICAN_ID => FABRIC_MACHINE_AMERICAN_TITLE,
            FABRIC_MACHINE_GERMAN_ID => FABRIC_MACHINE_GERMAN_TITLE,
            FABRIC_MACHINE_CHINA_ID => FABRIC_MACHINE_CHINA_TITLE,
            FABRIC_MACHINE_KOREAN_ID => FABRIC_MACHINE_KOREAN_TITLE,
            default => 'error'
        };
    }

    /**
     * Получаем ID стегальной машины по ее имени
     * @param string $machineName
     * @return int
     */
    public static function getFabricMachineIdByName(string $machineName): int
    {
        return match (strtolower($machineName)) {
            FABRIC_MACHINE_UNKNOWN_TITLE => FABRIC_MACHINE_UNKNOWN_ID,
            FABRIC_MACHINE_AMERICAN_TITLE => FABRIC_MACHINE_AMERICAN_ID,
            FABRIC_MACHINE_GERMAN_TITLE => FABRIC_MACHINE_GERMAN_ID,
            FABRIC_MACHINE_CHINA_TITLE => FABRIC_MACHINE_CHINA_ID,
            FABRIC_MACHINE_KOREAN_TITLE => FABRIC_MACHINE_KOREAN_ID,
            default => 0
        };
    }

    /**
     * Получаем номер бригады стегальной машины по дате
     * @param string|Carbon $date
     * @return int
     */
    public static function getFabricTeamChangeNumberByDate(string | Carbon $date): int
    {
        $referenceDate = Carbon::parse('2025-03-26');
        $targetDate = Carbon::parse($date);

        $diffInDays = abs($referenceDate->diffInDays($targetDate));

        $remainsOfDivision = $diffInDays % 4;

        if ($remainsOfDivision === 0 || $remainsOfDivision === 1) return 1;
        return 2;
    }

}
