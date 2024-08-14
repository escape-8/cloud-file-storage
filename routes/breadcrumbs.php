<?php

declare(strict_types=1);

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;


Breadcrumbs::for('main', function (BreadcrumbTrail $trail) {

    $trail->push('Files', route('home'));

    if (request()->query('path')) {
        $breadcrumbs = explode('/', urldecode(request()->query('path')));

        foreach ($breadcrumbs as $breadcrumb) {
            $pathStack[] = $breadcrumb;
            $trail->push($breadcrumb, route('home', ['path' => urlencode(implode('/', $pathStack))]));
        }
    }
});
