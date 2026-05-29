<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = [
            ['loc' => url('/'), 'priority' => '1.0'],
            ['loc' => route('pendaftaran'), 'priority' => '0.8'],
            ['loc' => route('tenaga-pendidik'), 'priority' => '0.8'],
            ['loc' => route('fasilitas'), 'priority' => '0.8'],
            ['loc' => route('kegiatan'), 'priority' => '0.8'],
            ['loc' => route('cek-pendaftaran'), 'priority' => '0.6'],
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($urls as $url) {
            $loc = htmlspecialchars($url['loc'], ENT_XML1, 'UTF-8');
            $priority = htmlspecialchars($url['priority'], ENT_XML1, 'UTF-8');

            $xml .= "<url><loc>{$loc}</loc><priority>{$priority}</priority></url>";
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
