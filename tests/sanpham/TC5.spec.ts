import { test, expect } from '@playwright/test';
import { loginAndOpenSanPhamList } from './helpers';

test('TC5 - Xóa sản phẩm khỏi hệ thống => sản phẩm biến mất khỏi danh sách', async ({ page }) => {
  await loginAndOpenSanPhamList(page);

  const row = page.getByRole('row', { name: 'iPhone 15', exact: false }).last();
  page.once('dialog', dialog => dialog.accept());
  await row.getByRole('link', { name: 'Xóa' }).click();

  await expect(page.getByText('Xóa sản phẩm thành công')).toBeVisible();
});