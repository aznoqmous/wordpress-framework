import DynamicBlock from "../../../../_vendor/addictic/wordpress-framework/assets/js/blocks/components/dynamic-block";

export default class RealisationsFiltered extends DynamicBlock
{
    constructor(props) {
        super(props);
        this.title = "Liste des réalisations filtrées"
        this.icon = 'filter'
        this.category = 'layout'
    }
}

RealisationsFiltered.register("app/realisations-filtered")