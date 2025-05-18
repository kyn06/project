<?php

require_once 'Model.php';

class Application extends Model {
    protected static $table ='applications';

    public $id;
    public $user_id;
    public $job_posting_id;
    public $application_date;
    public $status_id;
    public $letter;
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
        return $results ? array_map(fn($application) => new self($application), $results) : null;
    }

    public static function find($id) {
        $result = parent::find($id);
        return $result ? new self($result) : null;
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

    public function save() {
        $data = [
            'user_id' => $this->user_id,
            'job_posting_id' => $this->job_posting_id,
            'application_date' => $this->application_date,
            'status_id' => $this->status_id,
            'letter' => $this->letter,
        ];

        if (isset($this->id)) {
            return parent::update($this->id, $data);
        } else {
            return parent::create($data);
        }
    }

    public static function applyJob($userId, $jobId, $letter) {
        $application = new self([
            'user_id' => $userId,
            'job_posting_id' => $jobId,
            'application_date' => date('Y-m-d'),
            'status_id' => 1,
            'letter' => $letter,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        return $application->save();
    }

    public static function getApplications() {
        return self::getJoinedData([
            'users u' => 'a.user_id = u.id',
            'job_postings j' => 'a.job_posting_id = j.id',
            'statuses s' => 'a.status_id = s.id',
            'companies c' => 'j.company_id = c.id'
        ], 'a.application_date DESC');
    }

    public static function getJobPostings() {
        try {
            $stmt = self::$conn->query("
                SELECT id, job_title, job_type, job_description, posted_at
                FROM job_postings
                ORDER BY posted_at DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Failed to fetch job postings: " . $e->getMessage());
        }
    }

    public static function getApplicationStatusByEmail($email) {
        return self::getByConditions(
            // WHERE conditions
            [
                ['u.email', '=', $email],
            ],
            // JOINs
            [
                'users u' => 'a.user_id = u.id',
                'job_postings jp' => 'a.job_posting_id = jp.id',
                'statuses s' => 'a.status_id = s.id',
            ],
            // ORDER BY
            'a.application_date DESC',
            // SELECT fields with aliases
            [
                'a.id AS application_id',
                'a.application_date',
                'u.full_name',
                'u.email',
                'jp.job_title',
                's.label AS status_label'
            ]
        );
    }
    

    public static function fetchApplicationStatsByLabel(): array {
        return self::fetchStats([
            'complete' => 2,
            'inprogress' => 1,
            'rejected' => 3,
        ]);
    }

    public static function fetchTotalApplications(): int {
        return self::countAll();
    }

    public static function getAllWithUserAndJobDetails() {
        try {
            $stmt = self::$conn->query("
                SELECT 
                    a.id AS application_id,
                    a.application_date,
                    a.letter,
                    u.full_name,
                    u.email,
                    jp.job_title,
                    jp.job_type,
                    jp.job_description,
                    c.name AS company_name
                FROM applications a
                LEFT JOIN users u ON a.user_id = u.id
                LEFT JOIN job_postings jp ON a.job_posting_id = jp.id
                LEFT JOIN companies c ON jp.company_id = c.id
                ORDER BY a.application_date DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Failed to fetch applications: " . $e->getMessage());
        }
    }
    

    public static function getAllWithUserBasic()
    {
        try {
            $stmt = self::$conn->query("
                SELECT 
                    a.id,
                    a.application_date,
                    a.status_id,
                    u.full_name,
                    u.email
                FROM applications a
                LEFT JOIN users u ON a.user_id = u.id
                ORDER BY a.application_date DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Failed to fetch applications: " . $e->getMessage());
        }
    }

    public function getApplicationsCountPerJobPost()
    {
        $query = "SELECT job_posting_id, COUNT(*) AS total_applications FROM applications GROUP BY job_posting_id";
        $stmt = self::$conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getApplicationsCountByStatus()
    {
        $query = "SELECT status_id, COUNT(*) AS total FROM applications GROUP BY status_id";
        $stmt = self::$conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getApplicationsByUserId($userId)
    {
        try {
            $stmt = self::$conn->prepare("
                SELECT 
                    a.id,
                    a.application_date,
                    a.status_id,
                    u.full_name,
                    u.email,
                    jp.job_title  -- Added job_title from job_postings
                FROM applications a
                LEFT JOIN users u ON a.user_id = u.id
                LEFT JOIN job_postings jp ON a.job_posting_id = jp.id
                WHERE a.user_id = :user_id
                ORDER BY a.application_date DESC
            ");
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Failed to fetch user applications: " . $e->getMessage());
        }
    }

}