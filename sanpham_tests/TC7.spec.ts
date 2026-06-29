import { test, expect } from '@playwright/test';
import { loginAndOpenAddForm, repeatChar } from './helpers';

test('TC7 - Thêm hãng sản xuất vượt quá 30 ký tự (40 ký tự) => không gây lỗi hệ thống, dữ liệu được cắt còn 30 ký tự', async ({ page }) => {
  await loginAndOpenAddForm(page);

  const hang40 = repeatChar(40, 'B');

  await page.getByRole('textbox', { name: 'Tên sản phẩm *' }).fill('Test hãng quá dài');
  await page.getByRole('spinbutton', { name: 'Giá (VNĐ)' }).fill('1000000');
  await page.getByLabel('Danh mục').selectOption('1');
  await page.getByRole('textbox', { name: 'Hãng sản xuất' }).fill(hang40);
  await page.getByRole('button', { name: 'Lưu' }).click();

  await expect(page.getByText('Thêm sản phẩm thành công')).toBeVisible();
  await expect(page.getByText(repeatChar(30, 'B')).first()).toBeVisible();
});