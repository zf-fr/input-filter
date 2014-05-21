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
    public function runAgainst($data, $context = null)
    {
        $filteredData  = [];
        $errorMessages = [];

        // As the input collection can have filters/validators, we first run those globally
        $result = parent::runAgainst($data, $context);

        if (!$result->isValid()) {
            $errorMessages[$this->name] = $result->getErrorMessages();
        }

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
}
