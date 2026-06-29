import { Page } from '@playwright/test';

export async function login(page: Page) {
  await page.goto('http://localhost/QLShopDT_API/app.php');
  await page.getByRole('link', { name: ' Đăng nhập' }).click();
  await page.getByRole('textbox', { name: 'Nhập tên đăng nhập' }).fill('admin');
  await page.getByRole('textbox', { name: 'Nhập mật khẩu' }).fill('123');
  await page.getByRole('button', { name: ' Đăng nhập' }).click();
}

export async function loginAndOpenSanPhamList(page: Page) {
  await login(page);
  await page.getByRole('link', { name: ' Sản phẩm' }).click();
}

export async function loginAndOpenAddForm(page: Page) {
  await loginAndOpenSanPhamList(page);
  await page.getByRole('link', { name: '+ Thêm SP' }).click();
}

export async function loginAndOpenEditForm(page: Page) {
  await loginAndOpenSanPhamList(page);
  await page.getByRole('link', { name: 'Sửa' }).first().click();
}

export async function removeMinAttr(page: Page, inputId: string) {
  await page.evaluate((id) => {
    document.getElementById(id)?.removeAttribute('min');
  }, inputId);
}

export async function removeMaxLengthAttr(page: Page, inputId: string) {
  await page.evaluate((id) => {
    document.getElementById(id)?.removeAttribute('maxlength');
  }, inputId);
}

export async function removeRequiredAttr(page: Page, inputId: string) {
  await page.evaluate((id) => {
    document.getElementById(id)?.removeAttribute('required');
  }, inputId);
}

export function repeatChar(length: number, char: string = 'A'): string {
  return char.repeat(length);
}