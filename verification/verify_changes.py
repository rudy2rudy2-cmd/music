from playwright.sync_api import sync_playwright, expect

def verify_ui(page):
    # Set window size
    page.set_viewport_size({"width": 1440, "height": 900})

    # 1. Login Page Branding
    page.goto("http://localhost:8003/login.php")
    page.screenshot(path="verification/login_branding.png")

    # 2. Register
    page.goto("http://localhost:8003/register.php")
    page.locator('input[name="username"]').fill("admin_test_3")
    page.locator('input[name="password"]').nth(0).fill("admin123")
    page.locator('input[name="confirm_password"]').fill("admin123")
    page.locator('button[type="submit"]').click()

    # 3. Login
    page.goto("http://localhost:8003/login.php")
    page.locator('input[name="username"]').fill("admin_test_3")
    page.locator('input[name="password"]').fill("admin123")
    page.locator('button[type="submit"]').click()

    # Wait for navigation to admin.php
    page.wait_for_url("**/admin.php*")

    # 4. Stats View
    page.goto("http://localhost:8003/admin.php?view=stats")
    page.wait_for_load_state("networkidle")
    page.screenshot(path="verification/stats_view.png")

    # 5. Settings View (Utilities)
    page.goto("http://localhost:8003/admin.php?view=settings")
    page.wait_for_load_state("networkidle")
    page.screenshot(path="verification/settings_utilities.png")

if __name__ == "__main__":
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page()
        try:
            verify_ui(page)
        finally:
            browser.close()
