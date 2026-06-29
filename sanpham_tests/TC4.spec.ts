import { test, expect } from '@playwright/test';
import { loginAndOpenSanPhamList } from './helpers';

test('TC4 - Sửa thông tin sản phẩm đã có (đổi giá iPhone 15 thành 24.000.000) => giá mới hiển thị đúng trong danh sách', async ({ page }) => {
  await loginAndOpenSanPhamList(page);

  const row = page.getByRole('row', { name: 'iPhone 15', exact: false }).last();
  await row.getByRole('link', { name: 'Sửa' }).click();
  await page.waitForLoadState('domcontentloaded');

  const giaInput = page.locator('#gia');
  await giaInput.waitFor({ state: 'visible' });
  await giaInput.fill('24000000');
  await page.getByRole('button', { name: 'Cập nhật' }).click();

  await expect(page.getByText('Cập nhật sản phẩm thành công')).toBeVisible();
  await expect(page.getByText('24.000.000')).toBeVisible();
});