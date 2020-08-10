<?php

namespace core\base;

use Core;

class Model extends Component
{
    /**
     * @var array
     */
    protected $_attributes;
    /**
     * @var array
     */
    protected $_attributesOld;
    /**
     * @var array
     */
    protected $_errors;


    /**
     * Loads a data into this model from POST request.
     *
     * @param array $data
     *
     * @return bool
     */
    public function load($data)
    {
        $className = basename(str_replace('\\', '/', get_class($this)));
        if (isset($data[$className])) {
            $data = $data[$className];
        } else {
            return false;
        }

        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }

        return true;
    }

    /**
     * Validates a current model.
     *
     * @return bool
     */
    public function validate()
    {
        foreach ($this->rules() as $rule) {
            list($attributes, $ruleName, $ruleParams) = $rule;
            $ruleAction = 'validate' . ucfirst($ruleName);
            foreach ($attributes as $attribute) {
                $this->{$ruleAction}($attribute, $ruleParams);
                call_user_func_array([$this, $ruleAction], [$attribute, $ruleParams]);
            }
        }

        return count($this->_errors) === 0;
    }

    /**
     * Returns rules of validation.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * Adds a error description to attribute.
     *
     * @param string $attribute
     * @param string $message
     */
    public function setError($attribute, $message)
    {
        $this->_errors[$attribute] = $message;
    }

    /**
     * Returns errors of the current model.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * @param string $attribute
     */
    public function validateRequired($attribute)
    {
        if (empty($this->{$attribute})) {
            $this->setError($attribute, 'Not be empty');
        }
    }

    /**
     * @param string $attribute
     */
    public function validateEmail($attribute)
    {
        if (!empty($this->{$attribute})) {
            if (!filter_var($this->{$attribute}, FILTER_VALIDATE_EMAIL)) {
                $this->setError($attribute, 'Incorrect email');
            }

            if (!preg_match("/^[A-Za-z0-9@.-]+$/", $this->{$attribute})) {
                $this->setError($attribute, 'Incorrect symbols');
            }
        }
    }

    /**
     * @param string $attribute
     */
    public function validateCaptcha($attribute)
    {
        if ($this->{$attribute} != Core::$app->session->get('captcha')) {
            $this->setError($attribute, 'Incorrect captcha');
        }
    }

    /**
     * @param string $attribute
     */
    public function validatePhone($attribute)
    {
        $phone = preg_replace("/[^\d]/", '', $this->{$attribute});
        if (strlen($phone) !== 11) {
            $this->setError($attribute, 'Incorrect phone');
        }
    }

    /**
     * @param string $attribute
     * @param array  $params
     */
    public function validateRepeat($attribute, $params)
    {
        $attributeRepeat = $params['to'];
        if ($this->{$attribute} !== $this->{$attributeRepeat}) {
            $this->setError($attribute, 'Does not match the ' . $attributeRepeat);
        }
    }

    /**
     * @param string $attribute
     * @param array  $params
     */
    public function validateFile($attribute, $params)
    {
        if (empty($_FILES[$attribute]['name'])) {
            return;
        }

        if (isset($params['types'])) {
            if (!in_array($_FILES[$attribute]['type'], $params['types'])) {
                $this->setError(
                    $attribute,
                    'Only [' . implode('|', $params['types']) . '] allowed'
                );
            }
        }

        if (isset($params['sizeMax'])) {
            if ($_FILES[$attribute]['size'] > $params['sizeMax']) {
                $this->setError(
                    $attribute,
                    'Max size ' . $params['sizeMax'] . ' bytes.'
                );
            }
        }
    }

    /**
     * Returns a class name.
     *
     * @return string
     */
    public function getClassName()
    {
        return basename(str_replace('\\', '/', get_class($this)));
    }
}