import { Page } from '@playwright/test';

const BASE = 'http://localhost/QLShopDT_API/views/nhanvien';

// Session đã đăng nhập sẵn qua storageState — không cần login lại

export async function openNhanVienList(page: Page): Promise<void> {
  await page.goto(`${BASE}/nhanvien.php`);
}

export async function openAddForm(page: Page): Promise<void> {
  await page.goto(`${BASE}/nhanvien_add.php`);
}

export async function openEditForm(page: Page): Promise<void> {
  await openNhanVienList(page);
  await page.getByRole('link', { name: 'Sửa' }).first().click();
}

export { openNhanVienList as loginAndOpenNhanVienList };
export { openAddForm as loginAndOpenAddForm };
export { openEditForm as loginAndOpenEditForm };

export async function removeRequiredAttr(page: Page, inputId: string): Promise<void> {
  await page.evaluate((id) => {
    document.getElementById(id)?.removeAttribute('required');
  }, inputId);
}
