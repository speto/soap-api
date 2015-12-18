<?php
/**
 * File: RawExecutorTest.php
 * Created at: 2014-11-25 19:57
 */

namespace Webit\SoapApi\Tests\Executor;

use Webit\SoapApi\Executor\RawExecutor;
use Webit\SoapApi\Tests\AbstractTest;

/**
 * Class RawExecutorTest
 * @author Daniel Bojdo <daniel@bojdo.eu>
 */
class RawExecutorTest extends AbstractTest
{
    /**
     * @var \Mockery\MockInterface|\Webit\SoapApi\SoapClient\SoapClientFactory
     */
    private $factory;

    /**
     * @var \Mockery\MockInterface|\SoapClient
     */
    private $soapClient;

    /**
     * @var RawExecutor
     */
    private $executor;

    protected function setUp()
    {
        $this->soapClient = $this->mockSoapClient();

        $this->factory = $this->mockSoapClientFactory();
        $this->factory->shouldReceive('createSoapClient')->andReturn($this->soapClient);

        $this->executor = new RawExecutor($this->factory);
    }

    /**
     * @test
     */
    public function shouldLazyInstantiateSoapClientOnlyOnce()
    {
        $this->factory->shouldNotHaveReceived('createSoapClient');

        $this->soapClient->shouldReceive('__soapCall');

        $this->executor->executeSoapFunction('function');
        $this->executor->executeSoapFunction('function');

        $this->factory->shouldHaveReceived('createSoapClient')->once();
    }

    /**
     * @test
     */
    public function shouldDelegateToSoapClient()
    {
        $function = 'function';
        $input = array('input' => 'value');
        $result = new \stdClass();

        $this->soapClient->shouldReceive('__soapCall')->with(
            $function,
            array($input)
        )->andReturn($result);

        $this->assertEquals($result, $this->executor->executeSoapFunction($function, $input));
    }

    /**
     * @test
     * @expectedException \Webit\SoapApi\Executor\Exception\ExecutorException
     */
    public function shouldWrapAnyExecutionException()
    {
        $this->soapClient->shouldReceive('__soapCall')->andThrow('\SoapFault', 'msg', 23)->once();

        $this->executor->executeSoapFunction('function');
    }
}
