<?php namespace Kareem3d\Membership;

use Helper\Helper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Kareem3d\Eloquent\Model;
use Kareem3d\Membership\User;

class UserInfo extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'ka_user_info';

    /**
     * Validations rules
     *
     * @var array
     */
    protected $rules = array(
        'ip'       => 'required|ip',
    );

    /**
     * For factoryMuff package to be able to fill attributes.
     *
     * @var array
     */
    public static $factory = array(
    );

    /**
     * @return void
     */
    public function beforeValidate()
    {
        // Update user IP.
        $this->makeIP();

        // Clean xss
        $this->cleanXSS();
    }

    /**
     * @param User $user
     * @return bool
     */
    public function exists( User $user )
    {
        return $this->getDuplicateQuery($user)->count() > 0;
    }

    /**
     * @param User $user
     * @return Builder
     */
    protected function getDuplicateQuery( User $user )
    {
        // We first get all attributes except the ip
        $attributes = $this->getAttributes();

        unset($attributes['ip']);

        // Now we get all user infos that are attached to this user
        $query = $this->where('user_id', $user->id);

        // And have the same attributes
        foreach($attributes as $key => $value)
        {
            $query = $query->where($key, (string)$value);
        }

        return $query;
    }

    /**
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Model|Model|null|static
     */
    public function getDuplicateFor( User $user )
    {
        return $this->getDuplicateQuery($user)->first();
    }

    /**
     * @return void
     */
    public function makeIP()
    {
        $this->ip = Helper::instance()->getCurrentIP();
    }

    /**
     * @param $name
     * @return void
     */
    public function setNameAttribute($name)
    {
        $pieces = explode(' ', $name);

        $this->first_name = isset($pieces[0]) ? $pieces[0] : '';

        $this->last_name = isset($pieces[1]) ? $pieces[1] : '';
    }

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(App::make('Kareem3d\Membership\User'));
    }
}