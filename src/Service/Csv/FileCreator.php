<?php

declare(strict_types=1);

namespace App\Service\Csv;

class FileCreator implements FileCreatorInterface
{
    public function createCsvFile(string $filename, array $list)
    {
        $fp = fopen(sprintf('public/data/%s.csv', $filename), 'w');
        fputcsv($fp, $list, PHP_EOL);
        fclose($fp);
    }

    public function createTxtFile(string $filename, array $texts)
    {
        $fp = fopen(sprintf('public/data/%s.txt', $filename), 'w');
        foreach ($texts as $line) {
            fwrite($fp, $line.PHP_EOL);
        }
        fclose($fp);
    }
}
