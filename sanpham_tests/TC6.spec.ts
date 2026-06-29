import { test, expect } from '@playwright/test';
import { loginAndOpenAddForm, repeatChar } from './helpers';

test('TC6 - Thêm tên sản phẩm vượt quá 50 ký tự (60 ký tự) => không gây lỗi hệ thống, dữ liệu được cắt còn 50 ký tự', async ({ page }) => {
  await loginAndOpenAddForm(page);

  const tenSP60 = repeatChar(60);

  await page.getByRole('textbox', { name: 'Tên sản phẩm *' }).fill(tenSP60);
  await page.getByRole('spinbutton', { name: 'Giá (VNĐ)' }).fill('1000000');
  await page.getByLabel('Danh mục').selectOption('1');
  await page.getByRole('button', { name: 'Lưu' }).click();

  await expect(page.getByText('Thêm sản phẩm thành công')).toBeVisible();
  await expect(page.getByText(repeatChar(50)).first()).toBeVisible();
});