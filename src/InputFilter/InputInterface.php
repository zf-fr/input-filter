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
use Zend\Validator\ValidatorChain;

/**
 * Interface for an input
 *
 * In InputFilter component, an input represents a single value to filter/validate
 *
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 * @licence MIT
 */
interface InputInterface
{
    const REQUIRED_VALIDATOR_PRIORITY = 1000;

    /**
     * Set the name of the input
     *
     * @param  string $name
     * @return void
     */
    public function setName($name);

    /**
     * Get the name of the input
     *
     * @return string
     */
    public function getName();

    /**
     * Set if the input is required. This is a shortcut of manually adding a NotEmpty validator with
     * a very high priority into the validator chain
     *
     * @param  bool $required
     * @return void
     */
    public function setRequired($required);

    /**
     * Get if the input is required
     *
     * @return bool
     */
    public function isRequired();

    /**
     * Set if the input is allowed to be empty
     *
     * @param  bool $allowEmpty
     * @return void
     */
    public function setAllowEmpty($allowEmpty);

    /**
     * Get if the input is allowed to be empty
     *
     * @return bool
     */
    public function allowEmpty();

    /**
     * Set if the validation should break if one validator fails
     *
     * @param  bool $breakOnFailure
     * @return void
     */
    public function setBreakOnFailure($breakOnFailure);

    /**
     * If set to true, then no other inputs are validated
     *
     * @return bool
     */
    public function breakOnFailure();

    /**
     * Set the filter chain
     *
     * @param  FilterChain $filterChain
     * @return void
     */
    public function setFilterChain(FilterChain $filterChain);

    /**
     * Get the filter chain
     *
     * @return FilterChain
     */
    public function getFilterChain();

    /**
     * Set the validator chain
     *
     * @param  ValidatorChain $validatorChain
     * @return void
     */
    public function setValidatorChain(ValidatorChain $validatorChain);

    /**
     * Get the validator chain
     *
     * @return ValidatorChain
     */
    public function getValidatorChain();

    /**
     * Run against the input for the given data and optional context
     *
     * @param  mixed      $data    Data to validate
     * @param  mixed|null $context An optional context used for validation
     * @return Result\InputFilterResultInterface
     */
    public function runAgainst($data, $context = null);
}
