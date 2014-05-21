<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace InputFilter;

use InputFilter\Result\InputFilterResult;
use Zend\Validator\NotEmpty;
use Zend\Validator\ValidatorChain;
use Zend\Filter\FilterChain;

/**
 * Input
 */
class Input implements InputInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $required = false;

    /**
     * @var bool
     */
    protected $allowEmpty = false;

    /**
     * @var bool
     */
    protected $breakOnFailure = false;

    /**
     * @var FilterChain|null
     */
    protected $filterChain;

    /**
     * @var ValidatorChain|null
     */
    protected $validatorChain;

    /**
     * @param FilterChain    $filterChain
     * @param ValidatorChain $validatorChain
     */
    public function __construct(FilterChain $filterChain = null, ValidatorChain $validatorChain = null)
    {
        $this->filterChain    = $filterChain;
        $this->validatorChain = $validatorChain;
    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {
        $this->name = (string) $name;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function setRequired($required)
    {
        $this->required = (bool) $required;
    }

    /**
     * {@inheritDoc}
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * {@inheritDoc}
     */
    public function setAllowEmpty($allowEmpty)
    {
        $this->allowEmpty = (bool) $allowEmpty;
    }

    /**
     * {@inheritDoc}
     */
    public function allowEmpty()
    {
        return $this->allowEmpty;
    }

    /**
     * {@inheritDoc}
     */
    public function setBreakOnFailure($breakOnFailure)
    {
        $this->breakOnFailure = (bool) $breakOnFailure;
    }

    /**
     * {@inheritDoc}
     */
    public function breakOnFailure()
    {
        return $this->breakOnFailure;
    }

    /**
     * {@inheritDoc}
     */
    public function setFilterChain(FilterChain $filterChain)
    {
        $this->filterChain = $filterChain;
    }

    /**
     * {@inheritDoc}
     */
    public function getFilterChain()
    {
        if (null === $this->filterChain) {
            $this->filterChain = new FilterChain();
        }

        return $this->filterChain;
    }

    /**
     * {@inheritDoc}
     */
    public function setValidatorChain(ValidatorChain $validatorChain)
    {
        if (null === $this->validatorChain) {
            $this->validatorChain = new ValidatorChain();
        }

        return $this->validatorChain;
    }

    /**
     * {@inheritDoc}
     */
    public function getValidatorChain()
    {
        return $this->validatorChain ?: new ValidatorChain();
    }

    /**
     * {@inheritDoc}
     */
    public function runAgainst($value, $context = null)
    {
        $filteredValue  = $this->filterChain->filter($value, $context);
        $validatorChain = $this->getValidatorChain();

        // @TODO: how to use the Required validator?

        if ((null === $filteredValue && $this->allowEmpty)
            || $validatorChain->isValid($filteredValue, $context)
        ) {
            return new InputFilterResult($value, $filteredValue);
        }

        // If it is not valid, we don't want to store the filtered value, as it's
        // incorrect anyway...
        return new InputFilterResult($value, null, $validatorChain->getMessages());
    }
}
