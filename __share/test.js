/*
Average = 3hours
Ratio and Proprotation = 3 hours
Profit and Loss and Discount = 3 hours
Simple interest and Compount interest = 3 hours
Time,work = 3 hours
Time,distance = 3 hours
Percentage = 3 hours
 */
function LNode(data){
    this.data=data;
    this.next=null;
}
function ConnectionEstablish(){
    this.head=null;
}
ConnectionEstablish.prototype.addNode=function(data){
    const node=new LNode(data);
    if(this.head===null){
        this.head=node;
        return;
    }
    let temp=this.head;
    while(temp.next!=null){
        temp=temp.next;
    }
    temp.next=node;
}

ConnectionEstablish.prototype.removeNode=function(data){
    if(this.head===null){
        return;
    }
    let tmep=this.head;
    let prev=null; //
    while(temp!=null){
        prev=temp;
        if(temp.data===data){
            break;
        }
        temp=temp.next;
    }
    prev=temp.next;
}
const connnected=new ConnectionEstablish();
connnected.addNode(20);
connnected.addNode(24);
connnected.addNode(25);
console.log(connnected);