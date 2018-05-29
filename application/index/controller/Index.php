<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
        dump(config('database.prefix'));
    }
}
