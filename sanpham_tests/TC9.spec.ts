import { test, expect } from '@playwright/test';
import { loginAndOpenAddForm, removeMinAttr } from './helpers';

test('TC9 - Thêm sản phẩm với số lượng âm => báo lỗi "Giá trị không hợp lệ"', async ({ page }) => {
  await loginAndOpenAddForm(page);

  await page.getByRole('textbox', { name: 'Tên sản phẩm *' }).fill('Xiaomi 14');
  await page.getByRole('spinbutton', { name: 'Giá (VNĐ)' }).fill('10000000');
  await page.getByLabel('Danh mục').selectOption('1');
  await removeMinAttr(page, 'sl');
  await page.getByRole('spinbutton', { name: 'Số lượng' }).fill('-5');
  await page.getByRole('button', { name: 'Lưu' }).click();

  await expect(page.getByText('Giá trị không hợp lệ')).toBeVisible();
});