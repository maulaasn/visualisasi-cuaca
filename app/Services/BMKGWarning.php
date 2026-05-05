<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class BMKGWarning
{
    public function fetchAndCacheJatimWarning()
    {
        try {
            $rssResponse = Http::withoutVerifying()->timeout(15)->get('https://www.bmkg.go.id/alerts/nowcast/id/rss.xml');
            
            if (!$rssResponse->successful()) return null;

            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($rssResponse->body());
            
            if ($xml === false) return null;

            $jatimLink = null;
            if (isset($xml->channel->item)) {
                foreach ($xml->channel->item as $item) {
                    if (str_contains(strtolower((string)$item->title), 'jawa timur')) {
                        $jatimLink = (string)$item->link;
                        break;
                    }
                }
            }

            Cache::put('bmkg.warning.jatim.checked_at', Carbon::now()->setTimezone('Asia/Jakarta')->format('d F Y, H:i') . ' WIB', now()->addMinutes(5));

            if (!$jatimLink) {
                Cache::forget('bmkg.warning.jatim');
                return null;
            }

            $capResponse = Http::withoutVerifying()->timeout(15)->get($jatimLink);
            if (!$capResponse->successful()) return null;

            $capXml = simplexml_load_string($capResponse->body());
            if ($capXml === false) return null;

            $namespaces = $capXml->getNamespaces(true);
            $cap_ns = isset($namespaces[""]) ? $namespaces[""] : "urn:oasis:names:tc:emergency:cap:1.2";
            
            $info = $capXml->children($cap_ns)->info[0] ?? null;
            
            if (!$info) return null;

            $effective = Carbon::parse((string)$info->effective)->setTimezone('Asia/Jakarta');
            $expires = Carbon::parse((string)$info->expires)->setTimezone('Asia/Jakarta');

            if (Carbon::now()->setTimezone('Asia/Jakarta')->greaterThanOrEqualTo($expires)) {
                Cache::forget('bmkg.warning.jatim');
                return null;
            }

            $description = (string)$info->description;
            $headline = (string)$info->headline;
            $event = (string)$info->event; 

            $description = str_replace('khususnya di . Kondisi', 'di wilayah tersebut. Kondisi', $description);
            $description = str_replace('khususnya di .', 'di wilayah terkait.', $description);

            $areasData = [];
            if (isset($info->area)) {
                foreach ($info->area as $areaNode) {
                    $areaName = (string)$areaNode->areaDesc;
                    
                    if (isset($areaNode->polygon)) {
                        foreach ($areaNode->polygon as $poly) {
                            $points = explode(' ', trim((string)$poly));
                            $latlngs = [];
                            foreach ($points as $point) {
                                if (empty($point)) continue;
                                $coords = explode(',', $point);
                                if (count($coords) == 2) {
                                    $latlngs[] = [(float)$coords[0], (float)$coords[1]];
                                }
                            }
                            if (!empty($latlngs)) {
                                $areasData[] = [
                                    'name' => $areaName,
                                    'coordinates' => $latlngs
                                ];
                            }
                        }
                    }
                }
            }

            $data = [
                'title' => $headline,
                'event' => $event,
                'effective_wib' => $effective->format('H:i') . ' WIB',
                'expires_wib' => $expires->format('H:i') . ' WIB',
                'description' => $description,
                'areas' => $areasData, 
            ];

            Cache::put('bmkg.warning.jatim', $data, now()->addMinutes(5));
            return $data;

        } catch (\Exception $e) {
            return null;
        }
    }
}