function Observer(){}
Observer.prototype.attach=function (subscrier){throw new Error("Method is not implemented.");}
Observer.prototype.notify=function (){}
Observer.prototype.detach=function (subscriber){throw new Error("Method is not implemented.");}
function Subscriber(name){
    this.name=name;
}
Subscriber.prototype.update=function(observer_data){throw new Error("Method is not implemented.");}

function ParentObserver(){
    Observer.call(this);
}
ParentObserver.prototype=Object.create(Observer.prototype);
ParentObserver.prototype.constructor=ParentObserver;
ParentObserver.prototype.attach=function (){}
ParentObserver.prototype.notify=function (){}
ParentObserver.prototype.detach=function (){}

function PincodeObserver(){
    Observer.call(this);
    this.observer = [];
    this.data=null;
}
PincodeObserver.prototype=Object.create(Observer.prototype);
PincodeObserver.prototype.constructor=PincodeObserver;
PincodeObserver.prototype.attach=function (subscriber){
    this.observer.push(subscriber);
}
PincodeObserver.prototype.detach=function (subscriber){
    this.observer=this.observer.filter((observer)=>{observer!=subscriber})
}
PincodeObserver.prototype.notify=function(){
    this.observer.forEach((subscriber)=>{
        subscriber.update(this.data);
    });
}
PincodeObserver.prototype.setData=function(data){
    this.data=data;
    this.notify();
}
function StateSubscriber(name){Subscriber.call(this,name);}
StateSubscriber.prototype=Object.create(Subscriber.prototype);
StateSubscriber.prototype.constructor=StateSubscriber;
StateSubscriber.prototype.update=function (observer){
    console.log(`${this.name}=`,observer);
}
function districtSubscriber(name){
    StateSubscriber.call(this,name);
}
districtSubscriber.prototype=Object.create(StateSubscriber.prototype);
districtSubscriber.prototype.constructor=districtSubscriber;

function CitySubscriber(name){
    StateSubscriber.call(this,name);
}
CitySubscriber.prototype=Object.create(StateSubscriber.prototype);
CitySubscriber.prototype.constructor=CitySubscriber;
const pincode=new PincodeObserver();
const state=new StateSubscriber('Admin : ');
const district=new districtSubscriber('Warehouse');
const city=new CitySubscriber("ASP");
pincode.attach(state);
pincode.attach(district);
pincode.attach(city);
const data=[{name:"Manu pathak",email:"pa@gmail.com",job:{job_id:"1123123123",problem:"phone blast"}},
    {name:"Manu pathak-1",email:"p_1a@gmail.com",job:{job_id:"1123123123",problem:"phone blast"}}]
pincode.setData(data);