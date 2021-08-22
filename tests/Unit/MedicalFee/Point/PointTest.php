<?php

declare(strict_types=1);

namespace Tests\Unit\MedicalFee\Point;

use IjiUtils\MedicalFee\Point\Point;
use PHPUnit\Framework\TestCase;

class PointTest extends TestCase
{
    public function testCanGenerateNewInstance()
    {
        $this->assertInstanceOf(Point::class, new Point(100));
    }

    public function testCanGenerateNewInstanceByStaticMethod()
    {
        $this->assertEquals(new Point(100), Point::generate(100));
    }

    public function testIsStringable()
    {
        $this->assertEquals("1,000", (string)Point::generate(1000));
    }

    public function testCanConvertIntoInteger()
    {
        $this->assertEquals(1000, Point::generate(1000)->toInt());
    }
}
