<?php

require_once 'Model.php';

class User extends Model {
    protected static $table = 'users';

    public $id;
    public $full_name;
    public $email;
    public $password;
    public $phone_number;
    public $role;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct(array $data = []) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public static function all() {
        $results = parent::all();
        return $results ? array_map(fn($user) => new self($user), $results) : [];
    }

    public static function find($id) {
        $result = parent::find($id);
        return $result ? new self($result) : null;
    }

    public static function findByEmail($email) {
        try {
            $sql = "SELECT * FROM " . static::$table . " WHERE email = :email";
            $stmt = self::$conn->prepare($sql);
            $stmt->bindValue(':email', $email);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            return $data ? new self($data) : null;
        } catch (PDOException $e) {
            die("Query failed: " . $e->getMessage());
        }
    }

    public static function login($email, $password) {
        $user = self::findByEmail($email);

        if ($user && password_verify($password, $user->password)) {
            if ($user->status === 'inactive') {
                $_SESSION['error'] = "Your account is deactivated. Please contact the super-admin.";
                return false;
            }

            $_SESSION['user_id'] = $user->id;
            $_SESSION['email'] = $user->email;
            $_SESSION['role'] = $user->role;
            $_SESSION['full_name'] = $user->full_name;
            $_SESSION['company_id'] = $user->company_id;
            return true;
        }

        $_SESSION['error'] = "Invalid email or password.";
        return false;
    }

    public static function create(array $data) {
        $result = parent::create($data);
        return $result ? new self($result) : null;
    }

    public function update(array $data) {
        $result = parent::updateById($this->id, $data);

        if ($result) {
            foreach ($data as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
            return true;
        }

        return false;
    }

    public function save() {
        $data = [
            'full_name' => $this->full_name,
            'email' => $this->email,
            'password' => password_hash($this->password, PASSWORD_DEFAULT),
            'phone_number' => $this->phone_number,
            'role' => $this->role,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if ($this->id) {
            $this->update($data);
        } else {
            $createdUser = self::create($data);
            if ($createdUser) {
                $this->id = $createdUser->id;
            }
        }
    }

    public function delete() {
        return parent::deleteById($this->id);
    }

    public function getUsers() {
        return self::all();
    }

    public static function countAllUsers() {
        return self::countAll();
    }

    public static function countNewUsers($startDate, $endDate) {
        return self::countNew($startDate, $endDate);
    }

    public static function countUsersByStatus($status) {
        return self::countByStatus($status);
    }

    public static function getByStatus($status = null) {
        try {
            $sql = "SELECT * FROM " . static::$table;

            if ($status === 'active' || $status === 'inactive') {
                $sql .= " WHERE status = :status";
            }

            $stmt = self::$conn->prepare($sql);

            if ($status === 'active' || $status === 'inactive') {
                $stmt->bindValue(':status', $status);
            }

            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $results ? array_map(fn($user) => new self($user), $results) : [];
        } catch (PDOException $e) {
            die("Query failed: " . $e->getMessage());
        }
    }

    public function authenticateUser() {
        if (!isset($_SESSION['email'])) {
            header("Location: ../authentication/login.php");
            exit();
        }

        $user = self::findByEmail($_SESSION['email']);
        if (!$user) {
            session_destroy();
            header("Location: ../authentication/login.php");
            exit();
        }

        $role = $_SESSION['role'];
        $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $pathSegments = explode('/', $currentPath);

        switch ($role) {
            case 'super-admin':
            case 'admin':
                return $user;

            case 'hr':
                if (in_array('company_profile', $pathSegments) || in_array('job_postings', $pathSegments) || in_array('applications', $pathSegments)) {
                    return $user;
                }
                break;

            case 'job_seeker':
                if (in_array('my_applications', $pathSegments) || in_array('apply', $pathSegments)) {
                    return $user;
                }
                break;
        }

        http_response_code(403);
        echo "<h1 style='font-size: 60px; text-align: center'>
                Access Denied. Please contact the super-admin for access.
            </h1>";
        echo '<div style="font-size: 30px; text-align: center">
                <a href="../index.php" class="btn btn-outline-secondary">Back to Home</a>
            </div>';
        exit();
    }

    public function getUserName() {
        return $this->full_name;
    }

    public static function where($column, $operator, $value) {
        $result = parent::where($column, $operator, $value);
        return $result ? array_map(fn($data) => new self($data), $result) : [];
    }

    public static function getNavbarFile(): string {
        $role = $_SESSION['role'] ?? null;

        return match ($role) {
            'Job-seeker' => '../views/jobseeker/navbar-js.php',
            'Super-admin', 'Admin' => '../views/superadmin/navbar-superadmin.php',
            'HR' => '../views/hr/navbar-hr.php',
            default => '../views/layouts/navbar.php',
        };
    }
}
