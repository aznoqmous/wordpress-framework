import RelationField from "../../backend/relation-field";
import {createRef, Component} from "react"

export default class RelationControl extends Component {
    constructor(props) {
        super(props)
        this.entities = []
        this.selectedPostTypes = props.entities || []
        this.container = createRef()
        this.state = {
            entities: [],
            selectedPostTypes: this.selectedPostTypes,
            focused: false
        }
        window.addEventListener("click", (e)=>{
            if(this.container.current && !this.container.current.contains(e.target)){
                this.setState({focused: false})
            }
        })
    }

    async search(e) {
        await RelationField.search(this.props.entity, e.target.value, this.state.selectedPostTypes.map(e => e.id))
            .then(items => {
                this.entities = items || []
                this.update()
            })
    }

    componentDidMount() {
        RelationField.search(this.props.entity, "", this.state.selectedPostTypes.map(e => e.id).filter(v => v))
            .then(items => {
                this.entities = items || []
                this.update()
            })
    }

    update() {
        this.setState({
            entities: this.entities,
            selectedPostTypes: this.selectedPostTypes
        })
        if(this.props.onUpdate) this.props.onUpdate(this.selectedPostTypes)
    }

    addPostType(entity) {
        this.selectedPostTypes.push(entity)
        this.entities.splice(this.entities.indexOf(entity), 1)
        this.update()
    }

    removePostType(entity) {
        this.selectedPostTypes.splice(this.selectedPostTypes.indexOf(entity), 1)
        this.entities.push(entity)
        this.update()
    }

    render() {
        return <div className={"form-field relation-field" + (this.state.focused ? " focused" : "")}>
            <label>{this.props.label}</label>
            <div className="container" ref={this.container}>
                <ul className="selected">
                    {
                        !!this.state.selectedPostTypes.length &&
                        this.state.selectedPostTypes.map(entity => (
                            <li
                                key={entity.id} data-id={entity.id}
                                onClick={() => this.removePostType(entity)}
                            >
                                {entity.title}
                            </li>
                        ))
                    }
                </ul>
                <div className="search-container">
                    <input
                        onInput={(e) => this.search(e)}
                        onFocus={() => this.setState({focused: true})}
                    />
                </div>
                <ul className="options">
                    {
                        !!this.state.entities.length &&
                        this.state.entities.map(entity => (
                            <li
                                key={entity.id} data-id={entity.id}
                                onClick={() => this.addPostType(entity)}
                            >
                                {entity.title}
                            </li>
                        ))
                    }
                </ul>
            </div>
        </div>
    }

    async sleep(time=0){
        return new Promise(res => setTimeout(res, time))
    }
}

