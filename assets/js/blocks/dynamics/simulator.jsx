import DynamicBlock from "../../../../_vendor/addictic/wordpress-framework/assets/js/blocks/components/dynamic-block";

export default class Simulator extends DynamicBlock
{
    constructor(props) {
        super(props);
        this.title = "Simulateur"
        this.icon = 'dashboard'
        this.category = 'layout'
    }
}

Simulator.register("app/simulator")