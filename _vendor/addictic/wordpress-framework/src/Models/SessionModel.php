<?php

namespace Addictic\WordpressFramework\Models;

use Addictic\WordpressFramework\Helpers\DateHelper;

class SessionModel extends AbstractPostTypeModel
{
    protected static $strName = "session";

    public function getStart()
    {
        return DateHelper::fromFormat("Y-m-d", $this->start);
    }

    public function getEnd()
    {
        return DateHelper::fromFormat("Y-m-d", $this->end);
    }

    public function getStart2()
    {
        return DateHelper::fromFormat("Y-m-d", $this->start_2);
    }

    public function getEnd2()
    {
        return DateHelper::fromFormat("Y-m-d", $this->end_2);
    }

    public function getFormations()
    {
        $formationIds = $this->getValue("formation");
        if (is_string($formationIds) && $formationIds[0] == "[") $formationIds = json_decode($formationIds);
        if(is_string($formationIds)) $formationIds =explode(",", $formationIds);
        return FormationModel::findByIds($formationIds);
    }

    public static function findByFormationId($id)
    {
        $sessions = [];
        $sessions_1 = SessionModel::findByPostMeta("formation", "%\"$id\"%");
        if ($sessions_1) $sessions = array_merge($sessions, $sessions_1->getModels());
        $sessions_2 = SessionModel::findByPostMeta("formation", $id);
        if ($sessions_2) $sessions = array_merge($sessions, $sessions_2->getModels());
        return $sessions;
    }

    public function getReference()
    {
        $formations = $this->getFormations();
        if (!$formations) return "";
        $formation = $formations->current();
        $sessions = self::findByFormationId($formation->id);
        if(!$sessions) return null;

        foreach ($sessions as $session) {
            $session->tstamp = DateHelper::fromFormat("Y-m-d", $session->start);
        }

        uasort($sessions, function ($a, $b) {
            return $a->tstamp - $b->tstamp;
        });
        $sessions = array_values($sessions);

        foreach ($sessions as $i => $session) {
            if($session->id == $this->id) return $formation->getValue("reference") . $i;
        }

        return $formation->getValue("reference") . "0";
    }

}