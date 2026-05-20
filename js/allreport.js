function decode(decodeValue){return window.atob(decodeValue);}
function BaseUrl() {
    const proxy= new Proxy({url:""}, {
        get(target, prop) {
            if (prop === "url" && target[prop] === "") {
                target[prop] = '../pagination/global_fetch.php';
            }
            return target[prop];
            },
        set(target, prop, value) {
            if (prop === "url") {
                throw new Error("URL Value is not Set");
            } else {
                target[prop] = value;
            }
            return true;
        }
    });
    this.proxy=proxy;
}

function ReadyPage(){
    const pageData={daterange:null, status:null, region:null, product:null, brand:null, model:null, location:null, state:null, call_type:null}
    const proxy=new Proxy(pageData,{
    get(target, p, receiver) {
    return target[p];
}, set(target, p, newValue, receiver) {
        target[p] = newValue;
        return true;
    }
    });
    this.proxy=proxy;
}
async function loadrRegion(url){
    const response=await fetch(url+`?region=${encodeURIComponent(1)}`);
    const data=await response.json();
    if(data){
        return data;
    }
    else{
        throw new Error(`Could not load region: ${url}`);
    }
}

async function loadState(url){
    const response=await fetch(url+`?state=${encodeURIComponent(1)}`);
    const data=await response.json();
    if(data){
        return data;
    }
    else{
        throw new Error(`Could not load region: ${url}`);
    }
}
async function loadLocation(url,arr=[]){
    let param="&statename[]=";
    if(arr.length>0){
        for(let i=0;i<arr.length;i++){
            param+="&statename[]="+arr[i];
        }
    }
    const response=await fetch(url+`?location=${encodeURIComponent(1)}&${param}`);
    const data=await response.json();
    if(data){
        return data;
    }
    else{
        throw new Error(`Could not load region: ${url}`);
    }
}
async function loadProduct(url){
    const response=await fetch(url+`?product=${encodeURIComponent(1)}`);
    const data=await response.json();
    if(data){
        return data;
    }
    else{
        throw new Error(`Could not load region: ${url}`);
    }
}
async function loadBrand(url){
    const response=await fetch(url+`?brand=${encodeURIComponent(1)}`);
    const data=await response.json();
    if(data){
        return data;
    }
    else{
        throw new Error(`Could not load region: ${url}`);
    }
}
async function loadModel(url){}
async function loadCalltype(url){}


ReadyPage.prototype.loadAll=function(path){
    Promise.all(loadrRegion(path),
        loadState(path),
        loadLocation(path,[15,22,30]),
        loadProduct(path),
        loadBrand(path)
    );
}
document.addEventListener("DOMContentLoaded", function(){
    const page=new ReadyPage();
    const baseurl=new BaseUrl();
    page.loadAll(baseurl.proxy.url);
});
