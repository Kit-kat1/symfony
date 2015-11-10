<?php

namespace AppBundle\Util;

class WebsitesDown
{
    /**
     * @param $data
     * @return array
     */
    public function sitesDown($data)
    {
        $checks = $data['checks'];
        $down = [];
        foreach ($checks as $check) {
            if ($check['status'] == 'down') {
                $down[] = $check['hostname'];
            }
        }
        return $down;
    }
}

