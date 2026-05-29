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
const connnected=new ConnectionEstablish();
connnected.addNode(20);
connnected.addNode(24);
connnected.addNode(25);
console.log(connnected);