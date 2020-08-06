<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer;

use Composer\IO\IOInterface;
use Ntavelis\Dockposer\Enum\ExecutorStatus;
use Ntavelis\Dockposer\Factory\ExecutorsFactory;

class PostDependenciesEventHandler
{
    private const PACKAGE_NAME = 'Dockposer';
    /**
     * @var IOInterface
     */
    private $io;
    /**
     * @var ExecutorsFactory
     */
    private $executorsFactory;

    public function __construct(
        IOInterface $io,
        ExecutorsFactory $executorsFactory
    ) {
        $this->io = $io;
        $this->executorsFactory = $executorsFactory;
    }

    public function run(): void
    {
        $executors = $this->executorsFactory->createDefaultExecutors();

        foreach ($executors as $executor) {
            if (!$executor->shouldExecute()) {
                continue;
            }

            $result = $executor->execute();

            if ($result->getStatus() === ExecutorStatus::SUCCESS) {
                $this->writeSuccess($result->getResult());
                continue;
            } elseif (in_array($result->getStatus(), [ExecutorStatus::SKIPPED, ExecutorStatus::NOT_MARKED])) {
                continue;
            }

            $this->io->writeError(self::PACKAGE_NAME . ': ' . $result->getResult());
        }
    }

    private function writeSuccess(string $message): void
    {
        $this->io->write('<info>' . self::PACKAGE_NAME . ': ' . $message . '</info>');
    }
}
