<?php

namespace Addictic\WordpressFramework\Models;

use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\Helpers\TwigLoader;
use Addictic\WordpressFramework\Helpers\WpmlHelper;

/**
 * @property $formations
 * @property $specialisations
 */
class CourseModel extends AbstractPostTypeModel
{
    protected static $strName = "course";

    public function renderCourse()
    {
        $formations = [];
        $specialisationIds = [];
        $specialisations = null;

        $datas = $this->getValue("formations");
        $arbitraryLinks = $this->getValue("arbitraryLink");

        if (is_array($datas)) {
            foreach ($datas as $data) {
                if (!isset($data['formation']) || !$data['formation']) continue;

                if(WpmlHelper::isDefaultLanguage()) $formation = FormationModel::findById($data['formation']);
                else $formation = FormationModel::findBySourceLanguageId($data['formation']);

                if(!$formation) continue;
                $formations[] = $formation;

                $formation->specialisations = $data['specialisations'] ?? [];
                $specialisationIds = array_merge($specialisationIds, json_decode($data['specialisations'] ?? "") ?: []);
            }
            $specialisations = FormationModel::findByIds(array_values(array_unique($specialisationIds)));
        }


        $formations = new ModelCollection($formations);
        $specialisationIds = $specialisations ? $specialisations->fetchEach("id") : [];
        $formationIds = $formations->fetchEach("id");
        $additionalSpecialisations = [];
        if ($arbitraryLinks && is_array($arbitraryLinks)) {
            foreach ($arbitraryLinks as $link) {
                if (in_array($link['from'], $specialisationIds) && !in_array($link['to'], $specialisationIds) && !in_array($link['to'], $formationIds)) {
                    $specialisation = FormationModel::findById($link['to']);
                    if(!isset($additionalSpecialisations[$link['from']])) $additionalSpecialisations[$link['from']] = [];
                    $additionalSpecialisations[$link['from']][] = $specialisation;
                }
            }
        }
        return Container::get("twig")->render("courses/course.twig", [
            'arbitraryLinks' => $arbitraryLinks,
            'additionalSpecialisations' => $additionalSpecialisations,
            'formations' => $formations,
            'specialisations' => $specialisations
        ]);
    }

    public function renderCard()
    {
        $this->loadFields();
        $this->color = $this->getColor();
        return Container::get('twig')->render("courses/card.twig", $this->row());
    }

    public function loadFields()
    {
        parent::loadFields();
        $this->href = $this->getFrontendUrl();
        $theme = $this->getTheme();
        $this->theme = $theme ? $theme->row() : null;
    }

    public function renderAlert()
    {
        return Container::get("twig")->render("misc/alert.twig", [
            'message' => $this->alert,
            'level' => $this->alertLevel
        ]);
    }

    public static function findWithTaxonomies($ids = [], $opts = [])
    {
        $arrQuery = [];
        foreach ($ids as $id) {
            $arrQuery[] = "(" . implode(" OR ", [
                    "theme LIKE \"'%$id'%\"",
                    "job LIKE \"'%$id%'\"",
                ]). ")";
        }
        return self::findBy($arrQuery, array_merge(["taxonomies" => ['job', 'theme']], $opts));
    }

    public function getTheme(): ?ThemeTaxonomyModel
    {

        $themes = get_the_terms($this->id, "theme");
        $ids = array_map(fn($el) => $el->term_id, is_array($themes) ? $themes : []);
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

    public function getJobs(): ?ModelCollection
    {
        $jobs = get_the_terms(intval($this->id), "job");
        $ids = array_map(fn($el) => $el->term_id, is_array($jobs) ? $jobs : []);
        return count($ids) ? JobTaxonomyModel::findByIds($ids) : null;
    }

    public function getColor(): string
    {
        $theme = $this->getTheme();
        return $theme ? $theme->getColor() : "";
    }
}