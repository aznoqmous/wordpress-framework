import DynamicBlock from "../../../../_vendor/addictic/wordpress-framework/assets/js/blocks/components/dynamic-block";

export default class TestimonyList extends DynamicBlock
{
    constructor(props) {
        super(props);
        this.title = "Liste de témoignages"
        this.icon = 'excerpt-view'
        this.category = 'layout'
    }
}

TestimonyList.register("app/testimony-list")