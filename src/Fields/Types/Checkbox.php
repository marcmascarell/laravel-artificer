<?php

namespace Mascame\Artificer\Fields\Types;

use Form;
use Illuminate\Support\Collection;
use Mascame\Artificer\Fields\Field;

class Checkbox extends Field
{

    protected function input()
    {
        print '<div>';
        print Form::hidden($this->name, 0);

        print Form::checkbox($this->name, 1, $this->value, $this->attributes);
        print '</div>';
    }

    /**
     * @param $fields Collection
     * @param $next
     * @return mixed
     */
    public function savingHook($fields, $next)
    {
        $fields->each(function (Field $field, $key) {
            if ($field->getType() == $this->getType() && empty($field->getValue())) {

                $field->setValue(0);
            }
        });

        return $next($fields);
    }

}