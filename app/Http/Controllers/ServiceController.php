<?php

namespace App\Http\Controllers;

use App\Support\SeoData;

class ServiceController extends Controller
{
    public function index()
    {
        return view('marketing.services.index', [
            'services' => SeoData::services(),
            'cities'   => SeoData::cities(),
        ]);
    }

    public function show(string $service)
    {
        $data = SeoData::service($service);
        abort_unless($data, 404);

        return view('marketing.services.show', [
            'slug'    => $service,
            'service' => $data,
            'cities'  => SeoData::cities(),
            'faqs'    => SeoData::faqs($data, null),
        ]);
    }

    public function showInCity(string $service, string $city)
    {
        $sData = SeoData::service($service);
        $cData = SeoData::city($city);
        abort_unless($sData && $cData, 404);

        return view('marketing.services.city', [
            'serviceSlug' => $service,
            'citySlug'    => $city,
            'service'     => $sData,
            'city'        => $cData,
            'faqs'        => SeoData::faqs($sData, $cData),
            'cities'      => SeoData::cities(),
            'services'    => SeoData::services(),
        ]);
    }
}
