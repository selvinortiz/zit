<?php

use SelvinOrtiz\Zit\Zit;
use PHPUnit\Framework\TestCase;


class ZitTest extends TestCase
{
    public function test_bind()
    {
        $zit = Zit::make();

        Zit::make()->bind('myServiceGenerator', function () {
            $myObject     = new \stdClass;
            $myObject->id = 'myServiceGeneratorId';

            return $myObject;
        });

        $this->assertTrue($zit->myServiceGenerator() instanceof \stdClass);
        $this->assertTrue($zit->myServiceGenerator()->id === 'myServiceGeneratorId');
    }

    public function test_stash()
    {
        $zit = Zit::make();

        $myObject     = new \stdClass;
        $myObject->id = 'myServiceId';

        $zit->stash('myService', $myObject);

        $this->assertTrue($zit->myService instanceof \stdClass);
        $this->assertTrue($zit->myService() instanceof \stdClass);
        $this->assertTrue($zit->myService()->id === 'myServiceId');
    }

    public function test_extend()
    {
        $zit = Zit::make();

        $zit->extend('myCallable', function () {
            return 12345;
        });

        $this->assertTrue($zit->myCallable() === 12345);
        $this->assertTrue(Zit::myCallable() === 12345);
    }

    public function test_stash_with_same_id_as_protected_property()
    {
        $zit = Zit::make();

        $myObject     = new \stdClass;
        $myObject->id = 'testServices';

        $zit->stash('services', $myObject);

        $this->assertTrue($zit->services instanceof \stdClass);
        $this->assertSame('testServices', $zit->services->id);
    }
}
