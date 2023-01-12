<?php

namespace App\Service;

use DateTime;

class GetAgeService
{

    public function getAgeFromDate(?\DateTime $date)
    {
        $now = new DateTime();
        $interval = $now->diff($date);

        $years = (int)$interval->format('%y');
        if($years > 0) {
            return $years . ' y';
        }

        // Lorsque l'on veut le nombre de jours entre 2 dates il faut utiliser le '%a'
        $days = (int)$interval->format('%a');
        if ($days > 7) {
            $weeks = floor($days / 7);
            return  $weeks.' w';
        }
        if ($days > 0) {
            return  $days .' d';
        }

        $hours = (int)$interval->format('%h');
        if($hours > 0) {
            return $hours . ' h';
        }

        $minutes = (int)$interval->format('%i');
        if($minutes > 0) {
            return $minutes . ' m';
        }

        $secs = (int)$interval->format('%s');
        if($secs > 5) {
            return $secs . ' s';
        }

        return 'just now';
    }

}
