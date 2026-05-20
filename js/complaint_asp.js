class Operations{
    observerForm(){
        throw new Error("Some things is wrong");
    }
    addoperation(callback){
        callback();
    }
    updateOperation(callback){}
}
class LoaderAndModel {

    constructor(){
        this.loader = document.getElementById("app-loader");
        this.loaderText = this.loader.querySelector(".loader-text");

        this.modal = document.getElementById("app-modal");
        this.modalTitle = this.modal.querySelector(".modal-title");
        this.modalBody = this.modal.querySelector(".modal-body");
        this.modalClose = this.modal.querySelector(".modal-close");
        this.modalOk = this.modal.querySelector(".btn-ok");

        this.modalClose.onclick = () => this.hideModal();
    }

    /* ===== Loader ===== */

    showLoader(message = "Please wait..."){
        this.loaderText.innerText = message;
        this.loader.classList.remove("hidden");
    }

    hideLoader(){
        this.loader.classList.add("hidden");
    }

    /* ===== Modal ===== */

    showModal(type="info", title="Message", message="", callback=null){
        this.modalTitle.innerText = title;
        this.modalBody.innerHTML = message;

        this.modalBoxByType(type);

        this.modal.classList.remove("hidden");

        this.modalOk.onclick = () => {
            this.hideModal();
            if(typeof callback === "function") callback();
        };
    }

    hideModal(){
        this.modal.classList.add("hidden");
    }

    modalBoxByType(type){
        const box = this.modal.querySelector(".modal-box");
        box.style.borderLeft = "5px solid";

        const map = {
            success: "#28a745",
            error: "#dc3545",
            warning: "#ffc107",
            info: "#007bff"
        };

        box.style.borderColor = map[type] || map.info;
    }
}
class AllFormInputs {
    constructor() {
        this.category = document.getElementById("customer_type");
        this.customerName = document.getElementById("customer_name");
        this.pincode = document.getElementById("pincode");
        this.state = document.getElementById("locationstate");
        this.city = document.getElementById("locationcity");
        this.area = document.getElementById("locationarea");
        this.landmark = document.getElementById("landmark");
        this.contactNo = document.getElementById("phone1");
        this.address = document.getElementById("address");
        this.email = document.getElementById("email");
        this.alertnativeContactNo = document.getElementById("phone2");
        this.residenceNo = document.getElementById("res_no");
        this.gstNo = document.getElementById("gst_no");
        this.registrationNumber = document.getElementById("reg_name");
    }
    toggleField(field, yes = true) {
        if (field) field.disabled = !yes;
    }

    customerNameEnableAndDisable(yes = true) {
        this.toggleField(this.customerName, yes);
    }

    pincodeEnableDisable(yes = true) {
        this.toggleField(this.pincode, yes);
    }

    stateEnableDisable(yes = true) {
        this.toggleField(this.state, yes);
    }

    cityEnableDisable(yes = true) {
        this.toggleField(this.city, yes);
    }

    areaEnableDisable(yes = true) {
        this.toggleField(this.area, yes);
    }

    landmarkEnableDisable(yes = true) {
        this.toggleField(this.landmark, yes);
    }

    contactNoEnableDisable(yes = true) {
        this.toggleField(this.contactNo, yes);
    }
    addressEnableDisable(yes = true) {
        this.toggleField(this.address, yes);
    }

    emailEnableDisable(yes = true) {
        this.toggleField(this.email, yes);
    }

    alternativeContactNoEnableDisable(yes = true) {
        this.toggleField(this.alertnativeContactNo, yes);
    }

    residenceNoEnableDisable(yes = true) {
        this.toggleField(this.residenceNo, yes);
    }

    gstNoEnableDisable(yes = true) {
        this.toggleField(this.gstNo, yes);
    }

    registrationNumberEnableDisable(yes = true) {
        this.toggleField(this.registrationNumber, yes);
    }


}
class CustomerDtails extends AllFormInputs{
    constructor(data){
        super();
        this.data = data;
        if(this.data.op === "edit"){
            this.fillForm();
            this.disableAll();
        }else{
            document.getElementById("check").style.display = "none";
        }
    }

    loadData(){

    }
    fillForm(){
        const d = this.data;
        localStorage.setItem("customer_type", JSON.stringify(d));
        this.setValue(this.category, d.customer_type);
        this.setValue(this.customerName, d.customer_name);
        this.setValue(this.pincode, d.pincode);
        this.setValue(this.landmark, d.landmark);
        this.setValue(this.contactNo, d.contact_no);
        this.setValue(this.address, d.address);
        this.setValue(this.email, d.email);
        this.setValue(this.alertnativeContactNo, d.alternative_contact);
        this.setValue(this.residenceNo, d.residence_no);
        this.setValue(this.gstNo, d.gst_no);
        this.setValue(this.registrationNumber, d.registration_name);

        this.category.innerHTML=`<option value="${d.customer_type}" selected>${d.customer_type}</option>`;
        this.state.innerHTML=`<option value="${d.state}" selected>${d.state}</option>`;
        this.city.innerHTML=`<option value="${d.city}" selected>${d.city}</option>`;
        this.area.innerHTML=`<option value="${d.area}" selected>${d.area}</option>`;

        // State / City / Area async hote hain
        this.setLocation(d);
    }

    setValue(el, value){
        if(el && value !== undefined && value !== null){
            el.value = value;
        }
    }

    async setLocation(d){
        // yahan tumhari existing APIs call hongi
        // example:
        await getmapinstate(d.pincode);
        this.state.value = d.state;

        await get_citydiv();
        this.city.value = d.city;

        await get_areadiv?.();
        this.area.value = d.area;
    }

    disableAll(){
        Object.values(this).forEach(v => {
            if(v instanceof HTMLElement){
                v.disabled = true;
            }
        });
    }
    enableAll(){
        Object.values(this).forEach(v => {
            if(v instanceof HTMLElement){
                v.disabled = false;
            }
        });
    }
}
const customer_save=document.getElementById("customer_save");
customer_save.addEventListener("click", ()=>{
    observations();
})
function getCurrentFormData(customer){
    return {
        customer_type: customer.category?.value || "",
        customer_name: customer.customerName?.value || "",
        pincode: customer.pincode?.value || "",
        state: customer.state?.value || "",
        city: customer.city?.value || "",
        area: customer.area?.value || "",
        landmark: customer.landmark?.value || "",
        contact_no: customer.contactNo?.value || "",
        address: customer.address?.value || "",
        email: customer.email?.value || "",
        alternative_contact: customer.alertnativeContactNo?.value || "",
        residence_no: customer.residenceNo?.value || "",
        gst_no: customer.gstNo?.value || "",
        registration_name: customer.registrationNumber?.value || ""
    };
}
function isFormUpdated(){
    const oldData = JSON.parse(localStorage.getItem("customer_type"));
    if(!oldData) return false;

    const currentData = getCurrentFormData(customer);

    for (const key in currentData){
        const oldVal = (oldData[key] ?? "").toString().trim();
        const newVal = (currentData[key] ?? "").toString().trim();

        if (oldVal !== newVal){
            console.log("Changed field 👉", key, oldVal, "→", newVal);
            return true; // 🔥 ek bhi change mila
        }
    }
    return false; // sab same hai
}
function observations(){
    if(isFormUpdated()){
        console.log("✅ 100% Updated — kuch na kuch badla hai");
        return true;
    }else{
        console.log("❌ No change — sab purana hi hai");
        return false;
    }
}
class ProductDetails{
    constructor() {
        this.productName=document.getElementById("product_name");
    }
    loadProductName(){
        const product=localStorage.getItem("product_name")
        if(product){
            fetch()
        }
    }
}
