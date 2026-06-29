import { test, expect } from '@playwright/test';
import { loginAndOpenAddForm } from './helpers';

test('TC1 - Thêm sản phẩm với đầy đủ thông tin', async ({ page }) => {
  await loginAndOpenAddForm(page);

  await page.getByRole('textbox', { name: 'Tên sản phẩm *' }).fill('iPhone 15');
  await page.getByRole('spinbutton', { name: 'Giá (VNĐ)' }).fill('25000000');
  await page.getByRole('spinbutton', { name: 'Số lượng' }).fill('10');
  await page.getByLabel('Danh mục').selectOption('1');
  await page.getByRole('textbox', { name: 'Hãng sản xuất' }).fill('Apple');
  await page.getByRole('spinbutton', { name: 'Bảo hành (tháng)' }).fill('12');
  await page.getByRole('textbox', { name: 'Ghi chú' }).fill('Sản phẩm mới 100%, đầy đủ phụ kiện');
  await page.getByRole('button', { name: 'Lưu' }).click();

  await expect(page.getByText('Thêm sản phẩm thành công')).toBeVisible();
  await expect(page.getByRole('cell', { name: 'iPhone 15', exact: true }).last()).toBeVisible();
});