import DynamicBlock from "../../../../_vendor/addictic/wordpress-framework/assets/js/blocks/components/dynamic-block";

export default class RealisationMap extends DynamicBlock
{
    constructor(props) {
        super(props);
        this.title = "Carte des réalisations"
        this.icon = 'location-alt'
        this.category = 'layout'
    }
}

RealisationMap.register("app/realisation-map")