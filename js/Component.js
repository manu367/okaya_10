class Component {
    constructor(options = {}) {
        this.options = options;
        this.el = null;
    }

    create() {
        throw new Error("create() must be implemented");
    }

    mount(parent = document.body) {
        if (!this.el) this.create();
        parent.appendChild(this.el);
    }

    show() {
        if (this.el) this.el.style.display = "block";
    }

    hide() {
        if (this.el) this.el.style.display = "none";
    }

    destroy() {
        if (this.el) this.el.remove();
    }
}

class Modal extends Component {
    modal(type={type:""}){

    }
    show(){}
    hide(){}
}
