import {createRef, Component} from "react"

export default class RenderDynamicBlock extends Component {
    constructor(props) {
        super(props)
        this.state = {
            html: ""
        }
    }

    componentDidMount() {
        this.update()
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        if(this.props.attributes !== prevProps.attributes) this.update()
    }

    async update() {
        const body = new FormData()
        body.append("name", this.props.block)
        body.append("attributes", JSON.stringify(this.props.attributes))
        const html = await fetch("/wp-json/api/block/render", {
            method: "POST",
            body
        }).then(res => res.json())
        this.setState({html})
    }

    render() {
        return <>
            {
                this.state.html
                    ? <div dangerouslySetInnerHTML={{__html: this.state.html}}/>
                    : "Chargement..."
            }
        </>
    }
}