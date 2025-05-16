<?php  

require_once 'Model.php';

class Company extends Model {}
    protected static $table = 'companies';

    private $id;
    private $name;
    private $about;
    private $address;
    private $contact_no;
    private $company_size;
    private $field;
    private $created_at;
    private $updated_at;

    public function __construct(array $data = []) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public static function all() {
        $results = parent::all();
        return $results ? array_map(fn($company) => new self($company), $results) : null;
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
            'name' => $this->name,
            'about' => $this->about,
            'address' => $this->address,
            'contact_no' => $this->contact_no,
            'company_size' => $this->company_size,
            'field' => $this->field,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if ($this->id) {
            $this->update($data);
        } else {
            $createdCompany = self::create($data);
            if ($createdCompany) {
                $this->id = $createdCompany->id;
            }
        }
    }

}

