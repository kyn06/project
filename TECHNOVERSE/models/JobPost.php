<?php

require_once 'Model.php';

class JobPost extends Model {
    protected static $table = 'job_postings';

    public $id;
    public $user_id;
    public $company_id;
    public $job_title;
    public $job_description;
    public $job_type;
    public $status_id;
    public $posted_at;
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
        return $results ? array_map(fn($jobpost) => new self($jobpost), $results) : null;
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
            'company_id' => $this->company_id,
            'job_title' => $this->job_title,
            'job_description' => $this->job_description,
            'job_type' => $this->job_type,
            'status_id' => $this->status_id,
            'posted_at' => $this->posted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if ($this->id) {
            $this->update($data);
        } else {
            $createdJobPost = self::create($data);
            if ($createdJobPost) {
                $this->id = $createdJobPost->id;
            }
        }
    }

    public function getJobPosts() {
        return self::fetchJobPostings();
    }

}