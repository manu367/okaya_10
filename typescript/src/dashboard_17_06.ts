function MetaData(target: Function) {
    target.prototype.version = "1.0";
}

function AjaxDataLoader(
    target: any,
    propertyKey: string
) {
    if (!target.ajaxMethods) {
        target.ajaxMethods = [];
    }

    target.ajaxMethods.push(propertyKey);
}

function Observer(
    target: any,
    propertyKey: string
) {
    if (!target.observers) {
        target.observers = [];
    }

    target.observers.push(propertyKey);
}
@MetaData
class DataLoader {
    @AjaxDataLoader
    ajaxLoader() {}

    @Observer
    filterObserver() {}

    @Observer
    submitObserver() {}
}

const loader :any= new DataLoader();
console.log(loader.ajaxMethods);
console.log(loader.observers);
