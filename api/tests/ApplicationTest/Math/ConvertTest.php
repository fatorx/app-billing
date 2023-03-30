<?php

namespace ApplicationTest\Math;

use PHPUnit\Framework\TestCase;
use Application\Math\Convert;

class ConvertTest extends TestCase
{
    public function testFormatFloatToNumber()
    {
        $compare = 'R$ 1,23';
        $number = Convert::formatFloatToNumber(1.23);

        $this->assertEquals($compare, $number);
    }

    public function testFormatFloatToNumberNoSymbol()
    {
        $compare = '1,23';
        $number = Convert::formatFloatToNumber(1.23, false);

        $this->assertEquals($compare, $number);
    }

    public function testFormatFloatToNumberBr()
    {
        $compare = '1,23';
        $number = Convert::formatFloatToNumberBr(1.23);

        $this->assertEquals($compare, $number);
    }

    public function testFormatNumberToFloat()
    {
        $compare = 1.23;
        $number = Convert::formatNumberToFloat('R$ 1,23');

        $this->assertEquals($compare, $number);
    }

    public function testFormatNumberToFloatAux()
    {
        $compare = 1.23;
        $number = Convert::formatNumberToFloatAux('1,23');

        $this->assertEquals($compare, $number);
    }
}
