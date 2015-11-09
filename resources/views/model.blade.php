@php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class {{ $class }} extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = '{{ $table }}';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    @foreach($fields as $field)
        '{{ $field['name'] }}',
    @endforeach
    ];
}
