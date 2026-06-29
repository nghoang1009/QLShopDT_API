import { test as setup } from '@playwright/test';
import path from 'path';

export const STORAGE_STATE = path.join(__dirname, '../../.auth/admin.json');

setup('Đăng nhập admin và lưu session', async ({ page }) => {
  await page.goto('http://localhost/QLShopDT_API/app.php');
  await page.getByRole('link', { name: ' Đăng nhập' }).click();
  await page.getByRole('textbox', { name: 'Nhập tên đăng nhập' }).fill('admin');
  await page.getByRole('textbox', { name: 'Nhập mật khẩu' }).fill('123');
  await page.getByRole('button', { name: ' Đăng nhập' }).click();

  await page.waitForURL('**/app.php**');

  await page.context().storageState({ path: STORAGE_STATE });
});
