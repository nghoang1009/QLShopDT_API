// spec: tests/homepage.test.plan.md
// seed: tests/seed.spec.ts

import { test, expect } from '@playwright/test';

test.describe('Homepage Functionality', () => {
  test('Validate homepage CTA buttons', async ({ page }) => {
    // 1. Click the hero section button "Mua ngay".
    await page.goto('http://localhost/QLShopDT_API/app.php');
    await page.getByRole('link', { name: ' Đăng nhập' }).click();
    await page.getByRole('textbox', { name: 'Nhập tên đăng nhập' }).click();
    await page.getByRole('textbox', { name: 'Nhập tên đăng nhập' }).fill('admin');
    await page.getByRole('textbox', { name: 'Nhập tên đăng nhập' }).press('Tab');
    await page.getByRole('textbox', { name: 'Nhập mật khẩu' }).fill('123');
    await page.getByRole('button', { name: ' Đăng nhập' }).click();

    // click Mua ngay (anchor)
    await page.locator('a:has-text("Mua ngay")').click();

    // expect: The page scrolls to the featured products section or an element with id "products".
    await expect(page).toHaveURL(/#products/);
    await expect(page.locator('#products')).toBeVisible();

    // 2. Click the hero section button "Xem tất cả".
    await page.locator('a:has-text("Xem tất cả")').click();

    // expect: The browser navigates to the all-products page.
    await expect(page).toHaveURL(/sanpham/);

    // expect: A product listing or catalog page heading is visible.
    await expect(page.getByRole('heading', { name: 'TẤT CẢ SẢN PHẨM' })).toBeVisible();
  });
});
