<?php
    
    use App\Bags\Calculate;
    use PHPUnit\Framework\TestCase;
    
    class CalculationTest extends TestCase
    {
        public function testIncreaseQuantityInProduct()
        {
            $calc = new Calculate();
            $calc->setBagCost ( 72 );
            $calc->setMeasurement ( "meter" );
            $calc->setDepthMeasurement ( "centimeter" );
            $calc->setWidth ( 100 );
            $calc->setLength ( 82 );
            $calc->setDepth ( 26 );
            $result = $calc->calc ();
        
            self::assertSame(214920.0, $result->totalCost);
        }
    }