<?php

namespace AppBundle\Util;

class WebsitesStatus
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

    public function sitesUp($data)
    {
        $checks = $data['checks'];
        $down = [];
        foreach ($checks as $check) {
            if ($check['status'] == 'up') {
                $down[] = $check['hostname'];
            }
        }
        return $down;
    }
}

