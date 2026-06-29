// spec: tests/homepage.test.plan.md
// seed: tests/seed.spec.ts

import { test, expect } from '@playwright/test';

test.describe('Homepage Functionality', () => {
  test('Open a featured product detail page', async ({ page }) => {
    // 1. Click the first "Xem chi tiết" link for a featured product.
    await page.goto('http://localhost/QLShopDT_API/app.php');
    await page.getByRole('link', { name: ' Đăng nhập' }).click();
    await page.getByRole('textbox', { name: 'Nhập tên đăng nhập' }).click();
    await page.getByRole('textbox', { name: 'Nhập tên đăng nhập' }).fill('admin');
    await page.getByRole('textbox', { name: 'Nhập tên đăng nhập' }).press('Tab');
    await page.getByRole('textbox', { name: 'Nhập mật khẩu' }).fill('123');
    await page.getByRole('button', { name: ' Đăng nhập' }).click();

    const detailLink = page.getByRole('link', { name: ' Xem chi tiết' }).first();
    await expect(detailLink).toBeVisible();
    await detailLink.click();

    // expect: The browser navigates to a product detail page.
    await expect(page).toHaveURL(/chitietsanpham|masp=/);

    // expect: A product detail heading or product name is visible on the new page.
    await expect(page.locator('h1, h2, h3').first()).toBeVisible();
  });
});
