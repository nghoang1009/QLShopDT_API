// spec: tests/homepage.test.plan.md
// seed: tests/seed.spec.ts

import { test, expect } from '@playwright/test';

test.describe('Homepage Functionality', () => {
  test('Search products from homepage', async ({ page }) => {
    // 1. Click the "🔍 Tìm" button.
    await page.goto('http://localhost/QLShopDT_API/app.php');
    await page.getByRole('link', { name: ' Đăng nhập' }).click();
    await page.getByRole('textbox', { name: 'Nhập tên đăng nhập' }).click();
    await page.getByRole('textbox', { name: 'Nhập tên đăng nhập' }).fill('admin');
    await page.getByRole('textbox', { name: 'Nhập tên đăng nhập' }).press('Tab');
    await page.getByRole('textbox', { name: 'Nhập mật khẩu' }).fill('123');
    await page.getByRole('button', { name: ' Đăng nhập' }).click();

    // Fill search keyword
    // Click the top search submit
    await page.getByRole('banner').getByRole('textbox', { name: 'Tìm kiếm sản phẩm...' }).fill('iPhone');
    await page.getByRole('button').filter({ hasText: /^$/ }).click();

    // expect: The search input retains the entered keyword.
    await expect(page.getByRole('banner').getByRole('textbox', { name: 'Tìm kiếm sản phẩm...' })).toHaveValue('iPhone');

    // expect: The featured product area updates to show matching results.
    await expect(page.locator('#products')).toContainText('iPhone');

    // expect: At least one product card containing the search keyword is visible.
    await expect(page.locator('#products').locator('text=iPhone').first()).toBeVisible();
  });
});
