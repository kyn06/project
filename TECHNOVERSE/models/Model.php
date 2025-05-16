<?php

class Model {
    protected static $conn;
    protected static $table;

    public static function setConnection($conn) {
        self::$conn = $conn;
    }

    protected static function all() {
        try {
            $sql = "SELECT * FROM " . static::$table;
            $stmt = self::$conn->query($sql);
            $rows = $stmt->fetchAll();
            return count($rows) > 0 ? $rows : null;
        } catch (PDOException $e) {
            die("Query failed: " . $e->getMessage());
        }
    }

    protected static function find($id) {
        try {
            $sql = "SELECT * FROM " . static::$table . " WHERE id = :id";
            $stmt = self::$conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $row = $stmt->fetch();
            return $row ? $row : null;
        } catch (PDOException $e) {
            die("Query failed: " . $e->getMessage());
        }
    }

    protected static function create(array $data) {
        try {
            $columns = implode(",", array_keys($data));
            $values = implode(",", array_map(fn($key) => ":$key", array_keys($data)));

            $sql = "INSERT INTO " . static::$table . " ($columns) VALUES ($values)";
            $stmt = self::$conn->prepare($sql);

            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }

            $stmt->execute();
            $id = self::$conn->lastInsertId();

            return self::find($id);
        } catch (PDOException $e) {
            die("Insert failed: " . $e->getMessage());
        }
    }

    protected static function updateById($id, array $data) {
        try {
            $set = implode(", ", array_map(fn($key) => "$key = :$key", array_keys($data)));

            $sql = "UPDATE " . static::$table . " SET $set WHERE id = :id";
            $stmt = self::$conn->prepare($sql);

            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }

            $stmt->bindValue(':id', $id);
            $stmt->execute();

            return self::find($id);
        } catch (PDOException $e) {
            die("Update failed: " . $e->getMessage());
        }
    }

    protected static function deleteById($id) {
        try {
            $sql = "DELETE FROM " . static::$table . " WHERE id = :id";
            $stmt = self::$conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            die("Delete failed: " . $e->getMessage());
        }
    }

    protected static function countAll(): int {
        try {
            $sql = "SELECT COUNT(*) as total FROM " . static::$table;
            $stmt = self::$conn->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error counting records: " . $e->getMessage());
            return 0;
        }
    }

    protected static function countNew($startDate, $endDate) {
        try {
            $query = "SELECT COUNT(*) as total FROM " . static::$table . " WHERE created_at BETWEEN :startDate AND :endDate";
            $stmt = self::$conn->prepare($query);
            $stmt->bindValue(':startDate', $startDate);
            $stmt->bindValue(':endDate', $endDate);
            $stmt->execute();
            $row = $stmt->fetch();
            return $row['total'];
        } catch (PDOException $e) {
            die("Query failed: " . $e->getMessage());
        }
    }

    protected static function countByStatus($status) {
        try {
            $query = "SELECT COUNT(*) as total FROM " . static::$table . " WHERE status = :status";
            $stmt = self::$conn->prepare($query);
            $stmt->bindValue(':status', $status);
            $stmt->execute();
            $row = $stmt->fetch();
            return $row['total'];
        } catch (PDOException $e) {
            die("Query failed: " . $e->getMessage());
        }
    }

    protected static function where($column, $operator, $value) {
        try {
            $sql = "SELECT * FROM " . static::$table
            . " WHERE $column $operator :value";

            $stmt = self::$conn->prepare($sql);

            $stmt->bindValue(':value', $value);

            $stmt->execute();

            $rows = $stmt->fetchAll();

            return count($rows) > 0 ? $rows : null;
        }
        catch (PDOException $e) {
            die("Error fetching data: " . $e->getMessage());
        }
    }

    public static function getJoinedData(array $joins = [], string $orderBy = '') {
        try {
            $tableAlias = 'a';
            $sql = "SELECT $tableAlias.*, ";

            foreach ($joins as $alias => $on) {
                $aliasName = explode(' ', $alias)[1] ?? $alias; 
                $sql .= "$aliasName.*, ";
            }

            $sql = rtrim($sql, ", ");

            $sql .= " FROM " . static::$table . " $tableAlias ";

            foreach ($joins as $joinTable => $onCondition) {
                $sql .= "JOIN $joinTable ON $onCondition ";
            }

            if ($orderBy) {
                $sql .= "ORDER BY $orderBy";
            }

            $stmt = self::$conn->query($sql);
            $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $rows ?: [];
        } catch (PDOException $e) {
            die("Query failed: " . $e->getMessage());
        }
    }

    public static function getByConditions(array $conditions = [], array $joins = [], string $orderBy = '') {
        try {
            $tableAlias = 'a';
            $sql = "SELECT $tableAlias.*, ";

            foreach ($joins as $alias => $on) {
                $aliasName = explode(' ', $alias)[1] ?? $alias;
                $sql .= "$aliasName.*, ";
            }
            $sql = rtrim($sql, ", ");

            $sql .= " FROM " . static::$table . " $tableAlias ";

            foreach ($joins as $joinTable => $onCondition) {
                $sql .= "JOIN $joinTable ON $onCondition ";
            }

            if (!empty($conditions)) {
                $sql .= " WHERE ";
                $whereClauses = [];
                foreach ($conditions as $index => $condition) {
                    [$column, $operator, $value] = $condition;
                    $param = ":value$index";
                    $whereClauses[] = "$column $operator $param";
                }
                $sql .= implode(' AND ', $whereClauses);
            }

            if ($orderBy) {
                $sql .= " ORDER BY $orderBy";
            }

            $stmt = self::$conn->prepare($sql);
            foreach ($conditions as $index => $condition) {
                $stmt->bindValue(":value$index", $condition[2]);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ) ?: [];

        } catch (PDOException $e) {
            die("Query failed: " . $e->getMessage());
        }
    }

    public static function fetchStats(array $statusValues) {
        // statusValues = ['complete' => 2, 'inprogress' => 1, 'rejected' => 3]
        try {
            $selects = ["COUNT(*) AS total"];
            foreach ($statusValues as $key => $val) {
                $selects[] = "SUM(status_id = '$val') AS $key";
            }
            $sql = "SELECT " . implode(", ", $selects) . " FROM " . static::$table;
            $stmt = self::$conn->prepare($sql);
            $stmt->execute();
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            return $stats ?: array_fill_keys(array_merge(['total'], array_keys($statusValues)), 0);
        } catch (PDOException $e) {
            error_log("Error fetching stats: " . $e->getMessage());
            return array_fill_keys(array_merge(['total'], array_keys($statusValues)), 0);
        }
    }
    
    public function fetchJobPostings(): array {
        try {
            $stmt = self::$conn->prepare("SELECT * FROM job_postings ORDER BY posted_at DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error retrieving jobs: " . $e->getMessage());
            return [];
        }
    }

}