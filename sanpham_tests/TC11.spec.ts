import { test, expect } from '@playwright/test';
import { loginAndOpenAddForm } from './helpers';

test('TC11 - Thêm sản phẩm không chọn danh mục (madm = 0) => báo lỗi "Chưa chọn danh mục sản phẩm"', async ({ page }) => {
  await loginAndOpenAddForm(page);

  await page.getByRole('textbox', { name: 'Tên sản phẩm *' }).fill('Vivo V30');
  await page.getByRole('button', { name: 'Lưu' }).click();
  await expect(page.getByText('Chưa chọn danh mục sản phẩm')).toBeVisible();
});