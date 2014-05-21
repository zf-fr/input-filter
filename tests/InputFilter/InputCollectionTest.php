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

namespace InputFilterTest;

use InputFilter\Exception\RuntimeException;
use InputFilter\Input;
use InputFilter\InputCollection;
use Zend\Validator\NotEmpty;

class InputCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $input = new InputCollection();

        $this->assertNull($input->getName());
        $this->assertFalse($input->isRequired());
        $this->assertFalse($input->allowEmpty());
        $this->assertFalse($input->breakOnFailure());
    }

    public function testThrowExceptionIfInputDoNotHaveName()
    {
        $this->setExpectedException(RuntimeException::class);

        $input           = new Input();
        $inputCollection = new InputCollection();

        $inputCollection->addInput($input);
    }

    public function testThrowExceptionIfTryingToRetrieveUnexistingInput()
    {
        $this->setExpectedException(RuntimeException::class);

        $inputCollection = new InputCollection();
        $inputCollection->getInput('foo');
    }

    public function testCanAddInput()
    {
        $input = new Input();
        $input->setName('foo');

        $inputCollection = new InputCollection();
        $inputCollection->setName('bar');

        $this->assertFalse($inputCollection->hasInput('foo'));
        $inputCollection->addInput($input);
        $this->assertTrue($inputCollection->hasInput('foo'));
        $this->assertSame($input, $inputCollection->getInput('foo'));
    }

    public function dataProvider()
    {
        return [
            [
                'validation_group'     => InputCollection::VALIDATE_NONE,
                'data'                 => [],
                'result_raw_data'      => [],
                'result_filtered_data' => [],
                'result_unknown_data'  => [],
                'is_valid'             => true
            ],

            [
                'validation_group'     => InputCollection::VALIDATE_ALL,
                'data'                 => ['email' => 'test@example.com', 'first_name' => 'Marco'],
                'result_raw_data'      => [],
                'result_filtered_data' => [],
                'result_unknown_data'  => [],
                'is_valid'             => true
            ]
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testBehaviour(
        $validationGroup,
        array $data,
        array $resultRawData,
        array $resultFilteredData,
        array $resultUnknownData,
        $isValid
    ) {
        /*$inputCollection = new InputCollection();
        $inputCollection->setName('user');

        // We add one input that is required, one that is optional, and a nested input collection
        $input1 = new Input();
        $input1->setName('email');
        $input1->getValidatorChain()->attachByName(NotEmpty::class);

        $input2 = new Input();
        $input2->setName('first_name');

        $addressInputCollection = new InputCollection();
        $addressInputCollection->setName('address');

        $input3 = new Input();
        $input3->setName('city');
        $addressInputCollection->addInput($input3);

        $inputCollection->addInput($input1);
        $inputCollection->addInput($input2);
        $inputCollection->addInput($addressInputCollection);

        $inputCollection->setValidationGroup($validationGroup);
        $result = $inputCollection->runAgainst($data);

        $this->assertEquals($isValid, $result->isValid());
        $this->assertEquals($resultRawData, $result->getRawData());
        $this->assertEquals($resultFilteredData, $result->getData());
        $this->assertEquals($resultUnknownData, $result->getData());*/
    }
}
