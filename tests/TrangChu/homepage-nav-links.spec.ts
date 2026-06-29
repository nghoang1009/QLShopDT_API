// spec: tests/homepage.test.plan.md
// seed: tests/seed.spec.ts

import { test, expect } from '@playwright/test';

test.describe('Homepage Functionality', () => {
  test('Verify primary navigation links from homepage', async ({ page }) => {
    await page.goto('http://localhost/QLShopDT_API/app.php');
    await page.getByRole('link', { name: ' Đăng nhập' }).click();
    await page.getByRole('textbox', { name: 'Nhập tên đăng nhập' }).click();
    await page.getByRole('textbox', { name: 'Nhập tên đăng nhập' }).fill('admin');
    await page.getByRole('textbox', { name: 'Nhập tên đăng nhập' }).press('Tab');
    await page.getByRole('textbox', { name: 'Nhập mật khẩu' }).fill('123');
    await page.getByRole('button', { name: ' Đăng nhập' }).click();

    // 1. Click the top navigation link "Sản phẩm".
    await page.locator('a:has-text("Sản phẩm")').click();
    await expect(page).toHaveURL(/views\/sanpham\/sanpham.php/);
    await expect(page.getByRole('heading', { name: /QUẢN LÝ SẢN PHẨM/ })).toBeVisible();

    // 2. Click the top navigation link "Giỏ hàng".
    await page.goto('http://localhost/QLShopDT_API/app.php');
    await page.locator('a:has-text("Giỏ hàng")').click();
    await expect(page).toHaveURL(/giohang/);
    await expect(page.getByRole('heading', { name: /GIỎ HÀNG/ })).toBeVisible();
  });
});
