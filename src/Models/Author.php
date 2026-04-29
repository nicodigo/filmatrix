<?php

namespace App\Models;

use App\Core\AbstractModel;
use App\Core\Exceptions\InvalidValueFormatException;

class Author extends AbstractModel
{
    public string $table = 'author';

    public array $fields = [
        'name' => null,
        'bio' => null,
    ];

    public function setName(string $name)
    {
        if (strlen($name) > 60) {
            throw new InvalidValueFormatException('El nombre del autor no debe ser mayor a 60 caracteres');
        }

        $this->fields['name'] = $name;
    }

    public function setBiography(string $bio)
    {
        if (strlen($bio) > 250) {
            throw new InvalidValueFormatException('La biografía del autor no debe ser mayor a 250 caracteres');
        }

        $this->fields['bio'] = $bio;
    }

    public function set(array $values)
    {
        foreach (array_keys($this->fields) as $field) {
            if (!isset($values[$field])) {
                continue;
            }

            $method = 'set' . ucfirst($field);

            $this->$method($values[$field]);
        }
    }
}
