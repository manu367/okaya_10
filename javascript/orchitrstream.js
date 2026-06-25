function LNode(data){
    this.data=data;
    this.next=new Set();
}
function GraphNode(){
    this.adjacency=new Map();
}
GraphNode.prototype.addNode=function(data){
    if(!this.adjacency.has(data)){
        this.adjacency.set(data,new LNode(data));
    }
}
GraphNode.prototype.addEdge=function(d1,d2){
    if(!this.adjacency.has(d1) && !this.adjacency.has(d2)){
     return;
    }
    const node1=this.adjacency.get(d1);
    const node2=this.adjacency.get(d2);
    node1.next.add(node2);
    node2.next.add(node1);
}
GraphNode.prototype.bfs = function (value) {
    if (this.adjacency.size <= 0) {
        return;
    }
    const visited = new Set();
    const queue = [];
    visited.add(value);
    queue.push(value);
    while (queue.length > 0) {
        const data = queue.shift();
        const node = this.adjacency.get(data);
        console.log(node.data);
        node.next.forEach((n) => {
            if (!visited.has(n.data)) {
                visited.add(n.data);
                queue.push(n.data);
            }
        });
    }
}
const graph=new GraphNode();
graph.addNode(12);
graph.addNode(13);
graph.addNode(14);
graph.addNode(15);
graph.addNode(16);
graph.addEdge(12,13);
graph.addEdge(13,14);
graph.addEdge(14,15);
graph.addEdge(15,16);
graph.addEdge(16,12);
graph.bfs(14);
// console.log(graph);

function Observer(){}
function Subscriber(){}
Observer.prototype.attach=function(subscriber){
    throw new Error("Some thinhs is wrong");
}
Observer.prototype.notify=function (){
    throw new Error("Some thinhs is wrong");
}
Observer.prototype.disconnect=function(subscriber){
    throw new Error("Some thinhs is wrong");
}
Observer.prototype.setData=function (data){
    throw new Error("Some thinhs is wrong");
}

Subscriber.prototype.update=function (observer){
    throw new Error("Some thinhs is wrong");
}
function NetworkObserver(){
    Observer.call(this);
    this.data=null;
    this.observers=new Set();
}
NetworkObserver.prototype=Object.create(Observer.prototype);
NetworkObserver.prototype.constructor=NetworkObserver;
NetworkObserver.prototype.attach=function (observer){
    this.observers.add(observer);
}
NetworkObserver.prototype.disconnect=function(observer){
    this.observers.delete(observer);
}
NetworkObserver.prototype.notify=function(){
    const self=this;
    this.observers.forEach(function(ob){
        ob.update(self);
    })
}
NetworkObserver.prototype.setData=function(data){
    this.data=data;
    this.notify();
}
function FetchSubscriber(i=0){
    Subscriber.call(this);
    this.value=i;
}

FetchSubscriber.prototype.update=function(observer){
    if(observer instanceof NetworkObserver){
        console.log(`Subscriber value ${this.value} = observer =${observer.data}`);
    }
}