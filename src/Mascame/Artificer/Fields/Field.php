<?php namespace Mascame\Artificer\Fields;

use Event;
use App;
use Mascame\Artificer\Localization;
use Mascame\Artificer\Options\ModelOption;

abstract class Field implements FieldInterface {

	public $type;
	public $title;
	public $name;
	public $modelName;
	public $configKey;
	public $configFieldKey;
	public $value;
	public $output;
	public static $widgets = array();
    /**
     * @var FieldOptions
     */
	public $options;
	public $lists = array();
    /**
     * @var FieldRelation
     */
	public $relation;
    /**
     * @var Localization
     */
	public $localization;
	public $locale;
	public $wiki;
    /**
     * @var FieldAttributes
     */
    public $attributes;


	/**
	 * @param $name
	 * @param null $value
	 * @param $modelName
	 * @param $relation
	 */
	public function __construct($name, $value = null, $modelName, $relation)
	{
		$this->name = $name;
		$this->value = $value;
		$this->modelName = $modelName;

        $this->options = new FieldOptions($this->name);
		$this->relation = new FieldRelation($relation, $this->options->getExistent('relationship'));
		$this->attributes = new FieldAttributes($this->options->getExistent('attributes'), $this->options);

		$this->attributes->add(array('class' => 'form-control'));

		$this->title = $this->getTitle($this->name);
		$this->type = $this->getType(get_called_class());
		$this->wiki = $this->getWiki();

        $this->localization = App::make('artificer-localization');
        $this->locale = $this->getLocale();

		$this->boot();
	}

	public function getWiki()
	{
		return $this->options->getExistent('wiki');
	}

	/**
	 * @param string $type_class
	 * @return string
	 */
	public function getType($type_class)
	{
        $pieces = explode('\\', $type_class);

		return strtolower(end($pieces));
	}


	/**
	 * @param $widget
	 * @return bool
	 */
	public function addWidget($widget)
	{
		if (!in_array($widget->name, self::$widgets)) {
			self::$widgets[$widget->name] = $widget;

			return true;
		}

		return false;
	}


	/*
	 * Used to load custom assets, widgets, ...
	 */
	public function boot()
	{
		return false;
	}


    /**
     * @return null
     */
	public function show()
	{
		return $this->value;
	}

    /**
     * @param null $value
     * @return null
     */
    public function display($value = null)
    {
        $this->getValue($value);

        return $this->show($value);
    }


	/**
	 * @param null $value
	 * @return null
	 */
	public function getValue($value = null)
	{
        if (!$value) return $this->value = $this->options->getExistent('default', null);

		return $this->value = $value;
	}


	/**
	 * @return bool
	 */
	public function input()
	{
		return false;
	}


	/**
	 * @param $input
	 * @return mixed
	 */
	public function userInput($input)
	{
		$input = str_replace('(:value)', $this->value, $input);
		$input = str_replace('(:name)', $this->name, $input);
		$input = str_replace('(:label)', $this->title, $input);

		return $input;
	}


	/**
	 * @return bool|mixed|null|string
	 */
	public function output()
	{
		Event::fire('artificer.field.' . $this->type . '.output', $this->value);

		if ($this->isHidden()) {
			return null;
		} else if ($this->isGuarded()) {
			return $this->guarded();
		}

        $this->value = $this->getValue();

		if ($this->options->has('input')) {
			return $this->userInput($this->options->get('input'));
		}

		return $this->input();
	}


	/**
	 * @return string
	 */
	public function hidden()
	{
		return '<div class="label label-warning">Hidden data</div>';
	}


	/**
	 * @return bool
	 */
	public function hasList($list = 'list')
	{
		if ($this->options->has($list)) {
			return true;
		}

		return false;
	}

	/**
	 * @param $array
	 * @return bool
	 */
	protected function isAll($array) {
		return (is_array($array) && isset($array[0]) && $array[0] == '*');
	}

	/**
	 * @param string $list
	 * @return bool
	 */
	public function isListed($list = 'list')
	{
		$list = $this->options->getExistent($list);

		if ($this->isAll($list)) {
			return true;
		}

		return $this->isInArray($this->name, $list);
	}


	/**
	 * @return bool
	 */
	public function isHiddenList()
	{
		return $this->isListed('list-hide');
	}


	/**
	 * @param $value
	 * @param $array
	 * @return bool
	 */
	public function isInArray($value, $array)
	{
		return (is_array($array) && in_array($value, $array)) ? true : false;
	}


	/**
	 * @return string
	 */
	public function guarded()
	{
		return '(guarded) ' . $this->show();
	}


	/**
	 * @param $name
	 * @return mixed
	 */
	public function getTitle($name)
	{
		if ($this->options->has('title')) {
			return $this->options->get('title');
		}

		return $name;
	}


	/**
	 * @return bool
	 */
	public function isGuarded()
	{
		return $this->isInArray($this->name, ModelOption::get('guarded'));
	}


	/**
	 * @return bool
	 */
	public function isHidden()
	{
		return $this->isInArray($this->name, ModelOption::get('hidden'));
	}

	/**
	 * @return bool
	 */
	public function isRelation()
	{
		return $this->relation->isRelation();
	}

    public function isLocalized() {
        if ($this->getLocale()) {
            return true;
        }

        return false;
    }

    public function getLocale() {
        if ($this->locale) return $this->locale;

        if ($lang = $this->localization->parseColumnLang($this->name)) {
            return $this->locale = $lang;
        }

        return false;
    }
}