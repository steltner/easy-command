<?php declare(strict_types=1);

namespace Easy;

use Easy\Service\CommandListService;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    public function testCreation(): void
    {
        $serviceManager = $this->createServiceManagerMock();

        $serviceManager->expects($this->once())
            ->method('get')
            ->with(CommandListService::class)
            ->willReturn(function () {
                return [];
            });

        $instance = new Application($serviceManager);

        $this->assertInstanceOf(Application::class, $instance);
    }

    /**
     * @return ServiceManager|MockObject
     */
    private function createServiceManagerMock(): ServiceManager
    {
        return $this->getMockBuilder(ServiceManager::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
