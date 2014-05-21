<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace InputFilter\Result;

/**
 * Simple class that only holds the raw data, data and error messages
 */
class InputFilterResult implements InputFilterResultInterface
{
    /**
     * @var mixed
     */
    protected $rawData;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var mixed
     */
    protected $unknownData;

    /**
     * @var array
     */
    protected $errorMessages = [];

    /**
     * @var bool
     */
    protected $isValid;

    /**
     * Constructor
     *
     * @param mixed $rawData
     * @param mixed $data
     * @param mixed $unknownData
     * @param array $errorMessages
     */
    public function __construct($rawData, $data, $unknownData, array $errorMessages = [])
    {
        $this->rawData       = $rawData;
        $this->data          = $data;
        $this->errorMessages = $errorMessages;
        $this->unknownData   = $unknownData;
        $this->isValid       = empty($errorMessages);
    }

    /**
     * {@inheritDoc}
     */
    public function isValid()
    {
        return $this->isValid;
    }

    /**
     * {@inheritDoc}
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * {@inheritDoc}
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritDoc}
     */
    public function getUnknownData()
    {
        return $this->unknownData;
    }

    /**
     * {@inheritDoc}
     */
    public function getErrorMessages()
    {
        return $this->errorMessages;
    }

    /**
     * Serialize the error messages
     *
     * @return string
     */
    public function serialize()
    {
        return serialize($this->errorMessages);
    }

    /**
     * Unserialize the error messages
     *
     * @param  string $serialized
     * @return array
     */
    public function unserialize($serialized)
    {
        $this->errorMessages = unserialize($serialized);
    }

    /**
     * Return error messages that can be serialized by json_encode
     *
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->errorMessages;
    }
}
