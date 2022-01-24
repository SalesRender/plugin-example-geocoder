<?php
/**
 * Created for plugin-core-geocoder
 * Datetime: 03.03.2020 15:43
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace Leadvertex\Plugin\Instance\Geocoder;


use Leadvertex\Plugin\Components\Form\FieldDefinitions\FieldDefinition;
use Leadvertex\Plugin\Components\Form\FieldDefinitions\PasswordDefinition;
use Leadvertex\Plugin\Components\Form\FieldDefinitions\StringDefinition;
use Leadvertex\Plugin\Components\Form\FieldGroup;
use Leadvertex\Plugin\Components\Form\Form;
use Leadvertex\Plugin\Components\Form\FormData;
use Leadvertex\Plugin\Components\Translations\Translator;

class SettingsForm extends Form
{

    public function __construct()
    {
        $nonNull = function ($value, FieldDefinition $definition, FormData $data) {
            $errors = [];
            if (is_null($value)) {
                $errors[] = Translator::get('settings', 'Поле не может быть пустым');
            }
            return $errors;
        };
        parent::__construct(
            Translator::get('settings', 'Настройки'),
            null,
            [
                'main' => new FieldGroup(
                    Translator::get('settings', 'Основные настройки'),
                    null,
                    [
                        'email' => new StringDefinition(
                            Translator::get('settings', 'Email'),
                            null,
                            $nonNull
                        ),
                        'password' => new PasswordDefinition(
                            Translator::get('settings', 'Пароль'),
                            null,
                            $nonNull
                        ),
                    ]
                ),
            ],
            Translator::get('settings', 'Сохранить'),
        );
    }
}


