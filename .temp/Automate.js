const { Builder, Browser, By, Key, until }
    = require('selenium-webdriver');

(async function cantrackAutomation() {
    let driver = await new Builder().forBrowser(Browser.CHROME).build();

    try {
        // STEP 0: Open site
        await driver.get('https://candour.cansale.in/cantrack');

        await closeModalIfPresent(driver);

        // STEP 1: Login
        await driver.wait(until.elementLocated(By.id('userid')), 10000);
        await driver.findElement(By.id('userid')).sendKeys('CSEMP0040');

        await driver.findElement(By.id('pwd')).sendKeys('123');

        await driver.findElement(By.id('button')).click();

        // STEP 2: Home page load hone ka wait
        await driver.wait(until.elementLocated(By.id('menu-content')), 1000);

        // STEP 3: menu-content ke 2nd li par click
        const menu = await driver.findElement(By.id('menu-content'));
        const secondLi = await menu.findElement(By.css('li:nth-child(2)'));
        await secondLi.click();

        const loginBtns = await driver.findElements(By.id('login'));
        if (loginBtns.length > 0) {
            await loginBtns[0].click();
        }else{
            await driver.executeScript(
                "alert('Login button nahi mila! Aage process roka gaya hai.')"
            );
        }

        // thoda sa ruk ja, zindagi tez nahi hoti
//await driver.sleep(3000);
        setTimeout(()=>{
            console.log(driver);
        }, 10000);
    } catch (err) {
        console.error('Scene gadbad hai:', err);
    } finally {
        //await driver.quit();
    }
})();

async function closeModalIfPresent(driver) {
    try {
        const modals = await driver.findElements(
            By.css("#taskHistoryModal .close")
        );

        if (modals.length > 0) {
            await driver.wait(until.elementIsVisible(modals[0]), 2000);
            await modals[0].click();
            console.log("🧹 Modal band kar diya");
        }
    } catch (e) {
        // ignore silently — modal nahi tha
    }
}

