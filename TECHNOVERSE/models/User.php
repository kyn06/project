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
        return $results ? array_map(fn($user) => new self($user), $results) : null;
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
            return $stmt->fetch(); 
        } catch (PDOException $e) {
            die("Query failed: " . $e->getMessage());
        }
    }

    public static function login($email, $password) {
        $userData = self::findByEmail($email);

        if ($userData) {
            if (password_verify($password, $userData['password'])) {
                if ($userData['status'] == 'inactive') {
                    $_SESSION['error'] = "Your account is deactivated. Please contact the super-admin.";
                    return false;
                }
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $userData['role'];
                return true;
            }
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
        } else {
            return false;
        }
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

        $this->update($data);
    }

    public function delete() {
        $result = parent::deleteById($this->id);

        if ($result) {
            foreach ($this as $key => $value) {
                if (property_exists($this, $key)) {
                    unset($this->$key);
                }
            }
            return true;
        } else {
            return false;
        }
    }

    //change the prompt
    public function getUsers() {
        $users = self::all();

        if (empty($users)) {
            http_response_code(404);
            echo "<h1 style='text-align: center; 
                font-size: 70px; font-family: Verdana, sans-serif; 
                margin-top: 250px; 
                background: -webkit-linear-gradient(rgb(88, 10, 10),rgb(182, 98, 98)); 
                -webkit-background-clip: text;  
                -webkit-text-fill-color: transparent;'>
                    No Users Found!
                    <br>  ｡°(°.◜ᯅ◝°)°｡  
                  </h1>";
            exit();
        }

        return $users;
    }
 public function getApplications() {
    try {
        $sql = "
            SELECT 
                a.*, 
                u.full_name, 
                j.job_title AS job_title, 
                s.label AS status
            FROM applications a
            JOIN users u ON a.user_id = u.id
            JOIN job_postings j ON a.job_posting_id = j.id
            JOIN statuses s ON a.status_id = s.id
            ORDER BY a.application_date DESC
        ";
        $stmt = self::$conn->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
        return count($rows) > 0 ? $rows : null;
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
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

            if ($status === 'active') {
                $sql .= " WHERE status = 'active'";
            } elseif ($status === 'inactive') {
                $sql .= " WHERE status = 'inactive'";
            }

            $stmt = self::$conn->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $results ? array_map(fn($user) => new self($user), $results) : [];
        } catch (PDOException $e) {
            die("Query failed: " . $e->getMessage());
        }
    }

    //role-based access control
    //still needs revision
    private $user;

    public function authenticateUser () {
    // Check if the user is logged in
    if (!isset($_SESSION['email'])) {
        header("Location: ../authentication/login.php");
        exit();
    }

    // Retrieve user information based on the email stored in the session
    $user = self::findByEmail($_SESSION['email']);

    // If the user is not found, destroy the session and redirect to login
    if (!$user) {
        session_destroy();
        header("Location: ../authentication/login.php");
        exit();
    }

    // Set the current user
    $this->user = $user;

    // Get the user's role from the session
    $role = $_SESSION['role'];
    $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $pathSegments = explode('/', $currentPath);

        // Define access control based on user roles
        switch ($role) {
            case 'super-admin':
                // Super-Admin has full access
                return $user;

            case 'admin':
                // Admin has full access
                return $user;

            case 'hr':
                // HR can only access their own company's job postings and applications
                if (in_array('company_profile', $pathSegments) || in_array('job_postings', $pathSegments) || in_array('applications', $pathSegments)) {
                    return $user;
                } else {
                    http_response_code(403);
                    echo "<h1 style='font-size: 60px; text-align: center'>
                            Access Denied. You can only access your company's profile, job postings, and applications.
                        </h1>";
                    echo '<div style="font-size: 30px; text-align: center">
                            <a href="../index.php" class="btn btn-outline-secondary">Go Back</a>
                        </div>';
                    exit();
                }

            case 'job_seeker':
                // Job Seeker can only access their own applications
                if (in_array('my_applications', $pathSegments) || in_array('apply', $pathSegments)) {
                    return $user;
                } else {
                    http_response_code(403);
                    echo "<h1 style='font-size: 60px; text-align: center'>
                            Access Denied. You can only access your own applications.
                        </h1>";
                    echo '<div style="font-size: 30px; text-align: center">
                            <a href="../index.php" class="btn btn-outline-secondary">Go Back</a>
                        </div>';
                    exit();
                }

            default:
                // If the role is not recognized, deny access
                http_response_code(403);
                echo "<h1 style='font-size: 60px; text-align: center'>
                        Access Denied. Contact your super-admin to access this page.
                    </h1>";
                echo '<div style="font-size: 30px; text-align: center">
                        <a href="../index.php" class="btn btn-outline-secondary">Back to Home</a>
                    </div>';
                exit();
        }
    }


    public function getUserName() {
        return $this->user['first_name'];
    }

    public static function where($column, $operator, $value) {
        $result = parent::where($column, $operator, $value);

        return $result 
            ? array_map(fn($data) => new self($data), $result) 
            : null;
    }
public function getApplicationStatusByUserId($userId) {
    try {
        $sql = "SELECT 
                    a.id AS application_id,
                    a.created_at AS application_date,
                    a.status_id AS status,
                    u.full_name,
                    u.email,
                    jp.job_title AS job_title,
                    s.label AS status_label
                FROM applications a
                JOIN users u ON a.user_id = u.id
                JOIN job_postings jp ON a.job_posting_id = jp.id
                LEFT JOIN statuses s ON a.status_id = s.id
                WHERE a.user_id = :userId";

        $stmt = self::$conn->prepare($sql);
        $stmt->bindValue(':userId', $userId);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        die("Error fetching applications: " . $e->getMessage());
    }
}
public function fetchApplicationStatsByLabel(): array {
    try {
        $stmt = self::$conn->prepare("
            SELECT 
                COUNT(*) AS total, 
                SUM(status_id = '2') AS complete, 
                SUM(status_id = '1') AS inprogress, 
                SUM(status_id = '3') AS rejected 
            FROM applications
        ");
        $stmt->execute();
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        return [
            'total' => $stats['total'] ?? 0,
            'complete' => $stats['complete'] ?? 0,
            'inprogress' => $stats['inprogress'] ?? 0,
            'rejected' => $stats['rejected'] ?? 0,
        ];
    } catch (PDOException $e) {
        error_log("Error fetching application stats: " . $e->getMessage());
        return ['total' => 0, 'complete' => 0, 'inprogress' => 0, 'rejected' => 0];
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

public function fetchTotalApplications(): int {
    try {
        $stmt = self::$conn->prepare("SELECT COUNT(*) AS total_applications FROM applications");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_applications'] ?? 0;
    } catch (PDOException $e) {
        error_log("Error retrieving total applications: " . $e->getMessage());
        return 0;
    }
}

public static function getNavbarFile(): string {
    $role = $_SESSION['role'] ?? null;

    return match ($role) {
        'Job-seeker' => 'navbarforjobseeker.php',
        'Super Admin' => 'superadmin/navbarsuperadmin.php',
        'HR' => 'navbar.php',
        default => 'navbar.php',
    };
}

}
