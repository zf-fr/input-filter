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
        $filteredData  = $rawData = [];
        $errorMessages = [];

        // As the input collection can have filters/validators, we first run those globally
        $result = parent::runAgainst($data, $context);

        if (!$result->isValid()) {
            $errorMessages[$this->name] = $result->getErrorMessages();

            if ($this->breakOnFailure()) {
                return $this->buildInputFilterResult($data, [], [], $errorMessages);
            }
        }

        // We may want to actually validate nothing
        if ($this->validationGroup === self::VALIDATE_NONE) {
            return $this->buildInputFilterResult($data, [], [], $errorMessages);
        }

        // Prepare the data according to the validation group
        if (!empty($data)) {
            $data = $this->prepareData($data);
        }

        /** @var InputInterface $input */
        foreach ($this->getIterator() as $input) {
            $name   = $input->getName();
            $exists = array_key_exists($name, $data);

            // If data does not exist in the given payload, and that it's not required, we can
            // skip it as it's not needed
            if (!$exists && !$input->isRequired()) {
                continue;
            }

            $rawValue          = isset($data[$name]) ? $data[$name] : null;
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
     * Build a validation result from the given data, filtered data and error messages
     *
     * @param  mixed $data
     * @param  mixed $filteredData
     * @param  array $errorMessages
     * @return Result\InputFilterResultInterface
     */
    protected function buildInputFilterResult($data, $filteredData, array $errorMessages)
    {
        // We are doing a bit of work here for building the result. Basically, $rawData will contain
        // values for the inputs given, unfiltered. $filteredData will contain the exact same keys, but
        // filtered. Finally, $unknownData will contain values for inputs that the input collection
        // does not know (they are therefore NOT validated)
        //
        // Please note that if you use a validation group, any given value, even if it is in the
        // given data, it won't appear in the validation result

        $unknownData = [];

        if (is_array($data)) {
            $unknownData  = array_diff_key($data, $this->inputs);
            $data         = array_intersect_key($data, $this->inputs);

            // If we have validation group as array, we must only keep those ones
            if (is_array($this->validationGroup)) {
                $data = array_intersect_key($data, array_flip($this->validationGroup));
            }

            $filteredData = array_intersect_key($filteredData, $data);
        }

        return new InputFilterResult($data, $filteredData, $unknownData, $errorMessages);
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
            $indexedBy       = is_string($key) ? $key : $value;
            $inputCollection = $this->inputs[$indexedBy];

            if ($inputCollection instanceof InputCollection) {
                $inputCollection->setValidationGroup(is_int($key) ? self::VALIDATE_ALL : $value);
            }
        }

        return $data;
    }
}
