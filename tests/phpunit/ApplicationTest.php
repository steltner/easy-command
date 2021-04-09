<?php declare(strict_types=1);

namespace Easy;

use Easy\Service\CommandListService;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    public function testCreation(): void
    {
        $serviceManager = $this->createMock(ServiceManager::class);

        $serviceManager->expects($this->once())
            ->method('get')
            ->with(CommandListService::class)
            ->willReturn(function () {
                return [];
            });

        $instance = new Application($serviceManager);

        $this->assertInstanceOf(Application::class, $instance);
    }
}
