<?php
/**
 * Database Connection and Query Builder
 */

/**
 * Result wrapper class that supports both array and object access
 */
class DbResult implements ArrayAccess {
    private $data;
    
    public function __construct($data) {
        $this->data = (array) $data;
    }
    
    public function offsetExists($offset): bool {
        return isset($this->data[$offset]);
    }
    
    #[\ReturnTypeWillChange]
    public function offsetGet($offset): mixed {
        return $this->data[$offset] ?? null;
    }
    
    public function offsetSet($offset, $value): void {
        $this->data[$offset] = $value;
    }
    
    public function offsetUnset($offset): void {
        unset($this->data[$offset]);
    }
    
    public function __get($key) {
        return $this->data[$key] ?? null;
    }
    
    public function __set($key, $value) {
        $this->data[$key] = $value;
    }
    
    public function __isset($key) {
        return isset($this->data[$key]);
    }
    
    public function __unset($key) {
        unset($this->data[$key]);
    }
    
    /**
     * Convert to array
     */
    public function toArray() {
        return $this->data;
    }
}

class Database {
    private static $instance = null;
    private $pdo;
    private $query;
    private $error = false;
    private $results;
    private $count = 0;

    private function __construct() {
        try {
            // First connect without database to create it if needed
            $dsn = 'mysql:host=' . DB_HOST . ';charset=' . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            
            // Create database if not exists
            $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Now connect to the database
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => false
            ]);
        } catch (PDOException $e) {
            $this->error = true;
            error_log('Database Connection Error: ' . $e->getMessage());
            throw new Exception('Database connection failed');
        }
    }

    /**
     * Get singleton instance
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Execute a query
     */
    public function query($sql, $params = []) {
        $this->error = false;
        
        try {
            $this->query = $this->pdo->prepare($sql);
            
            if ($this->query) {
                $x = 1;
                if (count($params)) {
                    foreach ($params as $param) {
                        $this->query->bindValue($x, $param);
                        $x++;
                    }
                }
                
                if ($this->query->execute()) {
                    $rows = $this->query->fetchAll();
                    // Wrap each result in DbResult for both array and object access
                    $this->results = array_map(function($row) {
                        return new DbResult($row);
                    }, $rows);
                    $this->count = $this->query->rowCount();
                } else {
                    $this->error = true;
                    $this->query = null;
                }
            }
        } catch (PDOException $e) {
            $this->error = true;
            error_log('Query Error: ' . $e->getMessage() . ' | SQL: ' . $sql);
            throw new Exception('Query execution failed');
        }
        
        return $this;
    }

    /**
     * Select rows
     */
    public function select($table, $columns = '*', $where = [], $order = [], $limit = null) {
        $sql = "SELECT {$columns} FROM {$table}";
        
        if (!empty($where)) {
            $sql .= " WHERE ";
            $conditions = [];
            foreach ($where as $key => $value) {
                if (is_array($value)) {
                    $conditions[] = "{$key} {$value[0]} ?";
                } else {
                    $conditions[] = "{$key} = ?";
                }
            }
            $sql .= implode(' AND ', $conditions);
        }
        
        if (!empty($order)) {
            $sql .= " ORDER BY {$order[0]} {$order[1]}";
        }
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        $params = [];
        if (!empty($where)) {
            foreach ($where as $key => $value) {
                if (is_array($value)) {
                    $params[] = $value[1];
                } else {
                    $params[] = $value;
                }
            }
        }
        
        $this->query($sql, $params);
        return $this;
    }

    /**
     * Insert row
     */
    public function insert($table, $data) {
        $keys = array_keys($data);
        $fields = implode(', ', $keys);
        $placeholders = implode(', ', array_fill(0, count($keys), '?'));
        
        $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})";
        
        $params = array_values($data);
        $this->query($sql, $params);
        
        return !$this->error;
    }

    /**
     * Update row
     */
    public function update($table, $data, $where) {
        $sets = [];
        foreach ($data as $key => $value) {
            $sets[] = "{$key} = ?";
        }
        
        $sql = "UPDATE {$table} SET " . implode(', ', $sets);
        
        if (!empty($where)) {
            $sql .= " WHERE ";
            $conditions = [];
            foreach ($where as $key => $value) {
                $conditions[] = "{$key} = ?";
            }
            $sql .= implode(' AND ', $conditions);
        }
        
        $params = array_merge(array_values($data), array_values($where));
        $this->query($sql, $params);
        
        return !$this->error;
    }

    /**
     * Delete row
     */
    public function delete($table, $where) {
        $sql = "DELETE FROM {$table}";
        
        if (!empty($where)) {
            $sql .= " WHERE ";
            $conditions = [];
            foreach ($where as $key => $value) {
                $conditions[] = "{$key} = ?";
            }
            $sql .= implode(' AND ', $conditions);
        }
        
        $params = array_values($where);
        $this->query($sql, $params);
        
        return !$this->error;
    }

    /**
     * Get last inserted ID
     */
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit() {
        return $this->pdo->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollBack() {
        return $this->pdo->rollBack();
    }

    /**
     * Get results
     */
    public function results() {
        return $this->results;
    }

    /**
     * Get first result
     */
    public function first() {
        return !empty($this->results) ? $this->results[0] : null;
    }

    /**
     * Get count
     */
    public function count() {
        return $this->count;
    }

    /**
     * Check for errors
     */
    public function error() {
        return $this->error;
    }
}
