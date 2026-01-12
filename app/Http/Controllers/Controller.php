<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use App\{
    ResponseApi,
    Generator
};

class Controller extends BaseController
{
    use ResponseApi, Generator;
}