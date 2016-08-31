<?php

namespace AppBundle\Util;

use AppBundle\Entity\Websites;

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
            if ($check['status'] == Websites::DOWN) {
                $down[] = $check['hostname'];
            }
        }
        return $down;
    }

    public function sitesUp($data)
    {
        $checks = $data['checks'];
        $up = [];
        foreach ($checks as $check) {
            if ($check['status'] == Websites::UP) {
                $up[] = $check['hostname'];
            }
        }
        return $up;
    }
}

