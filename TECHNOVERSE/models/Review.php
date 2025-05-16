<?php

require_once 'Model.php';

class Review extends Model {
    protected static $table = 'review';

    public $id;
    public $user_id;
    public $company_id;
    public $rating;
    public $comment;
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
        return $results ? array_map(fn($review) => new self($revew), $results) : null;
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
            'rating' => $this->rating,
            'comment' => $this->comment,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if ($this->id) {
            $this->update($data);
        } else {
            $createdReview = self::create($data);
            if ($createdReview) {
                $this->id = $createdReview->id;
            }
        }
    }

}