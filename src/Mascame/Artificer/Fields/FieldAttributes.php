<?php namespace Mascame\Artificer\Fields;

class FieldAttributes {

    protected $options;
    protected $fieldOption;
    
	/**
	 * @param FieldOptions $fieldOption
	 */
	public function __construct($options, $fieldOption)
	{
		$this->options;
        $this->fieldOption = $fieldOption;
	}

	/**
	 * @return array
	 */
	public function all()
	{
		return $this->fieldOption->getExistent('attributes', array());
	}

	/**
	 * @param $key
	 * @return array
	 */
	public function get($key)
	{
		return (isset($this->options[$key])) ? $this->options[$key] : array();
	}

    public function add($attributes = array())
    {
        $this->fieldOption->add('attributes', array_merge($this->all(), $attributes));

        return $this->fieldOption->all();
    }
    
}