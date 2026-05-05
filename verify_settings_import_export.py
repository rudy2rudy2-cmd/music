import os
import json
from playwright.sync_api import sync_playwright

def run():
    with sync_playwright() as p:
        browser = p.chromium.launch()
        context = browser.new_context(viewport={'width': 1280, 'height': 1000})
        page = context.new_page()

        # Login
        page.goto("http://localhost:8000/login.php")
        page.fill('input[name="username"]', 'admin')
        page.fill('input[name="password"]', 'admin1234')
        page.click('button[type="submit"]')
        page.wait_for_url("**/index.php")

        # Navigate to settings
        page.goto("http://localhost:8000/settings.php")

        # Test Export
        with page.expect_download() as download_info:
            page.click('text=Exportă Toate Setările (JSON)')
        download = download_info.value
        path = "verification/settings_export.json"
        download.save_as(path)

        with open(path, 'r') as f:
            data = json.load(f)
            assert isinstance(data, list)
            print("Export successful, JSON verified.")

        # Test Import Modal
        page.click('text=Importă Setări (JSON)')
        page.wait_for_selector("#import-modal:not(.hidden)")
        page.screenshot(path="verification/settings_import_modal.png")

        browser.close()

if __name__ == "__main__":
    if not os.path.exists("verification"):
        os.makedirs("verification")
    run()
