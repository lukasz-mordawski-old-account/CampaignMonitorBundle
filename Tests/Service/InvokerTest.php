<?php

namespace Stevens\MainBundle\Tests\Service;

use LukaszMordawski\CampaignMonitorBundle\Helper\FactoryArguments;
use LukaszMordawski\CampaignMonitorBundle\Service\Invoker;

require_once __DIR__ . '/CSRestDummy.php';

class InvokerTest extends \PHPUnit_Framework_TestCase {

    private $factoryMock;
    private $cacheMock;
    private $clientId;
    private $service;
    private $dummy;

    public function setUp() {
        $this->factoryMock = $this->getMockBuilder('LukaszMordawski\CampaignMonitorBundle\Service\Factory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->cacheMock = $this->getMockBuilder('Doctrine\Common\Cache\Cache')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->clientId = '123456';

        $this->service = new Invoker($this->factoryMock, $this->cacheMock, $this->clientId, 3600);

        $this->dummy = $this->getMockBuilder('CS_REST_Dummy')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testConstruct() {
        $this->assertInstanceOf('LukaszMordawski\CampaignMonitorBundle\Service\Invoker', $this->service);
    }

    public function test_invoke_noClientIdProvided_DefaultClientIdIsUsed() {
        $arguments = new FactoryArguments();

        $this->factoryMock->expects($this->any())
            ->method('factory')
            ->with($this->callback(function(FactoryArguments $arguments) {
                return $arguments->clientId == '123456';
            }))
            ->will($this->returnValue($this->dummy));

        $response = new \stdClass();
        $response->http_status_code = 200;
        $response->response = null;

        $this->dummy->expects($this->any())
            ->method('dummy')
            ->with(1)
            ->will($this->returnValue($response));

        $this->service->invoke(
            $arguments, 'dummy', [ 1 ]
        );

    }

    public function test_invoke_validCachedResponse_CachedResponseReturned() {
        $arguments = new FactoryArguments();
        $arguments->clientId = 123;

        $this->factoryMock->expects($this->never())
            ->method('factory')
            ->with($this->anything());

        $this->dummy->expects($this->never())
            ->method('dummy')
            ->with($this->anything());

        $this->cacheMock->expects($this->never())
            ->method('save')
            ->with($this->anything());

        $this->cacheMock->expects($this->any())
            ->method('contains')
            ->with('campaignmonitor'.
                md5(serialize($arguments)) .
                'dummy' .
                md5(serialize([1])))
            ->will($this->returnValue(true));

        $this->cacheMock->expects($this->any())
            ->method('fetch')
            ->with('campaignmonitor'.
                md5(serialize($arguments)) .
                'dummy' .
                md5(serialize([1])))
            ->will($this->returnValue(serialize('fetched')));

        $data = $this->service->invoke(
            $arguments, 'dummy', [ 1 ]
        );

        $this->assertEquals('fetched', $data);

    }

    public function test_invoke_clientIdIsProvided_SameClientIdIsUsed() {
        $arguments = new FactoryArguments();
        $arguments->clientId = 1;

        $this->factoryMock->expects($this->any())
            ->method('factory')
            ->with($this->callback(function(FactoryArguments $arguments) {
                return $arguments->clientId == 1;
            }))
            ->will($this->returnValue($this->dummy));

        $response = new \stdClass();
        $response->http_status_code = 200;
        $response->response = null;

        $this->dummy->expects($this->any())
            ->method('dummy')
            ->with(1)
            ->will($this->returnValue($response));

        $this->service->invoke(
            $arguments, 'dummy', [ 1 ]
        );

    }

    public function test_invoke_goodInvocation_ResponseIsReturned() {
        $arguments = new FactoryArguments();

        $this->factoryMock->expects($this->any())
            ->method('factory')
            ->will($this->returnValue($this->dummy));

        $response = new \stdClass();
        $response->http_status_code = 200;
        $response->response = 'a response';

        $this->dummy->expects($this->any())
            ->method('dummy')
            ->with(1)
            ->will($this->returnValue($response));

        $data = $this->service->invoke(
            $arguments, 'dummy', [ 1 ]
        );

        $this->assertEquals('a response', $data);
    }

    public function test_invoke_goodInvocation_ResponseIsSavedInCache() {
        $arguments = new FactoryArguments();
        $arguments->clientId = 123;

        $this->factoryMock->expects($this->any())
            ->method('factory')
            ->will($this->returnValue($this->dummy));

        $response = new \stdClass();
        $response->http_status_code = 200;
        $response->response = 'a response';

        $this->dummy->expects($this->any())
            ->method('dummy')
            ->with(1)
            ->will($this->returnValue($response));

        $this->cacheMock->expects($this->atLeastOnce())
            ->method('save')
            ->with('campaignmonitor'.
            md5(serialize($arguments)) .
            'dummy' .
            md5(serialize([1])), 3600);

        $this->service->invoke(
            $arguments, 'dummy', [ 1 ]
        );
    }

    /**
     * @expectedException \LogicException
     */
    public function test_invoke_badMethodName_logicExceptionIsThrown() {
        $arguments = new FactoryArguments();

        $this->factoryMock->expects($this->any())
            ->method('factory')
            ->will($this->returnValue($this->dummy));

        $this->service->invoke(
            $arguments, 'dummy123', [ 1 ]
        );
    }

    /**
     * @expectedException \LukaszMordawski\CampaignMonitorBundle\Exception\Exception
     */
    public function test_invoke_ErrorResponseCode_ExceptionIsThrown() {
        $arguments = new FactoryArguments();

        $this->factoryMock->expects($this->any())
            ->method('factory')
            ->will($this->returnValue($this->dummy));

        $response = new \stdClass();
        $response->response = new \stdClass();
        $response->http_status_code = 401;
        $response->response->Code = '100';
        $response->response->Message = 'Error message';

        $this->dummy->expects($this->any())
            ->method('dummy')
            ->with(1)
            ->will($this->returnValue($response));

        $this->service->invoke(
            $arguments, 'dummy', [ 1 ]
        );
    }
}