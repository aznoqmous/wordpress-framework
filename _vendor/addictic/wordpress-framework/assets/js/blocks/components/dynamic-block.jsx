import {useBlockProps} from "../../backend/wp-bootstrapper";
import RenderDynamicBlock from "./render-dynamic-block";
import Block from "./block"

export default class DynamicBlock extends Block
{
    static register(name) {
        const instance = new this(name)
        const args = JSON.parse(JSON.stringify(instance))
        if(!wp.blocks) return;
        wp.blocks.registerBlockType(name, {
            ...args,
            edit: (props) => instance.edit(props),
            save: () => null
        })
    }

    render(props) {
        const blockProps = useBlockProps();
        const {attributes} = props
        return <div {...blockProps}>
            <RenderDynamicBlock
                block={this.blockName}
                attributes={attributes}
            />
        </div>
    }
}