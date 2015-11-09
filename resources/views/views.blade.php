@php

namespace {{ $namespace }};

use App\Http\Controllers\Controller;

class {{ $class }} extends Controller
{
    @if(!$options['empty'])
    public function index()
        {
            return view('backend.pages.index');
        }
    @endif
}