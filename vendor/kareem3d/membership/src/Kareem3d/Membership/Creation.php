<?php namespace Kareem3d\Membership;

use Illuminate\Support\Facades\App;
use Kareem3d\Eloquent\Model;

class Creation extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ka_user_creations';

    /**
     * Validation rules
     *
     * @var array
     */
    protected $rules = array(
    );

    /**
     * For factoryMuff package to be able to fill the post attributes.
     *
     * @var array
     */
    public static $factory = array(
    );

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(App::make('Kareem3d\Membership\User')->getClass());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creatable()
    {
        return $this->morphTo();
    }
}