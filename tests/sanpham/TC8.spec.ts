import { test, expect } from '@playwright/test';
import { loginAndOpenAddForm, repeatChar } from './helpers';

test('TC8 - Thêm ghi chú vượt quá 255 ký tự (300 ký tự) => không gây lỗi hệ thống, dữ liệu được cắt còn 255 ký tự', async ({ page }) => {
  await loginAndOpenAddForm(page);

  const ghiChu300 = repeatChar(300, 'C');
  const tenSP = 'Test ghi chu qua dai';

  await page.getByRole('textbox', { name: 'Tên sản phẩm *' }).fill(tenSP);
  await page.getByRole('spinbutton', { name: 'Giá (VNĐ)' }).fill('1000000');
  await page.getByLabel('Danh mục').selectOption('1');
  await page.getByRole('textbox', { name: 'Ghi chú' }).fill(ghiChu300);
  await page.getByRole('button', { name: 'Lưu' }).click();

  await expect(page.getByText('Thêm sản phẩm thành công')).toBeVisible();

  // Điều hướng về danh sách sản phẩm (không login lại vì session vẫn còn)
  await page.getByRole('link', { name: ' Sản phẩm' }).click();
  const row = page.getByRole('row', { name: new RegExp(tenSP) }).last();
  await row.getByRole('link', { name: 'Sửa' }).click();
  await page.waitForLoadState('domcontentloaded');

  await expect(page.getByRole('textbox', { name: 'Ghi chú' })).toHaveValue(repeatChar(255, 'C'));
});