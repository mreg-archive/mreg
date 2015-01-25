<?php
namespace mreg\Log;


class PDOHandlerTest extends \PHPUnit_Framework_TestCase
{

    function testWrite()
    {
        $logger = new \Monolog\Logger('');
 
        $pdo = $this->getMock(
            '\mreg\Log\MockPDO',
            array('prepare')
        );
        
        $stmt = $this->getMock(
            '\PDOStatement',
            array('execute')
        );
        
        $stmt->expects($this->atLeastOnce())
             ->method('execute')
             ->with(array(
                 'level' => 'ERROR',
                 'message' => 'test',
                 'user' => 'foobar',
                 'ip' => '1.1.1.1',
                 'url' => '\url',
                 'http_method' => 'GET'
             ));
        
        $pdo->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($stmt));        
        
        $handler = new PDOHandler($pdo);

        $logger->pushProcessor(function ($record) {
            $record['extra']['ip'] = '1.1.1.1';
            $record['extra']['url'] = '\url';
            $record['extra']['http_method'] = 'GET';
            $record['extra']['user'] = 'foobar';
            return $record;
        });

        $logger->pushHandler($handler);

        $logger->addError('test');
    }

}

class MockPDO extends \PDO
{
    function __construct(){}
}
