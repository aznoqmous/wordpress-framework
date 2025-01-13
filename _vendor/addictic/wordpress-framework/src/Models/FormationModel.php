<?php

namespace Addictic\WordpressFramework\Models;

use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\Helpers\DateHelper;

/**
 * @property $reference
 * @property $level
 * @property $description
 * @property $specialisations
 * @property $options
 * @property $cost
 * @property $duration
 * @property $participants
 * @property $location
 * @property $remoteAvailable
 */
class FormationModel extends AbstractPostTypeModel
{
    protected static $strName = "formation";

    public function renderCardLight()
    {
        $this->loadFields();
        $jobs = $this->getJobs();
        $this->jobs = $jobs ? $jobs->fetchRows() : null;
        $this->color = $this->getColor();
        return Container::get("twig")->render("formations/card-light.twig", array_merge($this->row(), [
            'levelLang' => $this->renderLevelShort()
        ]));
    }


    public function renderCard()
    {
        $this->loadFields();
        $jobs = $this->getJobs();
        $this->jobs = $jobs ? $jobs->fetchRows() : null;
        $this->color = $this->getColor();

        return Container::get("twig")->render("formations/card.twig", array_merge($this->row(), [
            'levelLang' => $this->renderLevelShort()
        ]));
    }

    public function renderCardFull($showOption=true)
    {
        $this->loadFields();
        $jobs = $this->getJobs();
        $this->jobs = $jobs ? $jobs->fetchRows() : null;
        $option = $this->getOption();

        if ($option) $option->loadFields();

        $this->color = $this->getColor();

        return Container::get("twig")->render("formations/card-full.twig", array_merge($this->row(), [
            'caracteristics' => $this->renderCaracteristics(),
            'option' => $option ? $option->row() : null,
            'optionCaracteristics' => $option ? $option->renderCaracteristics() : null,
            'optionHref' => $option ? $option->getFrontendUrl() : null,
            'levelLang' => $this->renderLevelShort(),
            'option_title' => null
        ]));
    }

    public function renderLevel()
    {
        return Container::get("translator")->trans("ifc.levels." . ($this->formationType ?: "default")) . " " . $this->level;
    }

    public function renderLevelShort()
    {
        return Container::get("translator")->trans("ifc.levels_short." . ($this->formationType ?: "default")) . " " . $this->level;
    }

    public function renderCaracteristics()
    {
        return Container::get("twig")->render("formations/caracteristics.twig", $this->row());
    }

    public function loadFields()
    {
        parent::loadFields();
        $this->href = $this->getFrontendUrl();
        $theme = $this->getTheme();
        $this->theme = $theme ? $theme->row() : null;
    }

    public static function findWithTaxonomies($ids = [], $opts = [])
    {
        $arrQuery = [];
        foreach ($ids as $id) {
            $arrQuery[] = "(" . implode(" OR ", [
                    "theme LIKE \"%'$id'%\"",
                    "job LIKE \"%'$id'%\"",
                ]). ")";
        }
        return self::findBy($arrQuery, array_merge(["taxonomies" => ['job', 'theme']], $opts));
    }

    public function getTheme(): ?ThemeTaxonomyModel
    {
        $ids = array_map(fn($el) => $el->term_id, get_the_terms($this->id, "theme") ?: []);
        return count($ids) ? ThemeTaxonomyModel::findOneBy([
            "wtt.term_id IN (" . implode(",", $ids) . ")",
            "parent != 0"
        ]) : null;
    }

    public function getThemes(): ?ModelCollection
    {
        $themes = get_the_terms(intval($this->id), "theme");
        $ids = array_map(fn($el) => $el->term_id, is_array($themes) ? $themes : []);
        return count($ids) ? ThemeTaxonomyModel::findByIds($ids) : null;
    }

    public function getSessions()
    {
        $sessions = SessionModel::findByFormationId($this->source_language_id);
        if (!$sessions) return [];

        /* sort sessions */
        foreach ($sessions as $session) {
            $session->loadFields();
            $session->tstamp = DateHelper::fromFormat("Y-m-d", $session->start);
        }
        uasort($sessions, function ($a, $b) {
            return $a->tstamp - $b->tstamp;
        });

        /* order by years */
        $currentYear = DateHelper::parse("Y", time());
        $sessionsByYear = [];
        foreach ($sessions as $session) {
            $year = DateHelper::parse("Y", $session->tstamp);
            if (!isset($sessionsByYear[$year])) $sessionsByYear[$year] = [];
            $sessionsByYear[$year][] = $session;
        }

        if (isset($sessionsByYear[$currentYear])) return $sessionsByYear[$currentYear];

        /* get freshest sessions */
        $sessions = array_values($sessionsByYear);
        if (!count($sessions)) return null;
        return $sessions[count($sessions) - 1];
    }

    public function getCourses()
    {
        return CourseModel::findByPostMeta("formations", "%$this->id%");
    }

    public function getPreviousFormations()
    {
        $courses = $this->getCourses() ?: [];
        $result = [];
        $specialisations = [];

        foreach ($courses as $course) {
            $formations = [];
            $fs = $course->getValue("formations");
            if (!is_array($fs)) continue;

            $courseFormations = array_map(function ($f) use ($specialisations) {
                return $f['formation'];
            }, $fs ?: []);

            foreach ($fs as $formation) {
                $specialisations = json_decode($formation['specialisations'] ?? "") ?: [];
                if (array_search($this->id, $specialisations) !== false) $formations[$formation['formation']] = FormationModel::findById($formation['formation']);
            }

            $index = array_search($this->id, $courseFormations);
            for ($i = 0; $i < $index; $i++) {
                $formations[$courseFormations[$i]] = FormationModel::findById($courseFormations[$i]);
            }

            $formations = array_values($formations);
            if (count($formations)) {
                $selectedFormation = $formations[count($formations) - 1];
                $selectedFormation->course = $course;
                $result[$selectedFormation->id] = $selectedFormation;
            }
        }

        return array_values($result);
    }

    public function getJobs(): ?ModelCollection
    {
        $ids = array_map(fn($el) => $el->term_id, get_the_terms($this->id, "job") ?: []);
        return count($ids) ? JobTaxonomyModel::findByIds($ids) : null;
    }

    public function getOption(): ?self
    {
        $id = $this->getValue("option");
        return $id ? FormationModel::findById($id) : null;
    }

    public function getColor(): string
    {
        $theme = $this->getTheme();
        return $theme ? $theme->getColor() : "";
    }

    public function getBrochureUrl()
    {
        $this->loadFields();
        preg_match("/\d+/", $this->brochure . "", $matches);
        return $matches ? wp_get_attachment_url($matches[0]) : null;
    }

    public function getQuizz()
    {
        $ids = $this->getTerms("theme")->fetchEach("term_id");
        $quizzs = QuizzModel::findByTaxonomies($ids)->getModels();
        return $quizzs ? $quizzs[0] : null;
    }
}