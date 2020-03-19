<?php declare(strict_types=1);

namespace Easy;

use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    public function testCreation(): void
    {
        $serviceManager = $this->createServiceManagerMock();

        $instance = new Application($serviceManager);

        $this->assertInstanceOf(Application::class, $instance);
    }

    /**
     * @return \Laminas\ServiceManager\ServiceManager|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createServiceManagerMock(): ServiceManager
    {
        return $this->getMockBuilder(ServiceManager::class)
                    ->disableOriginalConstructor()
                    ->getMock();
    }
}
