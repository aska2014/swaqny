<?php namespace Kareem3d\URL;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Request;
use Kareem3d\Eloquent\Model;

class URL extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ka_urls';

    /**
     * @var array
     */
    protected static $dontDuplicate = array('url');

    /**
     * @param $url
     * @return \Kareem3d\URL\URL
     */
    public static function getByUrl( $url )
    {
        return static::where('url', $url)->orWhere('url', trim($url, '/'))->first();
    }

    /**
     * @param $url
     * @return bool
     */
    public function samePath( $url )
    {
        $url = parse_url($url);

        return $this->path === $url['path'];
    }

    /**
     * @param $url
     * @return bool
     */
    public function sameHost( $url )
    {
        $url = parse_url($url);

        return $this->host === $url['host'];
    }

    /**
     * @param $url
     * @return bool
     */
    public function sameUrl( $url )
    {
        return $this->url === $url;
    }

    /**
     * @param $path
     * @return void
     */
    public function setPathAttribute( $path )
    {
        $this->url = rtrim($this->host, '/') . '/' . trim($path, '/');
    }

    /**
     * @param $host
     */
    public function setHostAttribute( $host )
    {
        $this->url = rtrim($host, '/') . '/' . trim($this->path);
    }

    /**
     * @return string
     */
    public function getPathAttribute()
    {
        $url = parse_url($this->url);

        return $url['path'];
    }

    /**
     * @return mixed
     */
    public function getHostAttribute()
    {
        $url = parse_url($this->url);

        return $url['host'];
    }

    /**
     * @return mixed|string
     */
    public function __toString()
    {
        return $this->url;
    }
}