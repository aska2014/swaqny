<?php namespace Kareem3d\Eloquent;

use Helper\Helper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

abstract class Model extends \Illuminate\Database\Eloquent\Model {

    /**
     * @var array
     */
    protected $guarded = array();

    /**
     * @var array
     */
    protected static $registeredExtensions = array();

    /**
     * Extensions used for this model
     *
     * @var array
     */
    protected $extensions = array();

    /**
     * Validation rules
     *
     * @var array
     */
    protected $rules = array();

    /**
     * Validation custom messages
     *
     * @var array
     */
    protected $customMessages = array();

    /**
     * @var \Illuminate\Validation\Validator
     */
    protected $validator = null;

    /**
     * array('email', 'username') => Won't duplicate if ONE of them is the same
     * array(array('email', 'username')) => Won't duplicate if BOTH of them are the same
     *
     * @var array
     */
    protected static $dontDuplicate = array();

    /**
     * If this model has specifications table associated with..
     *
     * @var null
     */
    protected static $specsTable = null;

    /**
     * Specifications for the specs table defined..
     *
     * @var array
     */
    protected static $specs = array();

    /**
     * All languages for this app
     *
     * @var array
     */
    public static $languages = array('en');

    /**
     * Default language used for fetching data (Current state language)
     *
     * @var string
     */
    public static $defaultLanguage = 'en';

    /**
     * @var array
     */
    protected $languagesAttributes = array();

    /**
     * Find model or get new instance.
     *
     * @param $id
     * @param array $columns
     * @return \Kareem3d\Eloquent\Model
     */
    public static function findOrNew($id, $columns = array('*'))
    {
        if($model = static::find($id, $columns))

            return $model;

        return new static;
    }

    /**
     * Register new extension to be used by other models
     *
     * @param $name
     * @param $class
     */
    public static function registerExtension( $name, $class )
    {
        static::$registeredExtensions[ $name ] = $class;
    }

    /**
     * Use to clean all attributes from xss attack
     *
     * @return void
     */
    public function cleanXSS()
    {
        Helper::instance()->cleanXSS($this->getAttributes());
    }

    /**
     * The last validator messages.
     *
     * @return\Illuminate\Support\MessageBag
     */
    public function getValidatorMessages()
    {
        return $this->getValidator()->messages();
    }

    /**
     * If you want to escape a rule.
     *
     * @param $rule
     */
    public function escapeRule( $rule )
    {
        unset($this->rules[ $rule ]);
    }

    /**
     * This method will check if the given attributes are valid or not..
     * Remember that there is a validator object that holds the last validation.
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->getValidator()->passes();
    }

    /**
     * @return void
     */
    public function resetValidator()
    {
        $this->validator = null;
    }

    /**
     * This method holds the state of the last validator
     * If null then it will validate with the current model attributes.
     *
     * @return \Illuminate\Validation\Validator
     */
    public function getValidator()
    {
        if($this->validator) return $this->validator;

        $this->validator = Validator::make($this->getAttributes(), $this->rules, $this->customMessages);

        return $this->validator;
    }

    /**
     * Each time this method is called the model is validated from all over again.
     *
     * @return bool
     */
    public function validate()
    {
        if($this->beforeValidate() === false) return false;

        // Will reset validator to validate the model again
        $this->resetValidator();

        if($this->getValidator()->passes()) {

            return true;
        }

        return false;
    }

    /**
     * Create new instance, validate attributes
     *
     * @param array $attributes
     * @return bool
     */
    public static function validateAttributes(array $attributes)
    {
        $instance = static::newInstance($attributes);

        return $instance->validate();
    }

    /**
     * This will validate the model and save it.
     *
     * @param array $attributes
     * @return Model
     */
    public static function create( array $attributes )
    {
        $model = new static($attributes);

        $model->save();

        return $model;
    }

    /**
     * This will validate the model and save it.
     *
     * @param array $options
     * @return bool
     */
    public function save(array $options = array())
    {
        // Check before save method
        if($this->beforeSave() === false) return false;

        // First check if there's a duplicate for this model to update it.
        if($duplicateModel = $this->getDuplicate())
        {
            $this->exists = true;
            $this->id     = $duplicateModel->id;
        }

        // If this has specs table then first get specs attributes that are already set
        // and set the current language for this specifications then push them to the
        // specs table
        if($this->hasSpecsTable())
        {
            $specsAttrs = $this->extractSpecsAttributes();
            $specsAttrs['language'] = $this->extractLanguageAttribute();

            // Try to save this model
            if(parent::save($options))

                // Try to save the specs table row
                return $this->setSpecsTableRow( $specsAttrs );

            return false;
        }

        return parent::save($options);
    }

    /**
     * Update the model in the database.
     *
     * @param  array  $attributes
     * @return mixed
     */
    public function update(array $attributes = array())
    {
        return $this->fill($attributes)->save();
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        // If Eloquent model can find value by itself return it
        if($value = parent::getAttribute($key)) return $value;

        // If this has specs table
        if($this->hasSpecsTable())
        {
            // If the key is in the specifications array then get this attribute by current language.
            if(in_array($key, static::$specs))
            {
                return $this->getAttributeByLanguage($key, $this->getCurrentLanguage());
            }
        }
    }

    /**
     * @param array|string $key
     * @param $language
     * @return array|string
     */
    public function getAttributeByLanguage( $key, $language )
    {
        // First make sure we are using this language
        if(! $this->isUsingLanguage( $language ))
        {
            $this->useLanguage( $language );
        }

        return isset($this->languagesAttributes[$language][$key]) ? $this->languagesAttributes[$language][$key] : null;
    }

    /**
     * @param $language
     * @return bool
     */
    protected function isUsingLanguage( $language )
    {
        return isset($this->languagesAttributes[$language]);
    }

    /**
     * @param $language
     * @return void
     */
    public function useLanguage( $language )
    {
        $array = (array) $this->getSpecsQuery($language)->first(static::$specs);

        if(! empty($array)) $this->languagesAttributes[ $language ] = $array;
    }

    /**
     * @return void
     */
    public function useDefaultLanguage()
    {
        $this->useLanguage( static::$defaultLanguage );
    }

    /**
     * @param $array
     */
    public static function setLanguages( $array )
    {
        static::$languages = $array;
    }

    /**
     * @return string
     */
    public function extractLanguageAttribute()
    {
        $language = $this->language;

        unset($this->attributes['language']);

        return $language ?: static::$defaultLanguage;
    }

    /**
     * @return Builder
     */
    public static function allSpecsQuery()
    {
        return DB::table(static::$specsTable);
    }

    /**
     * @return null
     */
    public static function getSpecsTable()
    {
        return static::$specsTable;
    }

    /**
     * @param string $language
     * @return mixed
     */
    public function getSpecsQuery( $language = '' )
    {
        $query = DB::table(static::$specsTable)->where($this->getForeignKey(), $this->id);

        if($language) return $query->where('language', $language);

        return $query;
    }

    /**
     * @return bool
     */
    protected function hasSpecsTable()
    {
        return static::$specsTable != null;
    }

    /**
     * Return current language.
     *
     * @return string
     */
    protected function getCurrentLanguage()
    {
        return $this->language ?: static::$defaultLanguage;
    }

    /**
     * Get query combining current model table with the specification table
     *
     * @return mixed
     */
    public static function specsQuery()
    {
        $model = new static;

        $query = $model->newQuery();

        $specsTable = static::getSpecsTable();

        return $query->join($specsTable, "{$model->getTable()}.{$model->getKeyName()}", '=', "$specsTable.{$model->getForeignKey()}");
    }

    /**
     * @param array $specsAttrs
     * @return mixed
     */
    protected function setSpecsTableRow( array $specsAttrs )
    {
        // Get specification query by given language
        $query = $this->getSpecsQuery($specsAttrs['language']);

        if($query->count() == 0)
        {
            $specsAttrs[$this->getForeignKey()] = $this->id;

            return DB::table(static::$specsTable)->insert($specsAttrs);
        }
        else
        {
            return $query->update($specsAttrs);
        }
    }

    /**
     * Get specifications attributes array.
     *
     * @return array
     */
    protected function extractSpecsAttributes()
    {
        $attributes = array();

        // Extract specification attributes from the current attributes
        foreach(static::$specs as $specification)
        {
            if(isset($this->attributes[ $specification ]))
            {
                $attributes[ $specification ] = $this->attributes[ $specification ];

                unset($this->attributes[ $specification ]);
            }
        }

        // Return the attributes;
        return $attributes;
    }

    /**
     * Return duplicate model if exists.
     *
     * @return \Kareem3d\Eloquent\Model|null
     */
    public function getDuplicate()
    {
        if(empty(static::$dontDuplicate)) return null;

        // If this model has specification table then create a query with this specsTable
        if($this->hasSpecsTable())
        {
            $query = DB::table(static::$specsTable);
        }
        else
        // Else create new query
        {
            $query = $this->newQuery();
        }

        $dontDuplicate = static::$dontDuplicate;
        $that = $this;

        return $query->where('id', '!=', (int) $this->id)->where(function($query) use($dontDuplicate, $that)
        {
            foreach($dontDuplicate as $attribute)
            {
                if(is_array($attribute))
                {
                    $query->orWhere(function($query) use ($attribute, $that)
                    {
                        foreach($attribute as $andAttr)
                        {
                            $query->where($andAttr, $that->getAttribute($andAttr));
                        }
                    });
                }
                else
                {
                    $query->orWhere($attribute, $that->getAttribute($attribute));
                }
            }
        })->first();
    }

    /**
     * Before validate event..
     *
     * @return mixed
     */
    public function beforeValidate()
    {
        $this->useMethodIfExists('beforeValidate');
    }

    /**
     * Before save event..
     *
     * @return mixed
     */
    public function beforeSave()
    {
        $this->useMethodIfExists('beforeSave');
    }

    /**
     * @param string $format
     * @return string
     */
    public function getCreatedAt( $format = '' )
    {
        $created_at = $this->getAttribute('created_at');

        if(! $format) return $created_at;

        else return date($format, strtotime($created_at));
    }

    /**
     * @param $method
     * @return null
     */
    protected function getExtensionObjectFromMethod($method)
    {
        foreach($this->extensions as $extension)
        {
            $className = $this->getExtensionClass($extension);

            if(! class_exists($className)) continue;

            $object = new $className( $this );

            if(method_exists($object, $method)) return $object;
        }

        return null;
    }

    /**
     * @param $method
     * @return mixed|null
     */
    protected function useMethodIfExists($method)
    {
        if($this->usesMethod($method))
        {
            return $this->useMethod($method, array());
        }

        return null;
    }

    /**
     * @param  string $method
     * @return bool
     */
    protected function usesMethod($method)
    {
        return $this->getExtensionObjectFromMethod($method) != null;
    }

    /**
     * @param $method
     * @param $parameters
     * @return mixed
     */
    protected function useMethod($method, $parameters)
    {
        $object = $this->getExtensionObjectFromMethod($method);

        return call_user_func_array(array($object, $method), $parameters);
    }

    /**
     * @param $class
     * @return bool
     */
    public function doesUse( $class )
    {
        return array_search($class, $this->extensions) !== false;
    }

    /**
     * @param array $models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = array())
    {
        foreach($this->extensions as $extension)
        {
            if(method_exists($this->getExtensionClass($extension), 'newCollection'))

                return call_user_func($this->getExtensionClass($extension) . '::newCollection', $models);
        }

        return new Collection($models);
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getExtensionClass( $name )
    {
        return static::$registeredExtensions[ $name ];
    }

    /**
     * @param Model $model
     * @return bool
     */
    public function same( Model $model )
    {
        return ($model->id == $this->id);
    }

    /**
     * Using extensions
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if(in_array($method, static::$languages) && isset($parameters[0]))
        {
            return $this->getAttributeByLanguage($parameters[0], $method);
        }

        if($this->usesMethod($method))
        {
            return $this->useMethod($method, $parameters);
        }

        return parent::__call($method, $parameters);
    }

    /**
     * @return string
     */
    public static function getClass()
    {
        return get_called_class();
    }

    /**
     * @param $value
     */
    public static function encrypt( $value )
    {
        return Crypt::encrypt( $value );
    }

    /**
     * @param $value
     * @return string
     */
    public static function decrypt( $value )
    {
        return Crypt::decrypt( $value );
    }

    /**
     * Get all rows newer than the given date
     *
     * @param mixed $date
     * @return Collection
     */
    public static function getNewerThan($date)
    {
        $instance = new static;

        return $instance->where($instance->getCreatedAtColumn(), '>', $date)->get();
    }
}