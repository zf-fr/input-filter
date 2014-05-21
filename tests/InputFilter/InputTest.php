<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace InputFilter;

use Zend\Filter\FilterChain;
use Zend\Filter\Digits as DigitsFilter;
use Zend\Validator\Digits as DigitsValidator;
use Zend\Validator\ValidatorChain;

class InputTest extends \PHPUnit_Framework_TestCase
{
    public function testAssertDefaults()
    {
        $input = new Input();

        $this->assertNull($input->getName());
        $this->assertFalse($input->isRequired());
        $this->assertFalse($input->allowEmpty());
        $this->assertFalse($input->breakOnFailure());
    }

    public function testSettersAndGetters()
    {
        $input = new Input();

        $input->setName('foo');
        $input->setRequired(true);
        $input->setAllowEmpty(true);
        $input->setBreakOnFailure(true);

        $this->assertEquals('foo', $input->getName());
        $this->assertTrue($input->isRequired());
        $this->assertTrue($input->allowEmpty());
        $this->assertTrue($input->breakOnFailure());
    }

    public function testCanLazyLoadValidatorChain()
    {
        $input = new Input();
        $this->assertInstanceOf(ValidatorChain::class, $input->getValidatorChain());
    }

    public function testCanLazyLoadFilterChain()
    {
        $input = new Input();
        $this->assertInstanceOf(FilterChain::class, $input->getFilterChain());
    }

    public function dataProvider()
    {
        return [
            [
                'data'          => null,
                'required'      => false,
                'allow_empty'   => false,
                'validators'    => [],
                'filters'       => [],
                'filtered_data' => null,
                'is_valid'      => true
            ],

            // Assert that it fails if is required
            [
                'data'          => null,
                'required'      => true,
                'allow_empty'   => false,
                'validators'    => [],
                'filters'       => [],
                'filtered_data' => null,
                'is_valid'      => false
            ],

            // Assert that it succeeds if is required but allow empty
            [
                'data'          => null,
                'required'      => true,
                'allow_empty'   => true,
                'validators'    => [],
                'filters'       => [],
                'filtered_data' => null,
                'is_valid'      => true
            ],

            // Assert that filters are executed before validation
            [
                'data'          => 'bar123',
                'required'      => true,
                'allow_empty'   => false,
                'validators'    => [
                    new DigitsValidator()
                ],
                'filters'       => [
                    new DigitsFilter()
                ],
                'filtered_data' => '123',
                'is_valid'      => true
            ],

            // Assert that filtered data is not set if validation fails
            [
                'data'          => 'bar123',
                'required'      => true,
                'allow_empty'   => false,
                'validators'    => [
                    new DigitsValidator()
                ],
                'filters'       => [],
                'filtered_data' => null,
                'is_valid'      => false
            ]
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testFunctional($data, $required, $allowEmpty, $validators, $filters, $filteredData, $isValid)
    {
        $input = new Input();
        $input->setRequired($required);
        $input->setAllowEmpty($allowEmpty);

        $validatorChain = $input->getValidatorChain();
        foreach ($validators as $validator) {
            $validatorChain->attach($validator);
        }

        $filterChain = $input->getFilterChain();
        foreach ($filters as $filter) {
            $filterChain->attach($filter);
        }

        $result = $input->runAgainst($data);

        $this->assertEquals($isValid, $result->isValid());
        $this->assertEquals($data, $result->getRawData());
        $this->assertEquals($filteredData, $result->getData());
    }
}
