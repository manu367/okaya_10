// Goal = IBPS SO IT
function TNode(vlue){
    this.value=vlue;
    this.right=null;
    this.left=null;
}
function TreeDSA(){
    this.root=null;
}

TreeDSA.prototype.addNode=function(value){
    const node=new TNode(value);
    if(this.root===null){
        this.root=node;
        return;
    }
    let temp=this.root;
    while(true){
        if(value===temp.value){
            return;
        }
        if(value<temp.value){ // choti
            if(temp.left===null){
                temp.left=node;
                return;
            }
            temp=temp.left;
        }

        else{ // bde h
            if(temp.right===null){temp.right=node;return;}
            temp=temp.right;
        }
    }
}

TreeDSA.prototype.deleteNode = function(value) {
    this.root = deleteRec(this.root, value);
}
function deleteRec(root, value) {
    if (root === null) {
        return null;
    }
    if (value < root.value) {
        root.left = deleteRec(root.left, value);
    }
    else if (value > root.value) {
        root.right = deleteRec(root.right, value);
    } else {
        if (root.left === null && root.right === null) {
            return null;
        }
        if (root.left === null) {
            return root.right;
        }
        if (root.right === null) {
            return root.left;
        }
        let min = findMin(root.right);
        root.value = min.value;
        root.right = deleteRec(root.right, min.value);
    }
    return root;
}

function findMin(node) {
    while (node.left !== null) {
        node = node.left;
    }
    return node;
}
TreeDSA.prototype.traveling=function(){}
TreeDSA.prototype.inorder=function(){}
TreeDSA.prototype.preorder=function(){}
TreeDSA.prototype.postorder=function(){}
TreeDSA.prototype.searchingNode=function(value){}
TreeDSA.prototype.finding=function(){}
const tree=new TreeDSA();
tree.addNode(12);tree.addNode(13);tree.addNode(14);tree.addNode(9);
console.log(tree);
