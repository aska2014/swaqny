<?php namespace Kareem3d\Images;

use Illuminate\Database\Eloquent\Collection;
use Kareem3d\Eloquent\Model;

class Group extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'ka_image_groups';

    /**
     * Validations rules
     *
     * @var array
     */
    protected $rules = array(
        'name' => 'required'
    );

    /**
     * For factoryMuff package to be able to fill attributes.
     *
     * @var array
     */
    public static $factory = array(
        'name' => 'string',
    );

    /**
     * @param $name
     * @return Model
     */
    public static function createByName( $name )
    {
        return static::create(array('name' => $name));
    }

    /**
     * @param $name
     * @return Group
     */
    public static function getByName( $name )
    {
        return static::where('name', $name)->first();
    }

    /**
     * @param $array
     */
    public function createSpecs( $array )
    {
        $this->specs()->create($array);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Collection
     */
    public function getSpecs()
    {
        return $this->specs;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function specs()
    {
        return $this->hasMany(App::make('Kareem3d\Images\Specification')->getClass(), 'image_group_id');
    }
}