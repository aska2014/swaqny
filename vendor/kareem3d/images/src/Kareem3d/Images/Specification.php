<?php namespace Kareem3d\Images;

use Kareem3d\Code\Code;
use Kareem3d\Eloquent\Model;

class Specification extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'ka_image_specifications';

    /**
     * Validations rules
     *
     * @var array
     */
    protected $rules = array(
        'image_group_id' => 'required|exists:image_groups,id'
    );

    /**
     * For factoryMuff package to be able to fill attributes.
     *
     * @var array
     */
    public static $factory = array(
        'directory' => 'string',
        'image_group_id' => 'factory|Kareem3d\Images\Group'
    );

    /**
     * @param array $attributes
     * @return Model|void
     */
    public static function create(array $attributes)
    {
        // Check if the code is set
        if(isset($attributes['code']))
        {
            // Extract it from attributes
            $code = $attributes['code'];

            unset($attributes['code']);

            // Create specification
            $spec = parent::create($attributes);

            // Create code
            $spec->setCode( $code );

            // Return specification
            return $spec;
        }

        return parent::create($attributes);
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @param $imageName
     * @return string
     */
    public function getPath( $imageName )
    {
        return rtrim($this->getDirectory(), '\\/') . '/' . $imageName;
    }

    /**
     * @param Code $code
     */
    public function setCode($code)
    {
        $this->code()->delete();

        $code = $code instanceof Code ? $code : $this->generateCode($code);
        $code->save();

        $this->code()->associate($code);

        $this->save();
    }

    /**
     * @param string $code
     * @return Code
     */
    protected function generateCode( $code )
    {
        if(strpos($code, 'return $image;') === false)
        {
            $code .= 'return $image;';
        }

        return Code::create(array('code' => $code));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function code()
    {
        return $this->belongsTo('Kareem3d\Code\Code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(App::make('Kareem3d\Images\Group')->getClass(), 'image_group_id');
    }
}