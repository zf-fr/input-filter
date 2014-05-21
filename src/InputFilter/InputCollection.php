<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter;

use ArrayIterator;
use IteratorIterator;
use Zend\Filter\FilterChain;
use Zend\InputFilter\Result\InputFilterResult;
use Zend\InputFilter\ValidationGroup\ValidationGroupInterface;
use Zend\Validator\ValidatorChain;

/**
 * Input collection class
 */
class InputCollection extends Input implements InputCollectionInterface
{
    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var InputCollectionInterface[]|InputInterface[]
     */
    protected $children = [];

    /**
     * @var ValidationGroupInterface[]
     */
    protected $validationGroups = [];

    /**
     * @param FilterChain|null    $filterChain
     * @param ValidatorChain|null $validatorChain
     * @param Factory|null        $factory
     */
    public function __construct(
        FilterChain $filterChain = null,
        ValidatorChain $validatorChain = null,
        Factory $factory = null
    ) {
        parent::__construct($filterChain, $validatorChain);
        $this->factory = $factory ?: new Factory(new InputFilterPluginManager());
    }

    /**
     * Get the input collection factory
     *
     * @return Factory
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * {@inheritDoc}
     */
    public function add($inputOrInputCollection, $name = null)
    {
        // NOTE: you MUST NOT check against Traversable here, because InputCollection is a Traversable itself
        if (is_array($inputOrInputCollection)) {
            $inputOrInputCollection = $this->factory->createFromSpecification($inputOrInputCollection);
        }

        if (null !== $name) {
            $inputOrInputCollection->setName($name);
        }

        $this->children[$inputOrInputCollection->getName()] = $inputOrInputCollection;
    }

    /**
     * {@inheritDoc}
     */
    public function get($name)
    {
        if (!isset($this->children[$name])) {
            throw new Exception\RuntimeException(sprintf(
                'No input or input collection named "%s" was found in input collection "%s"',
                $name,
                $this->getName()
            ));
        }

        return $this->children[$name];
    }

    /**
     * {@inheritDoc}
     */
    public function has($name)
    {
        return isset($this->children[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function remove($name)
    {
        unset($this->children[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function registerValidationGroup(ValidationGroupInterface $validationGroup, $name = 'default')
    {
        $this->validationGroups[$name] = $validationGroup;
    }

    /**
     * {@inheritDoc}
     */
    public function runAgainst($data, $context = null, $validationGroupName = null)
    {
        $filteredData  = [];
        $errorMessages = [];

        // As an input collection can have also validators and filters, we first apply the
        // validation for itself
        if (!$this->validatorChain->isValid($data, $context)) {
            $errorMessages[$this->name] = $this->validatorChain->getMessages();

            if ($this->breakOnFailure()) {
                // We want to break if the input collection fails its own validators, so
                // the filtered data does not exist, hence the empty array()
                return $this->buildInputFilterResult($data, [], $errorMessages);
            }
        }

        if (null === $validationGroupName) {
            $iterator = $this;
        } else {
            $iterator = $this->getFilterIterator($validationGroupName);
        }

        /** @var InputInterface|InputCollectionInterface $inputOrInputCollection */
        foreach ($iterator as $inputOrInputCollection) {
            $name     = $inputOrInputCollection->getName();
            $rawValue = isset($data[$name]) ? $data[$name] : null;

            $inputFilterResult = $inputOrInputCollection->runAgainst($rawValue, $context);

            if (!$inputFilterResult->isValid()) {
                $errorMessages[$name] = $inputFilterResult->getErrorMessages();

                if ($inputOrInputCollection->breakOnFailure()) {
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
        // As the input collection can have filters attached to it, we run those
        // to the data (that was filtered by each input).
        $filteredData = $this->filterChain->filter($filteredData);

        return new InputFilterResult($rawData, $filteredData, $errorMessages);
    }

    /**
     * Get a filter iterator from the validation group name
     *
     * @param  string $validationGroupName
     * @return \FilterIterator
     */
    protected function getFilterIterator($validationGroupName)
    {
        if (!isset($this->validationGroups[$validationGroupName])) {
            // @TODO: throw exception?
        }

        $validationGroup = $this->validationGroups[$validationGroupName];
        
        return $validationGroup->createFilterIterator($this);
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
        return new ArrayIterator($this->children);
    }
}
