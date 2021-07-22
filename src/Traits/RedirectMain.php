<?php

namespace App\Traits;

Trait RedirectMain
{
    public function redirectIndex()
    {
        return $this->redirectToRoute('index');
    }
}