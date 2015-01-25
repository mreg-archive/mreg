<?php
namespace itbz\phplibaddress\Composer;

class BreviatorTest extends \PHPUnit_Framework_TestCase
{
    public function testConcatenate()
    {
        $brev = new Breviator;

        $this->assertEquals(
            'Mr Karl Hannes Gustav Forsgard',
            $brev->concatenate(
                'Karl Hannes Gustav',
                'Forsgard',
                'Mr'
            )
        );

        $this->assertEquals(
            'Mr Karl H G Forsgard Eriksson',
            $brev->concatenate(
                'Karl Hannes Gustav',
                'Forsgard Eriksson',
                'Mr'
            )
        );

        $this->assertEquals(
            'Mr Karl Forsgard Eriksson Eriksson',
            $brev->concatenate(
                'Karl Hannes Gustav',
                'Forsgard Eriksson Eriksson',
                'Mr'
            )
        );

        $this->assertEquals(
            'Forsgard Eriksson Eriksson Eriksson',
            $brev->concatenate(
                'Karl Hannes Gustav',
                'Forsgard Eriksson Eriksson Eriksson',
                'Mr'
            )
        );

        $this->assertEquals(
            'Eriksson Eriksson Eriksson Eriksson',
            $brev->concatenate(
                'Karl Hannes Gustav',
                'Forsgard Eriksson Eriksson Eriksson Eriksson',
                'Mr'
            )
        );

        $this->assertEquals(
            'ErikssonErikssonErikssonErikssonErik',
            $brev->concatenate(
                'Karl Hannes Gustav',
                'Forsgard ErikssonErikssonErikssonErikssonEriksson',
                'Mr'
            )
        );
    }
}
