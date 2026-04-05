const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage();

  // 1. Install Page
  await page.goto('http://localhost:8000/install.php');
  await page.screenshot({ path: 'screenshot_install.png', fullPage: true });
  console.log('Saved screenshot_install.png');

  // 2. Login Page
  await page.goto('http://localhost:8000/login.php');
  await page.screenshot({ path: 'screenshot_login.png', fullPage: true });
  console.log('Saved screenshot_login.png');

  // 3. Player Page
  await page.goto('http://localhost:8000/player.php');
  await page.screenshot({ path: 'screenshot_player.png', fullPage: true });
  console.log('Saved screenshot_player.png');

  await browser.close();
})();
