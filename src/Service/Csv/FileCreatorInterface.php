<?php

namespace App\Service\Csv;

interface FileCreatorInterface
{
    public function createCsvFile(string $filename, array $list);

    public function createTxtFile(string $filename, array $texts);
}
