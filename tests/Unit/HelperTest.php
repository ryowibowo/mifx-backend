<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    /**
     * test if usd_to_rupiah_format defined
     *
     * @return void
     */
    public function testHasUsdToRupiahFormatConverter()
    {
        $this->assertTrue(function_exists('usd_to_rupiah_format'));
    }

    /**
     * test if usd_to_rupiah_format has 0 input
     * 
     * @dataProvider usdToRupiahDataProvider
     * @return void
     */
    public function testUsdToRupiahFormatConverterInputZero(mixed $input, string $output)
    {
        $this->assertEquals(usd_to_rupiah_format($input), $output);
    }

    public function usdToRupiahDataProvider()
    {
        return [
            [null, 'Rp 0,00'],
            [0, 'Rp 0,00'],
            [1, 'Rp 14.000,00'],
            [10, 'Rp 140.000,00'],
            [20, 'Rp 280.000,00'],
        ];
    }
}
