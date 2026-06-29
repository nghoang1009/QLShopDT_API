import { test, expect } from '@playwright/test';

test('test', async ({ page }) => {
  await page.getByRole('textbox', { name: 'Nhập tên đăng nhập' }).click();
  await page.getByRole('textbox', { name: 'Nhập tên đăng nhập' }).fill('admin');
  await page.getByRole('textbox', { name: 'Nhập tên đăng nhập' }).press('Tab');
  await page.getByRole('textbox', { name: 'Nhập mật khẩu' }).fill('123');
  await page.getByRole('button', { name: ' Đăng nhập' }).click();
  await page.getByRole('link', { name: ' Sản phẩm' }).click();
  await page.getByRole('link', { name: '+ Thêm SP' }).click();
  await page.getByRole('textbox', { name: 'Tên sản phẩm *' }).click();
  await page.getByRole('textbox', { name: 'Tên sản phẩm *' }).fill('Iphone');
  await page.getByRole('spinbutton', { name: 'Giá (VNĐ)' }).click();
  await page.getByRole('spinbutton', { name: 'Giá (VNĐ)' }).fill('123456');
  await page.getByRole('spinbutton', { name: 'Số lượng' }).dblclick();
  await page.getByRole('spinbutton', { name: 'Số lượng' }).fill('012');
  await page.getByLabel('Danh mục').selectOption('1');
  await page.getByRole('textbox', { name: 'Hãng sản xuất' }).click();
  await page.getByRole('textbox', { name: 'Hãng sản xuất' }).click();
  await page.getByRole('textbox', { name: 'Hãng sản xuất' }).fill('Apple');
  await page.getByRole('button', { name: 'Hình ảnh' }).setInputFiles('iphone-14pro-tim-chinh-thuc.png.webp');
  await page.getByRole('textbox', { name: 'Ghi chú' }).click();
  await page.getByRole('textbox', { name: 'Ghi chú' }).fill('iphone');
  await page.getByRole('button', { name: 'Lưu' }).click();
});