<?php

namespace tests\functional;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ListCommandTest extends KernelTestCase
{
    /**
     * @var Application
     */
    private $application;

    public function setUp()
    {
        $kernel = static::createKernel();
        $this->application = new Application($kernel);
        parent::setUp();
    }

    public function test_validate_email_list_from_csv_file()
    {
        $command = $this->application->find('validate-email-list');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'  => $command->getName(),
            'filename' => 'data'
        ]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertContains('[OK] Successfully validate email list', $output);
    }

    public function test_validate_email_list_from_non_existent_file_returns_error()
    {
        $command = $this->application->find('validate-email-list');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'  => $command->getName(),
            'filename' => 'file'
        ]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertContains('[ERROR] File "file.csv" does not exist.', $output);
    }
}
