<?php
/**
 * Base Model Class
 */

class Model implements ArrayAccess {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];
    protected $attributes = [];

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Magic getter to retrieve attributes
     */
    public function __get($key) {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Magic setter to set attributes
     */
    public function __set($key, $value) {
        $this->attributes[$key] = $value;
    }

    /**
     * Magic isset to check if attribute is set
     */
    public function __isset($key) {
        return isset($this->attributes[$key]);
    }

    /**
     * ArrayAccess: Check if offset exists
     */
    public function offsetExists($offset): bool {
        return isset($this->attributes[$offset]);
    }

    /**
     * ArrayAccess: Get offset value
     */
    public function offsetGet($offset): mixed {
        return $this->attributes[$offset] ?? null;
    }

    /**
     * ArrayAccess: Set offset value
     */
    public function offsetSet($offset, $value): void {
        if (is_null($offset)) {
            $this->attributes[] = $value;
        } else {
            $this->attributes[$offset] = $value;
        }
    }

    /**
     * ArrayAccess: Unset offset
     */
    public function offsetUnset($offset): void {
        unset($this->attributes[$offset]);
    }

    /**
     * Convert model to array
     */
    public function toArray() {
        return $this->attributes;
    }

    /**
     * Set attributes from database result
     */
    public function setAttributes($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $this->attributes[$key] = $value;
            }
        }
        return $this;
    }

    /**
     * Create a new instance and populate attributes
     */
    public static function hydrate($data) {
        $model = new static();
        if (is_array($data) || is_object($data)) {
            // If it's a DbResult, use toArray() method
            if (method_exists($data, 'toArray')) {
                $data = $data->toArray();
            } else {
                $data = (array) $data;
            }
            foreach ($data as $key => $value) {
                $model->attributes[$key] = $value;
            }
        }
        return $model;
    }

    /**
     * Hydrate a collection of models
     */
    public static function hydrateAll($results) {
        $models = [];
        if (is_array($results) || is_object($results)) {
            foreach ($results as $data) {
                $models[] = self::hydrate($data);
            }
        }
        return $models;
    }

    /**
     * Find by ID
     */
    public function find($id) {
        $this->db->select($this->table, '*', [$this->primaryKey => $id]);
        $result = $this->db->first();
        return $result ? self::hydrate($result) : null;
    }

    /**
     * Find all
     */
    public function all($order = [], $limit = null) {
        $this->db->select($this->table, '*', [], $order, $limit);
        return self::hydrateAll($this->db->results());
    }

    /**
     * Find by field
     */
    public function findBy($field, $value) {
        $this->db->select($this->table, '*', [$field => $value]);
        $result = $this->db->first();
        return $result ? self::hydrate($result) : null;
    }

    /**
     * Find many by field
     */
    public function findAllBy($field, $value, $order = []) {
        $this->db->select($this->table, '*', [$field => $value], $order);
        return self::hydrateAll($this->db->results());
    }

    /**
     * Create record
     */
    public function create($data) {
        $fillableData = [];
        
        foreach ($this->fillable as $field) {
            if (isset($data[$field])) {
                $fillableData[$field] = $data[$field];
            }
        }
        
        if ($this->db->insert($this->table, $fillableData)) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }

    /**
     * Update record
     */
    public function update($id, $data) {
        $fillableData = [];
        
        foreach ($this->fillable as $field) {
            if (isset($data[$field])) {
                $fillableData[$field] = $data[$field];
            }
        }
        
        return $this->db->update($this->table, $fillableData, [$this->primaryKey => $id]);
    }

    /**
     * Delete record
     */
    public function delete($id) {
        return $this->db->delete($this->table, [$this->primaryKey => $id]);
    }

    /**
     * Count records
     */
    public function count($where = []) {
        if (empty($where)) {
            $this->db->query("SELECT COUNT(*) as count FROM {$this->table}");
        } else {
            $conditions = [];
            $params = [];
            foreach ($where as $key => $value) {
                $conditions[] = "{$key} = ?";
                $params[] = $value;
            }
            $this->db->query("SELECT COUNT(*) as count FROM {$this->table} WHERE " . implode(' AND ', $conditions), $params);
        }
        
        $result = $this->db->first();
        return $result['count'] ?? 0;
    }

    /**
     * Paginate
     */
    public function paginate($page = 1, $perPage = 20, $where = [], $order = []) {
        $offset = ($page - 1) * $perPage;
        
        $conditions = [];
        $params = [];
        foreach ($where as $key => $value) {
            $conditions[] = "{$key} = ?";
            $params[] = $value;
        }
        
        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
        $orderClause = !empty($order) ? "ORDER BY {$order[0]} {$order[1]}" : '';
        
        // Get total count
        $this->db->query("SELECT COUNT(*) as total FROM {$this->table} {$whereClause}", $params);
        $total = $this->db->first()['total'] ?? 0;
        
        // Get results
        $this->db->query("SELECT * FROM {$this->table} {$whereClause} {$orderClause} LIMIT {$offset}, {$perPage}", $params);
        $results = $this->db->results();
        
        return [
            'data' => self::hydrateAll($results),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Query builder - returns model instances
     */
    public function query($sql, $params = []) {
        $this->db->query($sql, $params);
        return self::hydrateAll($this->db->results());
    }
    
    /**
     * Query builder - returns raw array results
     */
    public function queryRaw($sql, $params = []) {
        $this->db->query($sql, $params);
        return $this->db->results();
    }
}
