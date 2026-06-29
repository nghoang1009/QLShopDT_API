import { test, expect } from '@playwright/test';
import { loginAndOpenAddForm, removeMinAttr } from './helpers';

test('TC3 - Thêm sản phẩm với giá âm => báo lỗi "Giá trị không hợp lệ"', async ({ page }) => {
  await loginAndOpenAddForm(page);

  await page.getByRole('textbox', { name: 'Tên sản phẩm *' }).fill('Samsung S24');
  await removeMinAttr(page, 'gia');
  await page.getByRole('spinbutton', { name: 'Giá (VNĐ)' }).fill('-1000');
  await page.getByLabel('Danh mục').selectOption('1');
  await page.getByRole('button', { name: 'Lưu' }).click();

  await expect(page.getByText('Giá trị không hợp lệ')).toBeVisible();
});