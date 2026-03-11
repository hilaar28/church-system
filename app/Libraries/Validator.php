<?php
/**
 * Validation Library
 */

class Validator {
    private $data = [];
    private $rules = [];
    private $errors = [];
    private $messages = [];

    /**
     * Constructor
     */
    public function __construct($data = []) {
        $this->data = $data;
        $this->messages = [
            'required' => 'The :field field is required',
            'email' => 'The :field must be a valid email address',
            'min' => 'The :field must be at least :value characters',
            'max' => 'The :field must not exceed :value characters',
            'numeric' => 'The :field must be a number',
            'unique' => 'The :field already exists',
            'match' => 'The :field does not match :value',
            'date' => 'The :field must be a valid date',
            'alpha' => 'The :field must contain only letters',
            'alphanumeric' => 'The :field must contain only letters and numbers',
        ];
    }

    /**
     * Set validation rules
     */
    public function rules($rules) {
        $this->rules = $rules;
        return $this;
    }

    /**
     * Run validation
     */
    public function validate() {
        $this->errors = [];
        
        foreach ($this->rules as $field => $ruleSet) {
            $rules = explode('|', $ruleSet);
            
            foreach ($rules as $rule) {
                $this->validateField($field, $rule);
            }
        }
        
        return empty($this->errors);
    }

    /**
     * Validate single field
     */
    private function validateField($field, $rule) {
        $value = $this->data[$field] ?? null;
        
        // Parse rule and parameters
        if (strpos($rule, ':') !== false) {
            list($ruleName, $param) = explode(':', $rule, 2);
        } else {
            $ruleName = $rule;
            $param = null;
        }
        
        switch ($ruleName) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    $this->addError($field, 'required');
                }
                break;
                
            case 'email':
                if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, 'email');
                }
                break;
                
            case 'min':
                if ($value && strlen($value) < $param) {
                    $this->addError($field, 'min', [':value' => $param]);
                }
                break;
                
            case 'max':
                if ($value && strlen($value) > $param) {
                    $this->addError($field, 'max', [':value' => $param]);
                }
                break;
                
            case 'numeric':
                if ($value && !is_numeric($value)) {
                    $this->addError($field, 'numeric');
                }
                break;
                
            case 'alpha':
                if ($value && !ctype_alpha($value)) {
                    $this->addError($field, 'alpha');
                }
                break;
                
            case 'alphanumeric':
                if ($value && !ctype_alnum($value)) {
                    $this->addError($field, 'alphanumeric');
                }
                break;
                
            case 'date':
                if ($value && !strtotime($value)) {
                    $this->addError($field, 'date');
                }
                break;
                
            case 'match':
                if ($value !== ($this->data[$param] ?? null)) {
                    $this->addError($field, 'match', [':value' => $param]);
                }
                break;
                
            case 'unique':
                // Handle unique validation with table:column format
                if ($param && $value) {
                    $parts = array_pad(explode(',', $param), 3, null);
                    $table = $parts[0];
                    $column = $parts[1];
                    $exceptId = $parts[2];
                    
                    $db = Database::getInstance();
                    $where = [$column => $value];
                    
                    if ($exceptId) {
                        $db->query("SELECT * FROM {$table} WHERE {$column} = ? AND id != ?", [$value, $exceptId]);
                    } else {
                        $db->select($table, '*', $where);
                    }
                    
                    if ($db->count() > 0) {
                        $this->addError($field, 'unique');
                    }
                }
                break;
        }
    }

    /**
     * Add error
     */
    private function addError($field, $rule, $replacements = []) {
        $message = $this->messages[$rule] ?? 'The :field field is invalid';
        
        // Replace placeholders
        $message = str_replace(':field', $field, $message);
        foreach ($replacements as $placeholder => $value) {
            $message = str_replace($placeholder, $value, $message);
        }
        
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        
        $this->errors[$field][] = $message;
    }

    /**
     * Get errors
     */
    public function errors() {
        return $this->errors;
    }

    /**
     * Get first error
     */
    public function firstError() {
        foreach ($this->errors as $fieldErrors) {
            if (!empty($fieldErrors)) {
                return $fieldErrors[0];
            }
        }
        return null;
    }

    /**
     * Get errors as flat array
     */
    public function getFlatErrors() {
        $flat = [];
        foreach ($this->errors as $fieldErrors) {
            $flat = array_merge($flat, $fieldErrors);
        }
        return $flat;
    }

    /**
     * Static validation helper
     */
    public static function validateData($data, $rules) {
        $validator = new Validator($data);
        $validator->rules($rules);
        
        if ($validator->validate()) {
            return [true, []];
        }
        
        return [false, $validator->errors()];
    }

    /**
     * Sanitize input
     */
    public static function sanitize($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }
        
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        
        return $data;
    }
}
