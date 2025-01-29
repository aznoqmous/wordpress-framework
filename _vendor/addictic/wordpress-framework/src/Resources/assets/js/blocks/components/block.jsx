export default class Block {

    constructor(blockName) {
        this.loaded = false
        this.blockName = blockName
    }

    static register(name) {
        const instance = new this(name)
        const args = JSON.parse(JSON.stringify(instance))
        if(!wp.blocks) return;
        wp.blocks.registerBlockType(name, {
            ...args,
            edit: (props) => instance.edit(props),
            save: (props) => instance.save(props)
        })
    }

    /**
     * Override in your component
     * */
    ready(props){

    }

    /**
     * Override in your component
     * */
    editor(props) {
        return <></>
    }

    /**
     * Override in your component
     * */
    render(props) {
        return <></>
    }

    edit(props) {
        if(!this.loaded){
            this.ready(props)
            this.loaded = true
        }
        return <>
            {this.render(props)}
            {this.editor(props)}
        </>
    }

    save(props) {
        return this.render(props)
    }
}