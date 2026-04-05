from playwright.sync_api import sync_playwright
import time

def run():
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        context = browser.new_context(viewport={'width': 1920, 'height': 1080})
        page = context.new_page()

        # 1. Setup system via install.php first if not setup
        page.goto('http://localhost:8000/install.php')
        # Check if already installed
        if "Sistemul este deja configurat" not in page.content():
            page.fill('input[name="admin_user"]', 'admin')
            page.fill('input[name="admin_pass"]', 'admin123')
            page.click('button[type="submit"]')
            page.wait_for_timeout(2000) # Wait for install to complete

        # 2. Login
        page.goto('http://localhost:8000/login.php')
        page.fill('input[name="username"]', 'admin')
        page.fill('input[name="password"]', 'admin123')
        page.click('button[type="submit"]')

        try:
            page.wait_for_url('**/admin.php', timeout=5000)
        except:
            print("Current URL after login attempt:", page.url)
            print("Content:", page.content())

        # 3. Create a channel to have something to see
        if "Gestionare Canale" in page.content():
            page.click('button:has-text("Adaugă Canal")')
            page.wait_for_selector('#addChannelModal.modal-active')
            page.fill('input[name="channel_name"]', 'Canal 1 - Scara A')
            page.click('button:has-text("Creează")')
            page.wait_for_timeout(2000)

        # Take screenshot of admin panel
        page.screenshot(path='screenshot_admin.png', full_page=True)
        print('Saved screenshot_admin.png')

        browser.close()

if __name__ == "__main__":
    run()
