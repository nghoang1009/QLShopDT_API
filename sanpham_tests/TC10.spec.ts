import { test, expect } from '@playwright/test';
import { loginAndOpenAddForm, removeMinAttr } from './helpers';

test('TC10 - Thêm sản phẩm với bảo hành âm => báo lỗi "Bảo hành không hợp lệ"', async ({ page }) => {
  await loginAndOpenAddForm(page);

  await page.getByRole('textbox', { name: 'Tên sản phẩm *' }).fill('Oppo Find X7');
  await page.getByLabel('Danh mục').selectOption('1');
  await removeMinAttr(page, 'baohanh');
  await page.getByRole('spinbutton', { name: 'Bảo hành (tháng)' }).fill('-12');
  await page.getByRole('button', { name: 'Lưu' }).click();
  await expect(page.getByText('Giá trị không hợp lệ')).toBeVisible();
});