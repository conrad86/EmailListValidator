<?php

declare(strict_types = 1);

namespace App\Command;

use App\Service\Csv\FileCreatorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

class ValidateEmailListCommand extends Command
{
    private $fileCreator;

    public function __construct(FileCreatorInterface $fileCreator)
    {
        $this->fileCreator = $fileCreator;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('validate-email-list')
            ->setDescription('Validates email list from .csv file.')
            ->addArgument('filename', InputArgument::REQUIRED, 'CSV filename.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $fileName = $input->getArgument('filename');
        $file = $this->getFile($fileName);

        if (!$file) {
            $io->error(sprintf('File "%s.csv" does not exist.', $fileName));
            return;
        }

        $this->validate($file);
        $io->success(sprintf('Successfully validate email list'));
    }

    private function getFile(string $file)
    {
        $finder = new Finder();
        $finder->files()->in("public/data");
        $finder->files()->name(sprintf('%s.csv', $file));

        if ($finder->hasResults()) {
            foreach ($finder as $file) {
                $fileContent = file_get_contents($file->getRealPath());
            }

            return $fileContent;
        }
    }

    private function validate($fileContent)
    {
        $data = str_getcsv($fileContent, PHP_EOL);

        $validEmails = [];
        $invalidEmails = [];

        foreach ($data as $record) {
            if (filter_var($record, FILTER_VALIDATE_EMAIL)){
                $validEmails[] = $record;
            } else {
                $invalidEmails[] = $record;
            }
        }

        $this->createValidEmailList($validEmails);
        $this->createInvalidEmailList($invalidEmails);
        $this->createValidationReport($invalidEmails, $validEmails);
    }

    private function createValidEmailList(array $list)
    {
        $this->fileCreator->createCsvFile('valid', $list);
    }

    private function createInvalidEmailList(array $list)
    {
        $this->fileCreator->createCsvFile('invalid', $list);
    }

    private function createValidationReport(array $valid, array $invalid)
    {
        $invalidCount = count($invalid);
        $validCount = count($valid);
        $texts[] = 'Validation summary:';
        $texts[] = sprintf('Invalid email count: "%d".', $invalidCount);
        $texts[] = sprintf('Valid email count: "%d".', $validCount);

        $this->fileCreator->createTxtFile('report', $texts);
    }
}
