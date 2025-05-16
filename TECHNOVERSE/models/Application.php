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

    public static function getApplications() {
        return self::getJoinedData([
            'users u' => 'a.user_id = u.id',
            'job_postings j' => 'a.job_posting_id = j.id',
            'statuses s' => 'a.status_id = s.id',
        ], 'a.application_date DESC');
    }

    public static function getApplicationStatusByUserId($userId) {
        return self::getByColumn('user_id', $userId, [
            'users u' => 'a.user_id = u.id',
            'job_postings jp' => 'a.job_posting_id = jp.id',
            'statuses s' => 'a.status_id = s.id',
        ]);
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
}