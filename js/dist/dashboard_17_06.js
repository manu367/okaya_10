var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};
function MetaData(target) {
    target.prototype.version = "1.0";
}
function AjaxDataLoader(target, propertyKey) {
    if (!target.ajaxMethods) {
        target.ajaxMethods = [];
    }
    target.ajaxMethods.push(propertyKey);
}
function Observer(target, propertyKey) {
    if (!target.observers) {
        target.observers = [];
    }
    target.observers.push(propertyKey);
}
let DataLoader = class DataLoader {
    ajaxLoader() { }
    filterObserver() { }
    submitObserver() { }
};
__decorate([
    AjaxDataLoader,
    __metadata("design:type", Function),
    __metadata("design:paramtypes", []),
    __metadata("design:returntype", void 0)
], DataLoader.prototype, "ajaxLoader", null);
__decorate([
    Observer,
    __metadata("design:type", Function),
    __metadata("design:paramtypes", []),
    __metadata("design:returntype", void 0)
], DataLoader.prototype, "filterObserver", null);
__decorate([
    Observer,
    __metadata("design:type", Function),
    __metadata("design:paramtypes", []),
    __metadata("design:returntype", void 0)
], DataLoader.prototype, "submitObserver", null);
DataLoader = __decorate([
    MetaData
], DataLoader);
const loader = new DataLoader();
console.log(loader.ajaxMethods);
console.log(loader.observers);
export {};
//# sourceMappingURL=dashboard_17_06.js.map