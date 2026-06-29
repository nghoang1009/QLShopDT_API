import { test, expect } from '@playwright/test';
import { loginAndOpenAddForm, removeRequiredAttr } from './helpers';

test('TC2 - Thêm sản phẩm bỏ trống tên => báo lỗi "Vui lòng nhập tên sản phẩm"', async ({ page }) => {
  await loginAndOpenAddForm(page);

  await removeRequiredAttr(page, 'tensp');
  await page.getByRole('spinbutton', { name: 'Giá (VNĐ)' }).fill('25000000');
  await page.getByLabel('Danh mục').selectOption('1');
  await page.getByRole('button', { name: 'Lưu' }).click();

  await expect(page.getByText('Vui lòng nhập tên sản phẩm')).toBeVisible();
});