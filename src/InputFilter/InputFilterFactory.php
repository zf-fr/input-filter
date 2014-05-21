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
use Zend\Filter\FilterPluginManager;
use Zend\Validator\ValidatorChain;
use Zend\Validator\ValidatorPluginManager;

/**
 * This class can be used to programatically create input and/or input collection
 *
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 * @licence MIT
 */
class InputFilterFactory
{
    /**
     * @var ValidatorPluginManager
     */
    protected $validatorManager;

    /**
     * @var FilterPluginManager
     */
    protected $filterManager;

    /**
     * @param ValidatorPluginManager $validatorManager
     * @param FilterPluginManager    $filterManager
     */
    public function __construct(ValidatorPluginManager $validatorManager, FilterPluginManager $filterManager)
    {
        $this->validatorManager = $validatorManager;
        $this->filterManager    = $filterManager;
    }

    /**
     * Create an input or input collection (based on the type)
     *
     * @param  array $specification
     * @return InputInterface
     * @throws Exception\InvalidArgumentException
     */
    public function create(array &$specification)
    {
        $type = isset($specification['type']) ? $specification['type'] : Input::class;

        if ($type instanceof InputInterface) {
            return $this->createInput($specification);
        }

        if ($type instanceof InputCollectionInterface) {
            return $this->createInputCollection($specification);
        }

        throw new Exception\InvalidArgumentException(sprintf(
            'Input filter factory could not create any input with type "%s"',
            $type
        ));
    }

    /**
     * Create an input from a specification. Example of specification:
     *
     * $specification = [
     *     'type'             => 'MyCustomInput', // default to InputFilter\Input
     *     'name'             => 'inputName',
     *     'required'         => true,
     *     'allow_empty'      => true,
     *     'break_on_failure' => false,
     *     'validators'       => [
     *         ['name' => 'MyValidator']
     *     ],
     *     'filters'          => [
     *         ['name' => 'MyFilter']
     *     ]
     * ]
     *
     * @param  array &$specification
     * @return InputInterface
     */
    public function createInput(array &$specification)
    {
        $type = isset($specification['type']) ? $specification['type'] : Input::class;

        /** @var InputInterface $input */
        $input = new $type();

        foreach ($specification as $key => $value) {
            switch ($key) {
                case 'name':
                    $input->setName($value);
                    unset($specification['name']);

                    break;
                case 'required':
                    $input->setRequired($value);
                    unset($specification['required']);

                    break;
                case 'allow_empty':
                    $input->setAllowEmpty($value);
                    unset($specification['allow_empty']);

                    break;
                case 'break_on_failure':
                    $input->setBreakOnFailure($value);
                    unset($specification['break_on_failure']);

                    break;
                case 'validators':
                    $input->setValidatorChain($this->createValidatorChain($value));
                    unset($specification['validators']);

                    break;
                case 'filters':
                    $input->setFilterChain($this->createFilterChain($value));
                    unset($specification['filters']);

                    break;
            }
        }

        return $input;
    }

    /**
     * Create an input collection. Example of specification:
     *
     * $specification = [
     *     'type'       => 'MyCustomInputCollection', // default to InputFilter\InputCollection
     *     'name'       => 'inputCollectionName',
     *     'inputs'     => [ // list of input or input collection specification ],
     *     'validators' => [
     *         ['name' => 'MyValidator']
     *     ],
     *     'filters'          => [
     *         ['name' => 'MyFilter']
     *     ]
     * ]
     *
     * @param  array &$specification
     * @return InputCollectionInterface
     */
    public function createInputCollection(array &$specification)
    {
        $specification['type'] = isset($specification['type']) ? $specification['type'] : InputCollection::class;

        /** @var InputCollectionInterface $inputCollection */
        $inputCollection = $this->createInput($specification);

        foreach ($specification as $key => $value) {
            switch ($key) {
                case 'inputs':
                    foreach ($value as $inputSpecification) {
                        $inputCollection->addInput($this->createInput($inputSpecification));
                    }
                    unset($specification['inputs']);

                    break;
            }
        }

        return $inputCollection;
    }

    /**
     * Create a validator chain from specification
     *
     * @param  array $specification
     * @return ValidatorChain
     */
    protected function createValidatorChain(array $specification)
    {
        $validatorChain = new ValidatorChain();
        $validatorChain->setPluginManager($this->validatorManager);

        foreach ($specification as $validatorSpecification) {
            $validatorChain->attachByName(
                $validatorSpecification['name'],
                isset($validatorSpecification['options']) ? $validatorSpecification['options'] : []
            );
        }

        return $validatorChain;
    }

    /**
     * Create a filter chain from specification
     *
     * @param  array $specification
     * @return FilterChain
     */
    protected function createFilterChain(array $specification)
    {
        $filterChain = new FilterChain();
        $filterChain->setPluginManager($this->filterManager);

        foreach ($specification as $filerSpecification) {
            $filterChain->attachByName(
                $filerSpecification['name'],
                isset($filerSpecification['options']) ? $filerSpecification['options'] : []
            );
        }

        return $filterChain;
    }
}
