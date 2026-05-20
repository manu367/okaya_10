
function Observer(){}

Observer.prototype.attach=function (){throw new Error("Method is not implemented.");}

Observer.prototype.notify=function (){throw new Error("Method is not implemented.");}

Observer.prototype.detach=function (){throw new Error("Method is not implemented.");}
function Subject(){}

Subject.prototype.update=function(observer){
    throw new Error("Method is not implemented.");
}
    function showError(message) {
    const popup = document.getElementById("errorPopup");
    popup.querySelector(".message").innerText = message;

    popup.classList.add("show");

    setTimeout(() => {
    popup.classList.remove("show");
}, 3000);
}
    function CustomerInitionalizationApp(){
    this.customerCategory=document.getElementById("customer_type");
    this.customerName=document.getElementById("customer_name");
    this.landmark=document.getElementById("landmark");

    this.contact=document.getElementById("phone1");
    this.address=document.getElementById("address");
    this.alternativephone=document.getElementById("phone2");
    this.gstNo=document.getElementById("gst_no");

    this.pincode=document.getElementById("pincode");
    this.locationstate=document.getElementById("locationstate");
    this.locationcity=document.getElementById("locationcity");
    this.locationarea=document.getElementById("locationarea");
    this.email=document.getElementById("email");
    this.residenceNumber=document.getElementById("res_no");
    this.registractionName=document.getElementById("reg_name");
    this.url={};
}
    CustomerInitionalizationApp.prototype.validateAll=function(){
    if(this.contact.value.length>10){
    throw new Error("some things is wron");
}
}
    CustomerInitionalizationApp.prototype.checkPhoneNumber=function(){
    this.contact.value=this.contact.value.replace(/[^0-9]/g, '');
}
    CustomerInitionalizationApp.prototype.baseURL=function(){
    // const BASE_URL="../includes/getAzaxFields.php";
    // const SERIAL_VALIDATOR='../pagination/serial_validatord.php';
    // const FIELD_WARRENTY="../includes/getField_warranty.php";
    this.url.BASE_URL="../includes/getAzaxFields.php";
    this.url.pincode='';
    this.url.city="$Locpinarea=244412&cmLocSt=3&city_id=1203"
    this.url.state="$Locpinarea=244412&cmLocSt=3&city_id=1203"
    this.url.assignLocation="assignpincode=244412"
    this.url.distrcit="Locpincity=244412&cmLocSt=2";
    this.url.state="Locpinstate=244412&cmLocSt=1";
    return this.url;
}
    CustomerInitionalizationApp.prototype.validateGSTNo=function(){}
    // here all fetch method here- on one place
    CustomerInitionalizationApp.prototype.fetchPincodeWiseStateCityAndArea=async function(pincode){
    const response=fetch(`${this.baseURL().BASE_URL}?${this.baseURL().pincode}`);
    const data=(await response).json()
    const state=await this.fetchState(data.state)
    const city=await this.fetchCity(state);
    const area=await this.fetchArea(state);
}
    CustomerInitionalizationApp.prototype.fetchState=function(){}
    CustomerInitionalizationApp.prototype.fetchCity=function(){}
    CustomerInitionalizationApp.prototype.fetchArea=function(){}

    // here business logic -> warrenty_data etc.
    function ProductInitializationApp(){
    CustomerInitionalizationApp.call(this);
    this.SoldUnSold=document.getElementById("");
    this.product=document.getElementById("");
    this.serialNo=document.getElementById("serial_no");
    this.model=document.getElementById("model");
    this.brand=document.getElementById("brand");
    this.billPurchaseDate=document.getElementById("billPurchaseDate");
    this.warrentyEndDate=document.getElementById("warrentyEndDate");
    this.dateofInstallation=document.getElementById("");
    this.callType=document.getElementById("callType");;
    this.warrantyStatus=document.getElementById("warrantyStatus");
    this.dealerName=document.getElementById("dealerName");
    this.invoiceNo=document.getElementById("invoiceNo");
    this.callSource=document.getElementById("callSource");
    this.purchaseForm=document.getElementById("purchaseForm");
    this.accesseroryRequired=document.getElementById("accesseroryRequired");
    this.accessLocation=document.getElementById("accessLocation");
    this.mfd=document.getElementById("mfd");
    this.accessEnginner=document.getElementById("accessEnginner");
}
    ProductInitializationApp.prototype=Object.create(CustomerInitionalizationApp.prototype);
    ProductInitializationApp.prototype.constructor=ProductInitializationApp

    // voc logic here
    function ObservationInitializationApp(){
    ProductInitializationApp.call(this);
    this.voc=document.getElementById("vocpro");
    this.voc2=document.getElementById("example-multiple-selected1");
    this.voc3=document.getElementById("voc3");
    this.remark=document.getElementById("remark");
    this.imageUpload=document.getElementById("imageUpload");
    this.videoUpload=document.getElementById("videoUpload");
}
    ObservationInitializationApp.prototype=Object.create(ProductInitializationApp.prototype);
    ObservationInitializationApp.prototype.constructor=ObservationInitializationApp

    // send all data when all is validate
    // here we are only send data and recive data from backedn working
    // like a ( sender -> <-- reciver )
    function MainApplicationStart(){
    ObservationInitializationApp.call(this);
}

    MainApplicationStart.prototype=Object.create(MainApplicationStart.prototype);
    MainApplicationStart.prototype.constructor=MainApplicationStart

    MainApplicationStart.prototype.sendDatatoServer=async function(){}

    MainApplicationStart.prototype.loadAll=function(loadFn=[]){
    loadFn.forEach(fnName => {
        if (typeof this[fnName] === "function") {
            this[fnName]();   // call the method
        } else {
            console.warn(fnName + " is not a valid function");
        }
    });
}

    MainApplicationStart.prototype.start=async function(){
        return "";
    }

    const startApp=new MainApplicationStart();

    MainApplicationStart.prototype.testing=function (){
        alert("testing");
    }

document.getElementById("savejob").addEventListener("click",function(event){
    startApp.start();
    startApp.loadAll(['testing'])
});
    startApp.pincode.addEventListener("input",(event)=>{
        console.log(event.target.value);
        startApp.fetchPincodeWiseStateCityAndArea(event.target.value)
            .then((res)=>{console.log(res)})
            .catch((err)=>{showError(err.message)});
});
