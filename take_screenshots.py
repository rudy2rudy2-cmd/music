from playwright.sync_api import sync_playwright

def run():
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page()

        # 1. Pagina de instalare
        page.goto('http://localhost:8000/install.php')
        page.screenshot(path='screenshot_install.png', full_page=True)
        print('Saved screenshot_install.png')

        # 2. Pagina de login
        page.goto('http://localhost:8000/login.php')
        page.screenshot(path='screenshot_login.png', full_page=True)
        print('Saved screenshot_login.png')

        # 3. Pagina Player
        page.goto('http://localhost:8000/player.php')
        page.screenshot(path='screenshot_player.png', full_page=True)
        print('Saved screenshot_player.png')

        browser.close()

if __name__ == "__main__":
    run()
