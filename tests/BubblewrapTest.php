<?php

namespace Codelayer\Bubblewrap\Tests;

use Codelayer\Bubblewrap\Bubblewrap;
use PHPUnit\Framework\TestCase;

class BubblewrapTest extends TestCase
{
    /**
     * @var Bubblewrap
     */
    private $bubblewrap;

    protected function setUp(): void
    {
        $this->bubblewrap = new Bubblewrap();
    }

    public function testGetCommand()
    {
        $this->assertEquals(
            ['bwrap', 'test'],
            $this->bubblewrap->getCommand(['test'])
        );
    }
    
    public function testExecuteCommand()
    {
        $output = $this->usableSandbox()
            ->exec(['ls', '/']);

        $this->assertTrue($output->isSuccessful());
        $this->assertStringContainsString('lib', $output->getOutput());
    }
    
    public function testClearEnvironment()
    {
        $_ENV['test'] = 'hello world';

        $this->usableSandbox();

        $output = $this->bubblewrap
            ->exec(['env']);

        $this->assertStringContainsString(
            'hello world',
            $output->getOutput()
        );
        
        $output = $this->bubblewrap
            ->clearEnv()
            ->exec(['env']);
        
        $this->assertStringNotContainsString(
            'hello world',
            $output->getOutput()
        );
    }
    
    public function testChangeBinaryPath()
    {
        $this->assertEquals(
            ['/bin/bwrap', 'test'],
            $this->bubblewrap->setBinary('/bin/bwrap')->getCommand(['test'])
        );
    }
    
    private function usableSandbox()
    {
        return $this->bubblewrap
            ->bind('/lib')
            ->bind('/lib64')
            ->bind('/usr')
            ->bind('/bin');
    }
}
