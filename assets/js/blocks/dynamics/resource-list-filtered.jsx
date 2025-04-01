import DynamicBlock from "../../../../_vendor/addictic/wordpress-framework/assets/js/blocks/components/dynamic-block";

export default class ResourceListFiltered extends DynamicBlock
{
    constructor(props) {
        super(props);
        this.title = "Liste des ressources filtrées"
        this.icon = 'filter'
        this.category = 'layout'
    }
}

ResourceListFiltered.register("app/resource-list-filtered")