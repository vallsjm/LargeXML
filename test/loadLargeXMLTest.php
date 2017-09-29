<?php

namespace Vallsjm\Test;

use Vallsjm\loadLargeXML;
use PHPUnit\Framework\TestCase;

class loadLargeXMLTest extends TestCase
{
	private function getXML()
	{
		return '
			<products>
			    <last_updated>2009-11-30 13:52:40</last_updated>
			    <product>
			        <element_1>foo</element_1>
			        <element_2>foo</element_2>
			        <element_3>foo</element_3>
			        <element_4>foo</element_4>
			    </product>
			    <product>
			        <element_1>bar</element_1>
			        <element_2>bar</element_2>
			        <element_3>bar</element_3>
			        <element_4>bar</element_4>
			    </product>
			</products>		
		';
	}

    public function testLoadLargeXML()
    {
    	$ret1 = [];

        $xml = new loadLargeXML();
        $xml->openXML($this->getXML());

        $xml->addListener('element_1', function($sxe, $xpath) use (&$ret1) {
        	$ret1[] = array(
				'path'  => $xpath,
				'value' => (string) $sxe
        	);
        });

        $xml->addListener('last_updated', function($sxe, $xpath) use (&$ret1) {
        	$ret1[] = array(
				'path'  => $xpath,
				'value' => (string) $sxe
        	);
        });

        $xml->parse();

		$ret2 = array(
			array(
				'path'  => '//products/last_updated',
				'value' => '2009-11-30 13:52:40'
	        ),
		    array(
				'path'  => '//products/product/element_1',
				'value' => 'foo'
		    ),
		    array(
				'path'  => '//products/product/element_1',
				'value' => 'bar'
		    ),
		);

	    $this->assertEquals($ret1, $ret2);
    }
}