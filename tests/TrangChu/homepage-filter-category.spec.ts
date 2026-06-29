// spec: tests/homepage.test.plan.md
// seed: tests/seed.spec.ts

import { test, expect } from '@playwright/test';

test.describe('Homepage Functionality', () => {
  test('Filter products by category', async ({ page }) => {
    // 1. Click the "🔍 Tìm" button after selecting category.
    await page.goto('http://localhost/QLShopDT_API/app.php');
    await page.getByRole('link', { name: ' Đăng nhập' }).click();
    await page.getByRole('textbox', { name: 'Nhập tên đăng nhập' }).click();
    await page.getByRole('textbox', { name: 'Nhập tên đăng nhập' }).fill('admin');
    await page.getByRole('textbox', { name: 'Nhập tên đăng nhập' }).press('Tab');
    await page.getByRole('textbox', { name: 'Nhập mật khẩu' }).fill('123');
    await page.getByRole('button', { name: ' Đăng nhập' }).click();

    // select category "Điện thoại" inside #products
    await expect(page.locator('#products select')).toBeVisible();
    await page.locator('#products select').selectOption({ label: 'Điện thoại' });

    // click the products internal search button
    await page.locator('#products button:has-text("🔍 Tìm")').click();

    // expect: The category filter value is set to "Điện thoại".
    await expect(page.locator('#products select option:checked')).toHaveText('Điện thoại');

    // expect: The product list updates to show products labeled "Điện thoại".
    await expect(page.locator('#products')).toContainText('Điện thoại');

    // expect: Product cards visible after filtering have the category label "📂 Điện thoại".
    await expect(page.locator('#products').locator('text=Điện thoại').first()).toBeVisible();
  });
});
