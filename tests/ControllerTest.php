<?php

class ControllerTest extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        $unitTesting = true;

        $testEnvironment = 'testing';

        return require __DIR__.'/../../../../bootstrap/start.php';
    }
/*
    public function testAuth()
    {
        $this->call('GET', 'admin/home');
        $this->assertRedirectedTo(URL::to('admin/login'));
    }

    public function testLogin()
    {
        $this->call('GET', 'admin/login');
        $this->assertRequestOk();
    }
*/
    /**
     * Assert that the last request was successfull
     *
     * @return boolean
     */
    public function assertRequestOk()
    {
        $this->assertTrue($this->client->getResponse()->isOK());
    }

    public function assertViewReceives($prop, $val = null)
    {
        $response   = $this->client->getResponse();
        $prop       = $response->getOriginalContent()->$prop;

        if ($val) {
            return $this->assertEquals($val, $prop);
        }
        $this->assertTrue(!! $prop);
    }

    public function assertRedirectedTo($uri)
    {
        $response   = $this->client->getResponse();
        $redirectedTo = $response->headers->get('Location');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals($uri, $redirectedTo);
    }
}
