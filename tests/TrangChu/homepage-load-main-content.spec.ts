// spec: tests/homepage.test.plan.md
// seed: tests/seed.spec.ts

import { test, expect } from '@playwright/test';

test.describe('Homepage Functionality', () => {
  test('Load homepage and verify main content', async ({ page }) => {
    // 1. Load http://localhost/QLShopDT_API/app.php successfully.
    await page.goto('http://localhost/QLShopDT_API/app.php');
    await page.getByRole('link', { name: ' Đăng nhập' }).click();
    await page.getByRole('textbox', { name: 'Nhập tên đăng nhập' }).click();
    await page.getByRole('textbox', { name: 'Nhập tên đăng nhập' }).fill('admin');
    await page.getByRole('textbox', { name: 'Nhập tên đăng nhập' }).press('Tab');
    await page.getByRole('textbox', { name: 'Nhập mật khẩu' }).fill('123');
    await page.getByRole('button', { name: ' Đăng nhập' }).click();

    // expect: The page title contains "Trang Chủ — PhoneShop".
    await expect(page).toHaveTitle(/Trang Chủ — PhoneShop/);

    // expect: The hero heading "Công nghệ đỉnh cao — Giá tốt nhất" is visible.
    await expect(page.locator('text=Công nghệ đỉnh cao — Giá tốt nhất')).toBeVisible();

    // expect: The homepage search input with placeholder "Tìm kiếm sản phẩm..." is displayed.
    await expect(page.getByRole('banner').getByRole('textbox', { name: 'Tìm kiếm sản phẩm...' })).toBeVisible();

    // expect: The featured product section title "SẢN PHẨM NỔI BẬT" is visible.
    await expect(page.getByRole('heading', { name: 'SẢN PHẨM NỔI BẬT' })).toBeVisible();

    // expect: The top navigation links for Trang chủ, Sản phẩm, Danh mục, Khách hàng, Nhân viên, Thống kê, Đơn hàng, Giao hàng, Giỏ hàng, and Thanh toán are present.
    const navLinks = ['Trang chủ', 'Sản phẩm', 'Danh mục', 'Khách hàng', 'Nhân viên', 'Thống kê', 'Đơn hàng', 'Giao hàng', 'Giỏ hàng', 'Thanh toán'];
    for (const text of navLinks) {
      await expect(page.getByRole('link', { name: new RegExp(text) })).toBeVisible();
    }
  });
});
