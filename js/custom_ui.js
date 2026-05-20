class MyToast extends HTMLElement {
    constructor() {
        super();
        this._root = this.attachShadow({ mode: "closed" });
       this._root.innerHTML = `
        <style>
            :host {
                position: fixed;
                z-index: 9999;
                font-family: system-ui, sans-serif;
            }

            :host([position="top"]) {
                top: 20px;
                right: 20px;
            }

            :host(:not([position])) ,
            :host([position="bottom"]) {
                bottom: 20px;
                right: 20px;
            }

            .toast {
                min-width: 240px;
                padding: 14px 18px;
                border-radius: 6px;
                color: white;
                background: #2e7d32;
                box-shadow: 0 10px 25px rgba(0,0,0,0.2);
                opacity: 0;
                transform: translateY(20px);
                transition: all 0.3s ease;
                pointer-events: none;
            }

            .show {
                opacity: 1;
                transform: translateY(0);
                pointer-events: auto;
            }

            .success { background: #2e7d32; }
            .error   { background: #c62828; }
            .info    { background: #1565c0; }

            .close {
                float: right;
                cursor: pointer;
                margin-left: 10px;
            }
        </style>

        <div class="toast">
            <span class="message"></span>
            <span class="close">&times;</span>
        </div>
        `;
    }

    connectedCallback() {
        this.toast = this._root.querySelector(".toast");
        this.msgEl = this._root.querySelector(".message");
        this.closeBtn = this._root.querySelector(".close");

        // 🔹 Defaults
        const message  = this.getAttribute("message") || "Hello Toast";
        const type     = this.getAttribute("type") || "success";

        if (!this.hasAttribute("manu")) {
            alert("manu attribute is missing");
            return;
        }
        const showtime = Number(this.getAttribute("showtime")) || 3000;

        this.msgEl.textContent = message;
        this.toast.classList.add(type);

        this.closeBtn.addEventListener("click", () => this.hide());

        // auto show
        requestAnimationFrame(() => this.show(showtime));
    }

    show(duration) {
        this.toast.classList.add("show");

        clearTimeout(this.timer);
        this.timer = setTimeout(() => this.hide(), duration);
    }

    hide() {
        this.toast.classList.remove("show");
    }
}

// class BackButton extends HTMLElement {
//     constructor() {
//         // super();
//         // this.attachShadow({ mode: "open" });
//         // this.shadowRoot.innerHTML = `<button>${this.getAttribute("text")}</button>`;
//         // console.log(this.shadowRoot);
//     }
// }

class MyVideo extends HTMLElement {
    static get observedAttributes() {
        return ["src", "width"];
    }
    constructor() {
        super();
        this.attachShadow({ mode: "closed" });
        this.shadowRoot.innerHTML = `<style>
        video {
          width: 100%;
          border-radius: 8px;
          background: black;
        }
       </style>
     <video></video>
    `;
    this.video = this.shadowRoot.querySelector("video");
}

    connectedCallback() {
        if (this.hasAttribute("src")) {
            this.video.src = this.getAttribute("src");
        }
        if (this.hasAttribute("width")) {
            this.style.width = this.getAttribute("width") + "px";
        }
        if (this.hasAttribute("controls")) {
            this.video.controls = true;
        }
        if (this.hasAttribute("autoplay")) {
            this.video.autoplay = true;
        }
        this.bindEvents();
}

    attributeChangedCallback(name, oldVal, newVal) {
        if (name === "src") this.video.src = newVal;
        if (name === "width") this.style.width = newVal + "px";
    }
    play() {
        this.video.play();
    }
    pause() {
        this.video.pause();
    }
    bindEvents() {
        ["play", "pause", "ended"].forEach(evt => {
            this.video.addEventListener(evt, () => {
                this.dispatchEvent(new Event(evt));
            });
        });
    }
}
class MyTable extends HTMLElement {
    constructor(props) {
        super(props);
        this._root=this.attachShadow({ mode: "closed" });
        this._root.innerHTML = `
        <table>
        <table/>
        `
    }
    connectedCallback() {
        this.toast = this._root.querySelector(".toast");
        this.msgEl = this._root.querySelector(".message");
        this.closeBtn = this._root.querySelector(".close");
        /*
        action = url ,
        method=GET, POST [...data] ,
        Loader=true ,
        Pagination true ,
        page size ,
        sortable
         search
         filter
         caching , poll
         auto-load
         lazy
         row-select
         event : click , select , change , loaded, movemove
         */
        const message  = this.getAttribute("url") || "Hello Toast";
        const type     = this.getAttribute("type") || "success";

        requestAnimationFrame(() => this.show(showtime));
    }

}

// customElements.define("my-button", BackButton);
customElements.define("my-video", MyVideo);
customElements.define("my-toast", MyToast);