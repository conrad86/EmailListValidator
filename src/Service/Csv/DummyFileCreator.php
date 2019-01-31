<?php

declare(strict_types=1);

namespace App\Service\Csv;

class DummyFileCreator implements FileCreatorInterface
{
    public function createCsvFile(string $filename, array $list)
    {
    }

    public function createTxtFile(string $filename, array $texts)
    {
    }
}
