<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_InputFilter
 */

namespace InputFilter\Result;

use PHPUnit_Framework_TestCase as TestCase;
use InputFilter\Result\InputFilterResult;

class InputFilterResutTest extends TestCase
{
    public function testIsValidIfNoErrorMessages()
    {
        $inputFilterResult = new InputFilterResult([], []);
        $this->assertTrue($inputFilterResult->isValid());

        $inputFilterResult = new InputFilterResult([], [], []);
        $this->assertTrue($inputFilterResult->isValid());
    }

    public function testIsInvalidIfErrorMessages()
    {
        $inputFilterResult = new InputFilterResult([], [], ['this' => 'is not valid']);
        $this->assertFalse($inputFilterResult->isValid());
    }

    public function testCanGetUnknownInputs()
    {
        $inputFilterResult = new InputFilterResult(['bar' => 'baz', 'unknown' => 'foo'], ['bar' => 'baz']);
        $this->assertEquals(['unknown' => 'foo'], $inputFilterResult->getUnknownData());
    }

    public function testCanSerializeErrorMessages()
    {
        $inputFilterResult = new InputFilterResult(
            [],
            [],
            ['firstName' => 'Should not be empty']
        );

        $serialized   = serialize($inputFilterResult);
        $unserialized = unserialize($serialized);

        $this->assertEquals(array('firstName' => 'Should not be empty'), $unserialized->getErrorMessages());
    }

    public function testCanSerializeToJson()
    {
        $inputFilterResult = new InputFilterResult(
            [],
            [],
            ['firstName' => 'Should not be empty']
        );

        $encoded = json_encode($inputFilterResult);
        $this->assertEquals('{"firstName":"Should not be empty"}', $encoded);
    }
}
