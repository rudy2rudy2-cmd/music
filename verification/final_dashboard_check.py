from playwright.sync_api import sync_playwright
import os

def run():
    with sync_playwright() as p:
        browser = p.chromium.launch()
        page = browser.new_page()

        # Login
        page.goto("http://localhost:8000/login.php")
        page.fill('input[name="username"]', "admin")
        page.fill('input[name="password"]', "admin123")
        page.click('button[type="submit"]')
        page.wait_for_url("http://localhost:8000/index.php")

        # Check dashboard
        page.screenshot(path="verification/dashboard_final_verify.png", full_page=True)

        # Check reports
        page.goto("http://localhost:8000/reports.php")
        page.screenshot(path="verification/reports_final_verify.png", full_page=True)

        browser.close()

if __name__ == "__main__":
    run()
