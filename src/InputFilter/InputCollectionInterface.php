<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace InputFilter;

use IteratorAggregate;

/**
 * Input Collection interface
 *
 * An input collection (called "input filter" in ZF2) allows to filter/validate multiple values
 *
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 * @licence MIT
 */
interface InputCollectionInterface extends InputInterface, IteratorAggregate
{
    /** Validation group constants */
    const VALIDATE_ALL  = 0;
    const VALIDATE_NONE = 1;

    /**
     * Add an input or another input collection (if no name was set, it will extract the one set in
     * the input or input collection)
     *
     * @param  InputInterface $input
     * @return void
     */
    public function addInput(InputInterface $input);

    /**
     * Get an input by name
     *
     * @param  string $name
     * @return InputInterface
     */
    public function getInput($name);

    /**
     * Check if the input collection contains an input with the name given
     *
     * @param  string $name
     * @return bool
     */
    public function hasInput($name);

    /**
     * Remove the input with the given name
     *
     * @param  string $name
     * @return void
     */
    public function removeInput($name);

    /**
     * Set the validation group
     *
     * @param  int|array $validationGroup
     * @return void
     */
    public function setValidationGroup($validationGroup);

    /**
     * Get the validation group
     *
     * @return int|array
     */
    public function getValidationGroup();
}
