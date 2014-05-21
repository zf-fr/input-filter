<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace InputFilter;

use ArrayIterator;
use InputFilter\Result\InputFilterResult;
use InputFilter\ValidationGroup\ValidationGroupFilter;

/**
 * Input collection class
 */
class InputCollection extends Input implements InputCollectionInterface
{
    /**
     * @var InputInterface[]
     */
    protected $inputs = [];

    /**
     * @var int|array
     */
    protected $validationGroup = self::VALIDATE_ALL;

    /**
     * {@inheritDoc}
     */
    public function addInput(InputInterface $input)
    {
        if (null === $input->getName()) {
            throw new Exception\RuntimeException(sprintf(
                'Input of type "%s" does not have a name',
                get_class($input)
            ));
        }

        $this->inputs[$input->getName()] = $input;
    }

    /**
     * {@inheritDoc}
     */
    public function getInput($name)
    {
        if (!isset($this->inputs[$name])) {
            throw new Exception\RuntimeException(sprintf(
                'No input named "%s" was found in input collection "%s"',
                $name,
                $this->getName()
            ));
        }

        return $this->inputs[$name];
    }

    /**
     * {@inheritDoc}
     */
    public function hasInput($name)
    {
        return isset($this->inputs[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function removeInput($name)
    {
        unset($this->inputs[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function setValidationGroup($validationGroup)
    {
        $this->validationGroup = $validationGroup;
    }

    /**
     * {@inheritDoc}
     */
    public function getValidationGroup()
    {
        return $this->validationGroup;
    }

    /**
     * {@inheritDoc}
     */
    public function runAgainst($data, $context = null)
    {
        $filteredData  = [];
        $errorMessages = [];

        // As the input collection can have filters/validators, we first run those globally
        $result = parent::runAgainst($data, $context);

        if (!$result->isValid()) {
            $errorMessages[$this->name] = $result->getErrorMessages();
        }

        // We may want to actually validate nothing
        if ($this->validationGroup === self::VALIDATE_NONE) {
            return $this->buildInputFilterResult($data, [], $errorMessages);
        }

        // Prepare the data according to the validation group
        $data = $this->prepareData($data);

        /** @var InputInterface $input */
        foreach ($this->getIterator() as $input) {
            $name     = $input->getName();
            $rawValue = isset($data[$name]) ? $data[$name] : null;

            $inputFilterResult = $input->runAgainst($rawValue, $context);

            if (!$inputFilterResult->isValid()) {
                $errorMessages[$name] = $inputFilterResult->getErrorMessages();

                if ($input->breakOnFailure()) {
                    break;
                }
            } else {
                $filteredData[$name] = $inputFilterResult->getData();
            }
        }

        return $this->buildInputFilterResult($data, $filteredData, $errorMessages);
    }

    /**
     * Build a validation result from the raw data, filtered data and error messages
     *
     * @param  array $rawData
     * @param  array $filteredData
     * @param  array $errorMessages
     * @return Result\InputFilterResultInterface
     */
    protected function buildInputFilterResult(array $rawData, array $filteredData, array $errorMessages)
    {
        return new InputFilterResult($rawData, $filteredData, $errorMessages);
    }

    /**
     * --------------------------------------------------------------------------------
     * Implementation of IteratorAggregate interface
     * --------------------------------------------------------------------------------
     */

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->inputs);
    }

    /**
     * @param  mixed $data
     * @return array
     */
    protected function prepareData($data)
    {
        // If validation group is set to validate all, then we have nothing to do
        if ($this->validationGroup === self::VALIDATE_ALL) {
            return $data;
        }

        // Otherwise, we need to prepare the validation group, and filtering keys that are not in
        // the validation group
        foreach ($this->validationGroup as $key => $value) {
            if (is_string($key) && isset($this->inputs[$key]) && $this->inputs[$key] instanceof InputCollection) {
                $this->inputs[$key]->setValidationGroup($value);
                continue;
            }

            if (is_int($key) && isset($this->inputs[$value]) && $this->inputs[$value] instanceof InputCollection) {
                $this->inputs[$value]->setValidationGroup(self::VALIDATE_ALL);
                continue;
            }
        }

        return $data;
    }
}
