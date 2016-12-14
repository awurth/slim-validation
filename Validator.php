<?php

namespace Awurth\Slim\Validation;

use Psr\Http\Message\RequestInterface as Request;
use Respect\Validation\Exceptions\NestedValidationException;

class Validator
{
    /**
     * List of validation errors
     *
     * @var array
     */
    private $errors;

    /**
     * Validate request params with the given rules
     *
     * @param Request $request
     * @param array $rules
     * @param array $messages
     * @return $this
     */
    public function validate(Request $request, array $rules, array $messages = [])
    {

        foreach ($rules as $param => $rule) {
            try {
                $rule->assert($request->getParam($param));
            } catch (NestedValidationException $e) {
                $rulesNames = [];
                foreach ($rule->getRules() as $r)
                    $rulesNames[] = lcfirst((new \ReflectionClass($r))->getShortName());

                $this->errors[$param] = array_filter(array_merge($e->findMessages($rulesNames), $e->findMessages($messages)));
            }
        }

        return $this;
    }

    /**
     * Add an error for param
     *
     * @param string $param
     * @param string $message
     */
    public function addError($param, $message)
    {
        $this->errors[$param][] = $message;
    }

    /**
     * Add errors for param
     *
     * @param string $param
     * @param array $messages
     */
    public function addErrors($param, array $messages)
    {
        foreach ($messages as $message) {
            $this->errors[$param][] = $message;
        }
    }

    /**
     * Get all errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Set all errors
     *
     * @param array $errors
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * Get errors of param
     *
     * @param string $param
     * @return array
     */
    public function getErrorsOf($param)
    {
        return isset($this->errors[$param]) ? $this->errors[$param] : [];
    }

    /**
     * Set errors of param
     *
     * @param string $param
     * @param array $errors
     */
    public function setErrorsOf($param, array $errors)
    {
        $this->errors[$param] = $errors;
    }

    /**
     * Get first error of param
     *
     * @param string $param
     * @return string
     */
    public function getFirst($param)
    {
        if (isset($this->errors[$param])) {
            $first = array_slice($this->errors[$param], 0, 1);
            return array_shift($first);
        }

        return '';
    }

    /**
     * Return true if there is no error
     *
     * @return bool
     */
    public function isValid()
    {
        return empty($this->errors);
    }
}